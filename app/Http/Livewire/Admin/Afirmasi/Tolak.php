<?php

namespace App\Http\Livewire\Admin\Afirmasi;

use Livewire\Component;
use App\Models\Admin\Pendaftar as PendaftarM;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;

class Tolak extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $dataId;

    public function render()
    {
        $pendaftar = PendaftarM::join('users', 'users.id', '=', 'pendaftaran.siswa_id')
            ->join('users_detail', 'users_detail.user_id', '=', 'users.id')
            ->join('sekolah', 'sekolah.id', '=', 'pendaftaran.sekolah_id')
            ->select('pendaftaran.id', 'pendaftaran.no_pendaftaran', 'pendaftaran.status', 'pendaftaran.jalur', 'pendaftaran.tanggal_daftar', 'pendaftaran.sekolah_id', 'pendaftaran.siswa_id', 'pendaftaran.created_at', 'pendaftaran.updated_at', 'users.id as id_siswa', 'users_detail.nama_lengkap', 'users_detail.jenis_kelamin', 'users_detail.tempat_lahir', 'users_detail.tanggal_lahir', 'users_detail.agama', 'users_detail.alamat', 'users_detail.no_hp', 'users_detail.foto', 'sekolah.id as id_sekolah', 'sekolah.nama_sekolah')
            ->where('pendaftaran.jalur', 'afirmasi')
            ->where('pendaftaran.status', 'ditolak')
            ->where('pendaftaran.sekolah_id', Auth::user()->sekolah_id)
            ->paginate(10);

        return view('livewire.admin.afirmasi.tolak',['data_afirmasi' => $pendaftar]);
    }

    public function dataId($id)
    {
        $this->dataId = $id;
    }

    // return menunggu
    public function returnVerifikasi()
    {
        $afirmasi = PendaftarM::find($this->dataId);
        $afirmasi->status = 'diverifikasi';
        $afirmasi->save();
        session()->flash('success', 'Pendaftaran di kembalikan ke fase verifikasi');
        return redirect()->route('admin.afirmasi.lulus.index');
    }
}
