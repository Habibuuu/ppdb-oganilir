<?php

namespace App\Http\Livewire\Siswa\Mutasi;

use App\Models\Admin\Kontrol_jalur;
use App\Models\Admin\Mutasi;
use App\Models\Admin\Pendaftar;
use App\Models\Admin\Persyaratan;
use App\Models\Admin\Sekolah;
use App\Models\Users_detail;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Intervention\Image\Facades\Image;
use Livewire\WithFileUploads;


class Index extends Component
{
    public $jarak, $latitude_siswa, $longitude_siswa, $id_sekolah, $data_persyaratan, $surat_pengantar_sd;
    public $dynamicMapping = [];
    public $mutasi_ortu;
    public $pesan_persyaratan;
    use WithFileUploads;
    public function render()
    {
        // cek data user -> detail user
        $data_profil = Users_detail::where('user_id', Auth::user()->id)->first();
        if ($data_profil == null) {
            $kontrol_jalur_data = Kontrol_jalur::where('jenis_sekolah_id', '3')
                ->where('jalur', 'zonasi')
                ->where('tanggal_buka', '<=', date('Y-m-21'))
                ->where('tanggal_tutup', '>=', date('Y-m-21'))->get();
            $daftar_sekolah = [];
            $tanggal_buka = [];
            $status_pendaftaran = [];
            $persyaratan = [];
            session()->flash('error', 'Lengkapi data profil dahulu');
            redirect()->route('siswa.profil.index');
        } else {
            // get d
            // get data kontrol jalur
            if (Auth::user()->sekolah->jenis_sekolah_id == null) {
                $kontrol_jalur = Kontrol_jalur::where('jenis_sekolah_id', '1')->first();
            } else if (Auth::user()->sekolah->jenis_sekolah_id == 1) {
                $kontrol_jalur = Kontrol_jalur::where('jenis_sekolah_id', '2')->first();
            } else if (Auth::user()->sekolah->jenis_sekolah_id == 2) {
                $kontrol_jalur_data = Kontrol_jalur::where('jenis_sekolah_id', '3')
                    ->where('jalur', 'mutasi')
                    ->where('tanggal_buka', '<=', date('Y-m-21'))
                    ->where('tanggal_tutup', '>=', date('Y-m-21'))->get();

                // get data sekolah where jenis sekolah id 3
                $daftar_sekolah = Sekolah::where('jenis_sekolah_id', '3')->where('status_sekolah', 'negeri')->get();

                $tanggal_buka = Kontrol_jalur::where('jenis_sekolah_id', '3')->first();

                // cek status_pendaftaran
                // get pendaftar where jalur mutasi dan siswa_id
                $status_pendaftaran_data = Pendaftar::where('jalur', 'mutasi')
                    ->where('siswa_id', Auth::user()->id)
                    ->first();


                // cek sudah daftar atau belum
                if ($status_pendaftaran_data != null) {
                    $status_pendaftaran = 'sudah';
                } else {
                    $status_pendaftaran = 'belum';
                }
            }
        }

        // dd($kontrol_jalur_data);
        $persyaratan = Persyaratan::where('sekolah_id', $this->id_sekolah)
            ->where('jalur', 'mutasi')->get();
        // dd($persyaratan);

        $this->data_persyaratan = $persyaratan; // for dynamic variable persyaratan

        $this->emit('reinitializeSelect2');

        if ($this->id_sekolah and $this->latitude_siswa and $this->longitude_siswa) {
            // get data sekolah
            $sekolah = Sekolah::where('id', $this->id_sekolah)->first();
            // dd($sekolah->latitude,$sekolah->longitude);

            $latitude_sekolah = $sekolah->latitude;
            $longitude_sekolah = $sekolah->longitude;
            $latitude_siswa = $this->latitude_siswa;
            $longitude_siswa = $this->longitude_siswa;

            $theta = $longitude_sekolah - $longitude_siswa;
            $miles = (sin(deg2rad($latitude_sekolah)) * sin(deg2rad($latitude_siswa))) + (cos(deg2rad($latitude_sekolah)) * cos(deg2rad($latitude_siswa)) * cos(deg2rad($theta)));
            $miles = acos($miles);
            $miles = rad2deg($miles);
            $miles = $miles * 60 * 1.1515;
            $kilometers = $miles * 1.609344;
            $meters = $kilometers * 1000;
            $this->jarak = $meters;

            // $file_upload =
            // get table persyaratan
        }

        return view('livewire.siswa.mutasi.index', [
            'kontrol_jalur' => $kontrol_jalur_data,
            'daftar_sekolah' => $daftar_sekolah,
            'tanggal_buka' => $tanggal_buka,
            'status_pendaftaran' => $status_pendaftaran,
            'persyaratan' => $persyaratan,
        ]);
    }

