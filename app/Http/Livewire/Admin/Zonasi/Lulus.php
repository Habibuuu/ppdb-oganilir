<?php

namespace App\Http\Livewire\Admin\Zonasi;

use Livewire\Component;
use App\Models\Admin\Pendaftar as PendaftarM;
use Livewire\WithPagination;
use App\Models\Admin\DayaTampung;

class Lulus extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $file_persyaratan,$dataId;
    public function render()
    {
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
            ->where('pendaftaran.status', 'diterima')
            ->where('pendaftaran.sekolah_id', auth()->user()->sekolah_id)
            ->orderBy('zonasi.jarak', 'asc')
            ->paginate(10);

        // get data from daya tampung
        $daya_tampung = DayaTampung::where('sekolah_id', auth()->user()->sekolah_id)->first();
        // $zonasi = $daya_tampung->daya_tampung * 50 / 100;

        return view(
            'livewire.admin.zonasi.lulus',['data_zonasi' => $zonasi, 'daya_tampung' => $daya_tampung]
        );
    }

    public function dataId($id)
    {
        $this->dataId = $id;
    }

    // return menunggu
    public function returnVerifikasi()
    {
        $zonasi = PendaftarM::find($this->dataId);
        $zonasi->status = 'diverifikasi';
        $zonasi->save();
        session()->flash('success', 'Pendaftaran di kembalikan ke fase verifikasi');
        return redirect()->route('admin.prestasi.lulus.index');
    }

    // return menunggu
    public function returnMenunggu($id)
    {
        $zonasi = PendaftarM::find($id);
        $zonasi->status = 'diverifikasi';
        $zonasi->save();
        session()->flash('success', 'Pendaftaran di kembalikan ke menunggu');
        return redirect()->route('admin.zonasi.lulus.index');
    }

    // lihat lampiran
    public function lihatLampiran($id)
    {
        $zonasi = PendaftarM::find($id);
        // dd($zonasi->zonasi->file);
        $this->file_persyaratan = $zonasi->zonasi->file;
    }

}
