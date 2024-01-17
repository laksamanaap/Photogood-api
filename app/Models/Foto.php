<?php

namespace App\Models;

use App\Models\User;
use App\Models\Album;
use App\Models\Member;
use App\Models\Download;
use App\Models\Komentar;
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

    public function comment()
    {
        return $this->hasMany(Komentar::class, 'foto_id');
    }

    public function download()
    {
        return $this->hasMany(Download::class, 'foto_id');
    }

    public function like()
    {
        return $this->hasMany(Like::class, 'foto_id');
    }

    use HasFactory;
}
