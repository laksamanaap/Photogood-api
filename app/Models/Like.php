<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    protected $table = 'like_foto';
    protected $primaryKey = 'like_id';
    protected $guarded = [];

    public function foto()
    {
        return $this->belongsTo(Foto::class, 'foto_id');
    }

    use HasFactory;
}
