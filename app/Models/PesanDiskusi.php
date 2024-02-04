<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PesanDiskusi extends Model
{

    protected $table = 'pesan_diskusi';
    protected $primaryKey = 'pesan_id';
    protected $guarded = [];

    use HasFactory;
}
