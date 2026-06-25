<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Queue extends Model
{
    protected $fillable = [
        'queue_number',
        'poli_id',
        'user_id',
        'nim',
        'nama_pasien',
        'program_studi',
        'keluhan',
        'status',
        'estimated_time',
        'called_at',
        'served_at',
        'done_at',
        'tanggal_antrian',
    ];

    protected $casts = [
        'called_at'       => 'datetime',
        'served_at'       => 'datetime',
        'done_at'         => 'datetime',
        'tanggal_antrian' => 'date',
    ];

    public function poli()
    {
        return $this->belongsTo(Poli::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'waiting'  => 'Menunggu',
            'serving'  => 'Dilayani',
            'done'     => 'Selesai',
            'skipped'  => 'Dilewati',
            default    => 'Tidak Diketahui',
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'waiting'  => 'badge-waiting',
            'serving'  => 'badge-serving',
            'done'     => 'badge-done',
            'skipped'  => 'badge-skipped',
            default    => '',
        };
    }
}
