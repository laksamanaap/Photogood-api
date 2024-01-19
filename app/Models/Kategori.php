<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{

    protected $table = 'kategori_foto';
    protected $primaryKey = 'foto_id';
    protected $guarded = [];

    use HasFactory;
}
