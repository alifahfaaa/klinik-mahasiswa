<?php

namespace App\Http\Controllers;

use App\Models\Queue;
use App\Models\Poli;
use Illuminate\Http\Request;

class HistoryController extends Controller
{
    public function index(Request $request)
    {
        $polis   = Poli::where('is_active', true)->get();
        $search  = $request->search;
        $poliId  = $request->poli_id;
        $tanggal = $request->tanggal ?? today()->format('Y-m-d');

        $histories = Queue::whereDate('tanggal_antrian', $tanggal)
            ->where('status', 'done')
            ->when($search, fn ($q) => $q->where('nama_pasien', 'like', "%{$search}%")
                ->orWhere('queue_number', 'like', "%{$search}%"))
            ->when($poliId, fn ($q) => $q->where('poli_id', $poliId))
            ->with('poli')
            ->orderByDesc('done_at')
            ->paginate(15)
            ->withQueryString();

        return view('history.index', compact('histories', 'polis', 'search', 'poliId', 'tanggal'));
    }
}
