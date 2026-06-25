<?php

namespace App\Http\Controllers;

use App\Models\Queue;
use App\Models\Poli;

class DashboardController extends Controller
{
    public function index()
    {
        $today = today();

        $stats = [
            'total'   => Queue::whereDate('tanggal_antrian', $today)->count(),
            'waiting' => Queue::whereDate('tanggal_antrian', $today)->where('status', 'waiting')->count(),
            'serving' => Queue::whereDate('tanggal_antrian', $today)->where('status', 'serving')->count(),
            'done'    => Queue::whereDate('tanggal_antrian', $today)->where('status', 'done')->count(),
        ];

        $polis = Poli::where('is_active', true)->get();

        return view('dashboard.index', compact('stats', 'polis'));
    }
}
