<?php

namespace App\Http\Livewire\Siswa\Zonasi;

use Livewire\Component;
use App\Models\Admin\Kontrol_jalur;
use App\Models\Admin\Sekolah;
use App\Models\Admin\Zonasi;
use Illuminate\Support\Facades\Auth;
use App\Models\Admin\Persyaratan;
use App\Models\Admin\Pendaftar;
use App\Models\User;
use App\Models\Users_detail;
use Livewire\WithFileUploads;
use Intervention\Image\Facades\Image;


class Index extends Component
{
    use WithFileUploads;
    public $id_sekolah, $latitude_siswa, $longitude_siswa, $jarak, $file, $usia_kk, $surat_pengantar_sd, $data_aw, $file_kartu_keluarga;
    public $data_persyaratan;
    public $pesan_persyaratan;
    public $dynamicMapping = [];

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
            // get data kontrol jalur
            if (Auth::user()->sekolah->jenis_sekolah_id == null) {
                $kontrol_jalur = Kontrol_jalur::where('jenis_sekolah_id', '1')->first();
            } else if (Auth::user()->sekolah->jenis_sekolah_id == 1) {
                $kontrol_jalur = Kontrol_jalur::where('jenis_sekolah_id', '2')->first();
            } else if (Auth::user()->sekolah->jenis_sekolah_id == 2) {
                $kontrol_jalur_data = Kontrol_jalur::where('jenis_sekolah_id', '3')
                    ->where('jalur', 'zonasi')
                    ->where('tanggal_buka', '<=', date('Y-m-21'))
                    ->where('tanggal_tutup', '>=', date('Y-m-21'))->get();

                // get data sekolah where jenis sekolah id 3
                $daftar_sekolah = Sekolah::where('jenis_sekolah_id', '3')->where('status_sekolah', 'negeri')->get();

                $tanggal_buka = Kontrol_jalur::where('jenis_sekolah_id', '3')->first();

                // cek status_pendaftaran
                // get pendaftar where jalur zonasi dan siswa_id
                $status_pendaftaran_data = Pendaftar::where('jalur', 'zonasi')
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
        $persyaratan = Persyaratan::where('sekolah_id', $this->id_sekolah)
            ->where('jalur', 'zonasi')->get();
        $this->data_persyaratan = $persyaratan; // for dynamic variable persyaratan

        $this->emit('reinitializeSelect2');


        $user = User::where('id', Auth::user()->id)->first();
        $this->latitude_siswa =  $user->latitude;
        $this->longitude_siswa =  $user->longitude;

        if ($this->id_sekolah) {
            // get data sekolah
            $sekolah = Sekolah::where('id', $this->id_sekolah)->first();
            // dd($sekolah->latitude,$sekolah->longitude);

            // ambil latitude dan longitude siswa
            // get data user
            $user = User::where('id', Auth::user()->id)->first();

            $latitude_sekolah = $sekolah->latitude;
            $longitude_sekolah = $sekolah->longitude;
            $latitude_siswa = $user->latitude;
            $longitude_siswa = $user->longitude;

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

        return view('livewire.siswa.zonasi.index', [
            'kontrol_jalur' => $kontrol_jalur_data,
            'tanggal_buka'  => $tanggal_buka,
            'daftar_sekolah' => $daftar_sekolah,
            'persyaratan' => $persyaratan,
            'status_pendaftaran' => $status_pendaftaran,
        ]);
    }

    // make store
    public function store()
    {
        $this->validate([
            'id_sekolah' => 'required',
            'usia_kk' => 'required',
            'file_kartu_keluarga' => 'required:file|mimes:jpg,jpeg,png,pdf|max:2048',
            'surat_pengantar_sd' => 'required:file|mimes:jpg,jpeg,png,pdf|max:2048',
            'latitude_siswa' => 'required',
            'longitude_siswa' => 'required',
        ], [
            'id_sekolah.required' => 'Sekolah harus diisi',
            'usia_kk.required' => 'File Usia KK harus diisi',
            'file_kartu_keluarga.required' => 'File Kartu Keluarga harus diisi',
            'surat_pengantar_sd.required' => 'File Pendukung Jarak harus diisi',
            'surat_pengantar_sd.mimes' => 'File Pendukung Jarak harus berformat PDF',
            'surat_pengantar_sd.max' => 'File Pendukung Jarak maksimal 2MB',
            'latitude_siswa.required' => 'Latitude harus diisi',
            'longitude_siswa.required' => 'Longitude harus diisi',
        ]);

        // dd($this->dynamicMapping);

        // cek jumlah persyaratan
        $total = count($this->data_persyaratan);
        if (count($this->dynamicMapping) != $total) {
            $this->pesan_persyaratan = 'Persyaratan harus diisi semua';
        } else {

            $nomor_pendaftaran = 'ZONASI-' . date('y') . '-' . rand(10000, 99999) . '-' . auth()->user()->id;
            $data = new Pendaftar();
            $data->siswa_id = Auth::user()->id;
            $data->no_pendaftaran = $nomor_pendaftaran;
            $data->sekolah_id = $this->id_sekolah;
            $data->jalur = 'zonasi';
            $data->status = 'menunggu';
            $data->tanggal_daftar = date('Y-m-d');
            $data->save();


            $data_zonasi = new Zonasi();
            $data_zonasi->pendaftaran_id = $data->id;
            $data_zonasi->latitude_siswa = $this->latitude_siswa;
            $data_zonasi->longitude_siswa = $this->longitude_siswa;
            $data_zonasi->jarak = $this->jarak;
            $data_zonasi->usia_kk = $this->usia_kk;

            $files = $this->surat_pengantar_sd;
            $fileNamePendukung =  'zonasi-surat_pengantar_sd-' . Auth()->user()->id . '.' . $files->getClientOriginalExtension();
            // ORIGINAL
            $destinationPathPendukung = public_path('/storage/data_pendaftaran/');
            $img = Image::make($files->getRealPath());
            $QuploadImage = $img->resize(1080, 1080, function ($constraint) {
                $constraint->aspectRatio();
            })->save($destinationPathPendukung . $fileNamePendukung, 100);
            $data_zonasi->surat_pengantar_sd = $fileNamePendukung;

            $filesKK = $this->file_kartu_keluarga;
            $fileNameKK =  'zonasi-file_kartu_keluarga-' . Auth()->user()->id . '.' . $filesKK->getClientOriginalExtension();
            // ORIGINAL
            $destinationPathPendukung = public_path('/storage/data_pendaftaran/');
            $img = Image::make($filesKK->getRealPath());
            $QuploadImage = $img->resize(1080, 1080, function ($constraint) {
                $constraint->aspectRatio();
            })->save($destinationPathPendukung . $fileNameKK, 100);
            $data_zonasi->file_kk = $fileNameKK;


            foreach ($this->data_persyaratan as $key) {
                $files = $this->dynamicMapping[$key->nama_surat];
                $fileName =  'zonasi-' . $key->nama_surat . '-' . Auth()->user()->id . '.' . $files->getClientOriginalExtension();

                // ORIGINAL
                $destinationPath = public_path('/storage/data_pendaftaran/');
                $img = Image::make($files->getRealPath());
                $QuploadImage = $img->resize(1080, 1080, function ($constraint) {
                    $constraint->aspectRatio();
                })->save($destinationPath . $fileName, 100);
                $aww[] = $fileName;
                $data_zonasi->file = json_encode($aww);
            }
            $data_zonasi->save();
            session()->flash('success', 'Pendaftaran Anda Berhasil Dikirim');
            return redirect()->route('siswa.zonasi.index');
        }
    }
}
