<?php

namespace App\Models;

use App\Models\User;
use App\Models\Album;
use App\Models\Member;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Foto extends Model
{

    protected $table = 'foto';
    protected $primaryKey = 'foto_id';
    protected $guarded = [];

    public function album()
    {
        return $this->belongsTo(Album::class,'album_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }

    public function member()
    {
        return $this->belongsTo(Member::class,'member_id');
    }

    use HasFactory;
}
