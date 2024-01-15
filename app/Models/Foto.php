<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Foto extends Model
{

    protected $table = 'foto';
    protected $primaryKey = 'foto_id';
    protected $guarded = [];

    use HasFactory;
}