    // store
    public function store()
    {
        $this->validate([
            'id_sekolah' => 'required',
            'surat_pengantar_sd' => 'required',
            'mutasi_ortu' => 'required',
        ], [
            'id_sekolah.required' => 'Sekolah tujuan tidak boleh kosong',
            'surat_pengantar_sd.required' => 'File tidak boleh kosong',
            'mutasi_ortu.required' => 'File tidak boleh kosong',
        ]);

        // cek jumlah persyaratan
        $total = count($this->data_persyaratan);
        if (count($this->dynamicMapping) != $total) {
            $this->pesan_persyaratan = 'Persyaratan harus diisi semua';
        } else {
            $nomor_pendaftaran = 'MUTASI-' . date('y') . '-' . rand(10000, 99999) . '-' . auth()->user()->id;
            $data = new Pendaftar();
            $data->siswa_id = Auth::user()->id;
            $data->no_pendaftaran = $nomor_pendaftaran;
            $data->sekolah_id = $this->id_sekolah;
            $data->jalur = 'mutasi';
            $data->status = 'menunggu';
            $data->tanggal_daftar = date('Y-m-d');
            $data->save();


            $data_mutasi = new Mutasi();
            $data_mutasi->pendaftaran_id = $data->id;
            $files = $this->mutasi_ortu;
            $fileNameMutasi =  'mutasi-orangtua-' . Auth()->user()->id . '.' . $files->getClientOriginalExtension();

            // surat pengantar sd
            $filesPengantar = $this->surat_pengantar_sd;
            $fileNameSdmi =  'afirmasi-surat_sdmi-' . Auth()->user()->id . '.' . $filesPengantar->getClientOriginalExtension();
            // ORIGINAL
            $destinationPathSdmi = public_path('/storage/data_pendaftaran/');
            $imgSdmi = Image::make($filesPengantar->getRealPath());
            $QuploadImage = $imgSdmi->resize(1080, 1080, function ($constraint) {
                $constraint->aspectRatio();
            })->save($destinationPathSdmi . $fileNameSdmi, 100);
            $data_mutasi->surat_pengantar_sd = $fileNameSdmi;

            // ORIGINAL
            $destinationPathMutasi = public_path('/storage/data_pendaftaran/');

            $img = Image::make($files->getRealPath());
            $QuploadImage = $img->resize(1080, 1080, function ($constraint) {
                $constraint->aspectRatio();
            })->save($destinationPathMutasi . $fileNameMutasi, 100);
            $data_mutasi->sk_mutasi_orang_tua = $fileNameMutasi;

            // dd($this->data_persyaratan);
            foreach ($this->data_persyaratan as $key) {
                $files = $this->dynamicMapping[$key->nama_surat];
                $fileName =  'mutasi-' . $key->nama_surat . '-' . Auth()->user()->id . '.' . $files->getClientOriginalExtension();

                // ORIGINAL
                $destinationPath = public_path('/storage/data_pendaftaran/');
                $img = Image::make($files->getRealPath());
                $QuploadImage = $img->resize(1080, 1080, function ($constraint) {
                    $constraint->aspectRatio();
                })->save($destinationPath . $fileName, 100);
                $aww[] = $fileName;
                $data_mutasi->file = json_encode($aww);
            }
            $data_mutasi->save();
            session()->flash('success', 'Pendaftaran Anda Berhasil Dikirim');
            return redirect()->route('siswa.mutasi.index');
        }
    }
}
