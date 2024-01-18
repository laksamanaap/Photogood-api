<?php

namespace App\Models;

use App\Models\Bookmark;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Album extends Model
{

    protected $table = 'album_foto';
    protected $primaryKey = 'album_id';
    protected $guarded = [];

    public function bookmark_fotos()
    {
        return $this->hasMany(Bookmark::class, 'album_id');
    }

    use HasFactory;
}
