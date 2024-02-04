<?php

namespace App\Models;

use App\Models\PesanDiskusi;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RuangDiskusi extends Model
{
    protected $table = 'ruang_diskusi';
    protected $primaryKey = 'ruang_id';
    protected $guarded = [];
    public $incrementing = false;

    public function messages()
    {
        return $this->hasMany(PesanDiskusi::class, 'ruang_id');
    }

    use HasFactory;
}
