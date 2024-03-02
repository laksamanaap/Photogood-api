<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RiwayatPembayaran extends Model
{

    protected $table = 'riwayat_pembayaran';
    protected $primaryKey = 'riwayat_id';
    protected $guarded = [];
    public $incrementing = false;

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }


    use HasFactory;
}
