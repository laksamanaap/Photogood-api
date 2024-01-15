<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Album extends Model
{

    protected $table = 'album_foto';
    protected $primaryKey = 'album_id';
    protected $guarded = [];

    use HasFactory;
}
