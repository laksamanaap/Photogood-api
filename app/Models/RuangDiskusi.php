<?php

namespace App\Models;

use App\Models\User;
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

    public function member()
    {
        return $this->hasMany(AnggotaDiskusi::class,'ruang_id');
    }

     public function lastMessage()
    {
        return $this->hasOne(PesanDiskusi::class, 'ruang_id')->latest();
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    use HasFactory;
}
