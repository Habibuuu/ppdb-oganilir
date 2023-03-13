<?php

namespace App\Http\Livewire\Admin\Zonasi;

use Livewire\Component;
use App\Models\Admin\Pendaftar as PendaftarM;
use Livewire\WithPagination;

class Tolak extends Component
{
    public $dataId;

    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public function render()
    {
        // get pendaftaran join zonasi order by zonasi jarak desc
        $zonasi = PendaftarM::join('zonasi', 'zonasi.pendaftaran_id', '=', 'pendaftaran.id')
            ->join('sekolah', 'sekolah.id', '=', 'pendaftaran.sekolah_id')
            ->join('users', 'users.id', '=', 'pendaftaran.siswa_id')
            ->join('users_detail', 'users_detail.user_id', '=', 'users.id')
            ->select(
                'pendaftaran.no_pendaftaran',
                'pendaftaran.id as id_pendaftaran',
                'pendaftaran.status',
                'pendaftaran.jalur',
                'pendaftaran.tanggal_daftar',
                'zonasi.id as id_zonasi',
                'zonasi.jarak',
                'zonasi.latitude_siswa',
                'zonasi.longitude_siswa',
                'sekolah.id as id_sekolah',
                'sekolah.nama_sekolah',
                'sekolah.latitude',
                'sekolah.jenis_sekolah_id',
                'sekolah.latitude',
                'sekolah.longitude',
                'users.id as id_siswa',
                'users_detail.nama_lengkap',
                'users_detail.jenis_kelamin',
                'users_detail.tempat_lahir',
                'users_detail.tanggal_lahir',
                'users_detail.agama',
                'users_detail.alamat',
                'users_detail.no_hp',
                'users_detail.foto'
            )
            ->where('pendaftaran.status', 'ditolak')
            ->where('pendaftaran.jalur', 'zonasi')
            ->where('pendaftaran.sekolah_id', auth()->user()->sekolah_id)
            ->orderBy('zonasi.jarak', 'asc')
            ->paginate(10);
        //

        // dd($zonasi);

        return view(
            'livewire.admin.zonasi.tolak',
            ['data_zonasi' => $zonasi]
        );
    }

    public function dataId($id)
    {
        $this->dataId = $id;
    }

    // return menunggu
    public function returnVerifikasi()
    {
        // dd($this->dataId);
        $zonasi = PendaftarM::find($this->dataId);
        $zonasi->status = 'diverifikasi';
        $zonasi->save();
        session()->flash('success', 'Pendaftaran di kembalikan ke menunggu');
        return redirect()->route('admin.zonasi.tidak.lulus.index');
    }
}
