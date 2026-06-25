<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Poli extends Model
{
    protected $fillable = ['kode', 'nama', 'lokasi_ruang', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function queues()
    {
        return $this->hasMany(Queue::class);
    }

    public function queueCounter()
    {
        return $this->hasMany(QueueCounter::class);
    }

    /** Antrian hari ini */
    public function todayQueues()
    {
        return $this->queues()->whereDate('tanggal_antrian', today());
    }

    public function waitingCount(): int
    {
        return $this->todayQueues()->where('status', 'waiting')->count();
    }
}
