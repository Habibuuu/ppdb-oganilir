<?php

namespace App\Http\Livewire\Admin\Zonasi;

use Livewire\Component;
use App\Models\Admin\Pendaftar as PendaftarM;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class Pendaftar extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $file_persyaratan, $dataId, $status_data;
    public function render()
    {
        // dd(Auth::user()->sekolah_id);
        // get data Pendaftar where jalur zonasi relasi to zonasi where status menunggu
        $zonasi = PendaftarM::join('zonasi', 'zonasi.pendaftaran_id', '=', 'pendaftaran.id')
            ->join('sekolah', 'sekolah.id', '=', 'pendaftaran.sekolah_id')
            ->join('users', 'users.id', '=', 'pendaftaran.siswa_id')
            ->join('users_detail', 'users_detail.user_id', '=', 'users.id')
            ->select('pendaftaran.no_pendaftaran', 'pendaftaran.id as id_pendaftaran','pendaftaran.status','pendaftaran.jalur', 'pendaftaran.tanggal_daftar'
            ,'zonasi.id as id_zonasi','zonasi.jarak','zonasi.latitude_siswa','zonasi.longitude_siswa','zonasi.file_pendukung_jarak','zonasi.usia_kk'
            ,'sekolah.id as id_sekolah','sekolah.nama_sekolah','sekolah.latitude','sekolah.jenis_sekolah_id','sekolah.latitude','sekolah.longitude'
            ,'users.id as id_siswa',
            'users_detail.nama_lengkap','users_detail.jenis_kelamin','users_detail.tempat_lahir','users_detail.tanggal_lahir','users_detail.agama','users_detail.alamat','users_detail.no_hp','users_detail.foto')
            // ->where('pendaftaran.status', 'menunggu')
            ->where('pendaftaran.sekolah_id', auth()->user()->sekolah_id)
            ->orderBy('zonasi.jarak', 'asc')
            ->paginate(10);

            // dd($zonasi);
        return view(
            'livewire.admin.zonasi.pendaftar',['data_zonasi' => $zonasi]
        );
    }

    public function dataId($id)
    {
        $this->dataId = $id;
    }

    public function showToastr($icon, $text, $title)
    {
        $this->emit('swal:alert', [
            'icon' => $icon,
            'title' => $title,
            'text' => $text,
            'timeout'   => 1000
        ]);
    }

     // make verifikasi diterima zonasi
     public function verifikasi()
     {
         $zonasi = PendaftarM::find($this->dataId);
         $zonasi->status = 'diverifikasi';
         $zonasi->save();
         session()->flash('success', 'Pendaftaran Diverifikasi');
         return redirect()->route('admin.zonasi.index');
     }

    // make verifikasi diterima zonasi
    public function verifikasiDitolak()
    {
        $zonasi = PendaftarM::find($this->dataId);
        $zonasi->status = 'ditolak';
        $zonasi->save();
        session()->flash('success', 'Pendaftaran Ditolak');
        return redirect()->route('admin.zonasi.index');
    }

    // lihat lampiran
    public function lihatLampiran($id)
    {
        $zonasi = PendaftarM::find($id);
        // dd($zonasi->zonasi->file);
        $this->file_persyaratan = $zonasi->zonasi->file;
    }
}
