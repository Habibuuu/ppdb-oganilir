<?php

namespace App\Http\Livewire\Siswa\Dashboard;

use App\Models\Admin\Pemberkasan_afirmasi;
use App\Models\Admin\Pemberkasan_mutasi;
use App\Models\Admin\Pemberkasan_zonasi;
use App\Models\Admin\Pengumuman;
use Livewire\Component;

class Index extends Component
{
    public function render()
    {
        // get data pemberkasan afirmasi
        $pemberkasan_afirmasi = Pemberkasan_afirmasi::get();
        $pemberkasan_mutasi = Pemberkasan_mutasi::get();
        $pemberkasan_zonasi = Pemberkasan_zonasi::get();
        // get pengumuman where status aktif dan level 1 atau 3
        $pengumuman = Pengumuman::where('status', 'aktif')->where('level', '1')->orWhere('level', '3')->get();

        return view('livewire.siswa.dashboard.index',[
            'pemberkasan_afirmasi' => $pemberkasan_afirmasi,
            'pemberkasan_mutasi' => $pemberkasan_mutasi,
            'pemberkasan_zonasi' => $pemberkasan_zonasi,
            'pengumuman'    => $pengumuman
        ]);
    }
}
