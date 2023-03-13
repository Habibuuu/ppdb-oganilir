<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sekolah extends Model
{
    use HasFactory;

    protected $table = 'sekolah';

    public function sekolah_area()
    {
        return $this->hasMany(Sekolah_area::class, 'sekolah_id', 'id');
    }

    // join to kecamatan where sekola kecamatan id
    public function kecamatan()
    {
        return $this->hasMany(Kecamatan::class, 'kecamatan_id', 'id');
    }

}
