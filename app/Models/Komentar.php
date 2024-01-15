<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Komentar extends Model
{

    protected $table = 'komentar_foto';
    protected $primaryKey = 'komentar_id';
    protected $guarded = [];

    use HasFactory;
}
