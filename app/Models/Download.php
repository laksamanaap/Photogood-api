<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Download extends Model
{

    protected $table = 'download_foto';
    protected $primaryKey = 'download_id';
    protected $guarded = [];

    use HasFactory;
}
