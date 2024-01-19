<?php

namespace App\Models;

use App\Models\Foto;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Kategori extends Model
{

    protected $table = 'kategori_foto';
    protected $primaryKey = 'kategori_id';
    protected $guarded = [];

    // public function foto()
    // {
    //     return $this->hasMany(Foto::class, 'kategori_id');
    // }

    use HasFactory;
}
