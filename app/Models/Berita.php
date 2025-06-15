<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Berita extends Model
{
    use HasFactory;

    protected $fillable = [
        'judul',
        'konten',
        'gambar',
        'status',
        'user_id',
    ];

    // Relasi: satu berita dimiliki oleh satu user
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}