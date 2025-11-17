<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agenda extends Model
{
    use HasFactory;

    protected $table = 'agenda';

    protected $fillable = [
        'judul',
        'deskripsi',
        'tanggal',
        'lokasi',
        'status'
    ];

    protected $casts = [
        'tanggal' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public $timestamps = true;

    /**
     * Get formatted date for display
     */
    public function getFormattedDateAttribute()
    {
        return $this->tanggal->locale('id')->isoFormat('D MMMM YYYY');
    }

    /**
     * Get status badge color
     */
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'Selesai' => 'success',
            'Berlangsung' => 'primary',
            'Akan Datang' => 'warning',
            default => 'secondary'
        };
    }

    /**
     * Scope untuk agenda yang akan datang
     */
    public function scopeUpcoming($query)
    {
        return $query->where('tanggal', '>=', now()->toDateString())
                    ->orderBy('tanggal', 'asc');
    }

    /**
     * Scope untuk agenda yang sudah lewat
     */
    public function scopePast($query)
    {
        return $query->where('tanggal', '<', now()->toDateString())
                    ->orderBy('tanggal', 'desc');
    }

    /**
     * Scope untuk agenda berdasarkan status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }
}
