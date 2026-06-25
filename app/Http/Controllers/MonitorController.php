<?php

namespace App\Http\Controllers;

use App\Models\Poli;
use App\Models\Queue;

class MonitorController extends Controller
{
    public function index()
    {
        $today = today();

        $serving = Queue::whereDate('tanggal_antrian', $today)
            ->where('status', 'serving')
            ->with('poli')
            ->first();

        $nextQueue = Queue::whereDate('tanggal_antrian', $today)
            ->where('status', 'waiting')
            ->with('poli')
            ->orderBy('queue_number')
            ->first();

        $totalWaiting = Queue::whereDate('tanggal_antrian', $today)
            ->where('status', 'waiting')
            ->count();

        $activeQueues = Queue::whereDate('tanggal_antrian', $today)
            ->whereIn('status', ['waiting', 'serving', 'done'])
            ->with('poli')
            ->orderByRaw("FIELD(status, 'serving', 'waiting', 'done')")
            ->orderBy('queue_number')
            ->limit(20)
            ->get();

        $poliStats = Poli::where('is_active', true)->get()->map(function ($poli) use ($today) {
            return [
                'nama'    => $poli->nama,
                'waiting' => Queue::whereDate('tanggal_antrian', $today)
                    ->where('poli_id', $poli->id)
                    ->where('status', 'waiting')
                    ->count(),
            ];
        });

        return view('monitor.index', compact(
            'serving', 'nextQueue', 'totalWaiting', 'activeQueues', 'poliStats'
        ));
    }
}
