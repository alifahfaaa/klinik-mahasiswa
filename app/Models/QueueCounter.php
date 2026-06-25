<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QueueCounter extends Model
{
    protected $fillable = ['poli_id', 'tanggal', 'counter'];

    protected $casts = ['tanggal' => 'date'];

    public function poli()
    {
        return $this->belongsTo(Poli::class);
    }
}
