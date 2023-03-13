<?php

namespace App\Http\Livewire\Siswa\Prestasi;

use Livewire\Component;
use App\Models\Admin\Kontrol_jalur;
use App\Models\Admin\Pendaftar;
use App\Models\Admin\Persyaratan;
use App\Models\Admin\Prestasi;
use App\Models\Admin\Sekolah;
use App\Models\Users_detail;
use Livewire\WithFileUploads;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Auth;
use Termwind\Components\Dd;

class Index extends Component
{
    public $jarak, $id_sekolah, $data_persyaratan, $surat_pengantar_sd, $surat_keterangan_bta, $akumulasi_raport, $menang_lomba;
    public $suket_rumah_tahfidz, $sertifikat_kabupaten, $sertifikat_provinsi, $sertifikat_nasional, $sertifikat_internasional;
    public $pesan_persyaratan;
    public $dynamicMapping = [];

    use WithFileUploads;
    public function render()
    {
        // cek data user -> detail user
        $data_profil = Users_detail::where('user_id', Auth::user()->id)->first();
        if ($data_profil == null) {
            $kontrol_jalur_data = Kontrol_jalur::where('jenis_sekolah_id', '3')
                ->where('jalur', 'prestasi')
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
                    ->where('jalur', 'prestasi')
                    ->where('tanggal_buka', '<=', date('Y-m-21'))
                    ->where('tanggal_tutup', '>=', date('Y-m-21'))->get();

                // get data sekolah where jenis sekolah id 3
                $daftar_sekolah = Sekolah::where('jenis_sekolah_id', '3')->where('status_sekolah', 'negeri')->get();

                $tanggal_buka = Kontrol_jalur::where('jenis_sekolah_id', '3')->first();

                // cek status_pendaftaran
                // get pendaftar where jalur prestasi dan siswa_id
                $status_pendaftaran_data = Pendaftar::where('jalur', 'prestasi')
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
            ->where('jalur', 'prestasi')->get();
        // dd($persyaratan);

        $this->data_persyaratan = $persyaratan; // for dynamic variable persyaratan

        $this->emit('reinitializeSelect2');

        return view('livewire.siswa.prestasi.index', [
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
            'surat_keterangan_bta' => 'required',
            'akumulasi_raport' => 'required',
        ], [
            'id_sekolah.required' => 'Sekolah harus diisi',
            'surat_pengantar_sd.required' => 'Surat pengantar sekolah dasar harus diisi',
            'surat_keterangan_bta.required' => 'Surat keterangan bta harus diisi',
            'akumulasi_raport.required' => 'Akumulasi raport harus diisi',
        ]);

        // cek jumlah persyaratan
        $total = count($this->data_persyaratan);
        // dd($total);
        if (count($this->dynamicMapping) != $total) {
            $this->pesan_persyaratan = 'Persyaratan harus diisi semua';
        } else {
            // dd($this->surat_pengantar_sd);
            $nomor_pendaftaran = 'PRESTASI-' . date('y') . '-' . rand(10000, 99999) . '-' . auth()->user()->id;
            $data = new Pendaftar();
            $data->siswa_id = Auth::user()->id;
            $data->no_pendaftaran = $nomor_pendaftaran;
            $data->sekolah_id = $this->id_sekolah;
            $data->jalur = 'prestasi';
            $data->status = 'menunggu';
            $data->tanggal_daftar = date('Y-m-d');
            $data->save();

            $data_prestasi = new Prestasi();
            $data_prestasi->pendaftaran_id = $data->id;

            // // surat_pengantar_sd WAJIB
            $files1 = $this->surat_pengantar_sd;
            $fileNamePengantar_sd =  'surat-pengantar-sd-mi-' . Auth()->user()->id . '.' . $files1->getClientOriginalExtension();
            $destinationPathPengantar_sd = public_path('/storage/data_pendaftaran/');
            // $img = $this->file->store($files1, $destinationPathPengantar_sd);
            $img = Image::make($files1->getRealPath());
            $QuploadImage = $img->resize(1080, 1080, function ($constraint) {
                $constraint->aspectRatio();
            })->save($destinationPathPengantar_sd . $fileNamePengantar_sd, 100);
            $data_prestasi->surat_pengantar_sd = $fileNamePengantar_sd;

            // surat keterangan BTA WAJIB
            $files2 = $this->surat_keterangan_bta;
            $fileNameBta =  'surat-keterangan-bta-' . Auth()->user()->id . '.' . $files2->getClientOriginalExtension();
            $destinationPathBta = public_path('/storage/data_pendaftaran/');
            // $img = $this->file->store($files2, $destinationPathBta);
            $img = Image::make($files2->getRealPath());
            $QuploadImage = $img->resize(1080, 1080, function ($constraint) {
                $constraint->aspectRatio();
            })->save($destinationPathBta . $fileNameBta, 100);
            $data_prestasi->sk_bta = $fileNameBta;

            // ===============>>>>>>>>AKADEMIK<<<<<<<<================
            // akumulasi_raport WAJIB
            $files3 = $this->akumulasi_raport;
            $fileNameRaport =  'akumulasi-raport-' . Auth()->user()->id . '.' . $files3->getClientOriginalExtension();
            $destinationPathRaport = public_path('/storage/data_pendaftaran/');
            // $img = $this->file->store($files3, $destinationPathRaport);
            $img = Image::make($files3->getRealPath());
            $QuploadImage = $img->resize(1080, 1080, function ($constraint) {
                $constraint->aspectRatio();
            })->save($destinationPathRaport . $fileNameRaport, 100);
            $data_prestasi->akumulasi_raport = $fileNameRaport;

            // menang_lomba
            // jika tidak null maka upload
            if ($this->menang_lomba != null) {
                $files4 = $this->menang_lomba;
                $fileNameLomba =  'menang-lomba-' . Auth()->user()->id . '.' . $files4->getClientOriginalExtension();
                $destinationPathLomba = public_path('/storage/data_pendaftaran/');
                // $img = $this->file->store($files4, $destinationPathLomba);
                $img = Image::make($files4->getRealPath());
                $QuploadImage = $img->resize(1080, 1080, function ($constraint) {
                    $constraint->aspectRatio();
                })->save($destinationPathLomba . $fileNameLomba, 100);
                $data_prestasi->menang_lomba = $fileNameLomba;
            }
            // ===============>>>>>>>>TUTUP AKADEMIK<<<<<<<<================

            // ===============>>>>>>>>TAHFIDZ<<<<<<<<================
            // suket_rumah_tahfidz
            // jika tidak null
            if ($this->suket_rumah_tahfidz != null) {
                $files5 = $this->suket_rumah_tahfidz;
                $fileNameTahfidz =  'suket-rumah-tahfidz' . Auth()->user()->id . '.' . $files5->getClientOriginalExtension();
                $destinationPathTahfidz = public_path('/storage/data_pendaftaran/');
                // $img = $this->file->store($files5, $destinationPathTahfidz);
                $img = Image::make($files5->getRealPath());
                $QuploadImage = $img->resize(1080, 1080, function ($constraint) {
                    $constraint->aspectRatio();
                })->save($destinationPathTahfidz . $fileNameTahfidz, 100);
                $data_prestasi->suket_rumah_tahfidz = $fileNameTahfidz;
            }
            // ===============>>>>>>>>TUTUP TAHFIDZ<<<<<<<<================


            // ===============>>>>>>>>NON AKADEMIK<<<<<<<<================
            // sertifikat_kabupaten
            if ($this->sertifikat_kabupaten != null) {
                $files6 = $this->sertifikat_kabupaten;
                $fileNameSkabupaten =  'sertifikat-kabupaten' . Auth()->user()->id . '.' . $files6->getClientOriginalExtension();
                $destinationPathSkabupaten = public_path('/storage/data_pendaftaran/');
                // $img = $this->file->store($files6, $destinationPathSkabupaten);
                $img = Image::make($files6->getRealPath());
                $QuploadImage = $img->resize(1080, 1080, function ($constraint) {
                    $constraint->aspectRatio();
                })->save($destinationPathSkabupaten . $fileNameSkabupaten, 100);
                $data_prestasi->sertifikat_kabupaten = $fileNameSkabupaten;
            }

            // sertifikat_provinsi
            if ($this->sertifikat_provinsi != null) {
                $files7 = $this->sertifikat_provinsi;
                $fileNameSprovinsi =  'sertifikat-provinsi' . Auth()->user()->id . '.' . $files7->getClientOriginalExtension();
                $destinationPathSprovinsi = public_path('/storage/data_pendaftaran/');
                // $img = $this->file->store($files7, $destinationPathSprovinsi);
                $img = Image::make($files7->getRealPath());
                $QuploadImage = $img->resize(1080, 1080, function ($constraint) {
                    $constraint->aspectRatio();
                })->save($destinationPathSprovinsi . $fileNameSprovinsi, 100);
                $data_prestasi->sertifikat_provinsi = $fileNameSprovinsi;
            }

            // sertifikat_nasional
            if ($this->sertifikat_nasional != null) {
                $files8 = $this->sertifikat_nasional;
                $fileNameSnasional =  'sertifikat-nasional' . Auth()->user()->id . '.' . $files8->getClientOriginalExtension();
                $destinationPathSnasional = public_path('/storage/data_pendaftaran/');
                // $img = $this->file->store($files8, $destinationPathSnasional);
                $img = Image::make($files8->getRealPath());
                $QuploadImage = $img->resize(1080, 1080, function ($constraint) {
                    $constraint->aspectRatio();
                })->save($destinationPathSnasional . $fileNameSnasional, 100);
                $data_prestasi->sertifikat_nasional = $fileNameSnasional;
            }

            // sertifikat_internasional
            if ($this->sertifikat_internasional != null) {
                $files9 = $this->sertifikat_internasional;
                $fileNameSinternasional =  'sertifikat-internasional' . Auth()->user()->id . '.' . $files9->getClientOriginalExtension();
                $destinationPathSinternasional = public_path('/storage/data_pendaftaran/');
                // $img = $this->file->store($files9, $destinationPathSinternasional);
                $img = Image::make($files9->getRealPath());
                $QuploadImage = $img->resize(1080, 1080, function ($constraint) {
                    $constraint->aspectRatio();
                })->save($destinationPathSinternasional . $fileNameSinternasional, 100);
                $data_prestasi->sertifikat_internasional = $fileNameSinternasional;
            }
            // ===============>>>>>>>>TUTUP NON AKADEMIK<<<<<<<<================

            // dd($this->data_persyaratan);
            foreach ($this->data_persyaratan as $key) {
                $files = $this->dynamicMapping[$key->nama_surat];
                $fileName =  'prestasi-' . $key->nama_surat . '-' . Auth()->user()->id . '.' . $files->getClientOriginalExtension();
                // ORIGINAL
                $destinationPath = public_path('/storage/data_pendaftaran/');
                // $img = $this->file->store($files, $destinationPath);
                $img = Image::make($files->getRealPath());
                $QuploadImage = $img->resize(1080, 1080, function ($constraint) {
                    $constraint->aspectRatio();
                })->save($destinationPath . $fileName, 100);
                $aww[] = $fileName;
                $data_prestasi->file = json_encode($aww);
            }
            $data_prestasi->save();
            session()->flash('success', 'Pendaftaran Anda Berhasil Dikirim');
            return redirect()->route('siswa.prestasi.index');
        }
    }
}
