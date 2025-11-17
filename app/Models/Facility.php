<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Facility extends Model
{
    use HasFactory;

    protected $table = 'fasilitas';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = false; // Disable timestamps karena tabel tidak punya created_at dan updated_at

    protected $fillable = [
        'name',
        'deskripsi',
        'foto'
    ];

    protected $hidden = [
        // Tidak ada field yang perlu disembunyikan
    ];

    protected $casts = [
        // Tidak ada casting khusus yang diperlukan
    ];
}
