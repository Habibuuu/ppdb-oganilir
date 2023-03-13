<?php

namespace App\Http\Livewire\Siswa\Cetak;

use App\Models\Admin\Pendaftar;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class PendaftaranAfirmasi extends Component
{
    public function render()
    {
        if (isset($_GET['id_siswa'])) {
            $pendaftar = Pendaftar::where('jalur', 'afirmasi')
                ->where('siswa_id', $_GET['id_siswa'])
                ->first();
        } else {
            $pendaftar = Pendaftar::where('jalur', 'afirmasi')
                ->where('siswa_id', Auth::user()->id)
                ->first();
        }

        return view('livewire.siswa.cetak.pendaftaran-afirmasi', [
            'pendaftaran' => $pendaftar
        ]);
    }
}
