<?php

namespace App\Models;

use App\Models\User;
use App\Models\Member;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PesanDiskusi extends Model
{

    protected $table = 'pesan_diskusi';
    protected $primaryKey = 'pesan_id';
    protected $guarded = [];

    public function room()
    {
        return $this->belongsTo(RuangDiskusi::class, 'ruang_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id');
    }

    use HasFactory;
}
