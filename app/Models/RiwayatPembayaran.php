<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiwayatPembayaran extends Model
{

    protected $table = 'riwayat_pembayaran';
    protected $primaryKey = 'riwayat_id';
    protected $guarded = [];

    use HasFactory;
}
