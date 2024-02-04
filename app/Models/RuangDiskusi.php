<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RuangDiskusi extends Model
{
    protected $table = 'ruang_diskusi';
    protected $primaryKey = 'ruang_id';
    protected $guarded = [];

    use HasFactory;
}
