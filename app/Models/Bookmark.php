<?php

namespace App\Models;

use App\Models\Foto;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Bookmark extends Model
{

    protected $table = 'bookmark_foto';
    protected $primaryKey = 'bookmark_id';
    protected $guarded = [];

    public function foto()
    {
        return $this->belongsTo(Foto::class, 'foto_id');
    }


    use HasFactory;
}
