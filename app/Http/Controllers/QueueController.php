<?php

namespace App\Http\Controllers;

use App\Models\Poli;
use App\Models\Queue;
use App\Models\QueueCounter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class QueueController extends Controller
{
    // ─── Mahasiswa: Ambil Antrian ─────────────────────────────────────────────

    public function take()
    {
        $polis = Poli::where('is_active', true)->get();
        return view('queue.take', compact('polis'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nim'           => 'required|string|max:20',
            'nama_pasien'   => 'required|string|max:100',
            'program_studi' => 'required|string|max:100',
            'poli_id'       => 'required|exists:polis,id',
            'keluhan'       => 'required|string|max:500',
        ], [
            'nim.required'           => 'NIM wajib diisi.',
            'nama_pasien.required'   => 'Nama lengkap wajib diisi.',
            'program_studi.required' => 'Program studi wajib diisi.',
            'poli_id.required'       => 'Tujuan poli wajib dipilih.',
            'keluhan.required'       => 'Keluhan wajib diisi.',
        ]);

        $poli  = Poli::findOrFail($request->poli_id);
        $today = today();

        $queue = DB::transaction(function () use ($poli, $today, $request) {
            // Ambil / buat counter harian
            $counter = QueueCounter::lockForUpdate()->firstOrCreate(
                ['poli_id' => $poli->id, 'tanggal' => $today],
                ['counter' => 0]
            );
            $counter->increment('counter');
            $counter->refresh();

            $number       = $poli->kode . '-' . str_pad($counter->counter, 3, '0', STR_PAD_LEFT);
            $waitingCount = Queue::whereDate('tanggal_antrian', $today)
                ->where('poli_id', $poli->id)
                ->whereIn('status', ['waiting', 'serving'])
                ->count();

            // Estimasi 10 menit per pasien
            $estimated = now()->addMinutes($waitingCount * 10)->format('H:i');

            return Queue::create([
                'queue_number'   => $number,
                'poli_id'        => $poli->id,
                'user_id'        => Auth::id(),
                'nim'            => $request->nim,
                'nama_pasien'    => $request->nama_pasien,
                'program_studi'  => $request->program_studi,
                'keluhan'        => $request->keluhan,
                'status'         => 'waiting',
                'estimated_time' => $estimated,
                'tanggal_antrian'=> $today,
            ]);
        });

        return redirect()->route('queue.take')->with('success', $queue->id);
    }

    // ─── Staff: Kelola Antrian ────────────────────────────────────────────────

    public function manage(Request $request)
    {
        $poli_id = $request->poli_id ?? optional(Poli::where('is_active', true)->first())->id;
        $poli    = Poli::findOrFail($poli_id);
        $polis   = Poli::where('is_active', true)->get();
        $today   = today();

        $serving = Queue::whereDate('tanggal_antrian', $today)
            ->where('poli_id', $poli_id)
            ->where('status', 'serving')
            ->with('poli')
            ->first();

        $search = $request->search;

        $queues = Queue::whereDate('tanggal_antrian', $today)
            ->where('poli_id', $poli_id)
            ->where('status', '!=', 'done')
            ->when($search, fn ($q) => $q->where('nama_pasien', 'like', "%{$search}%")
                ->orWhere('queue_number', 'like', "%{$search}%"))
            ->with('poli')
            ->orderByRaw("FIELD(status, 'serving', 'waiting', 'skipped')")
            ->orderBy('queue_number')
            ->get();

        return view('queue.manage', compact('serving', 'queues', 'poli', 'polis', 'search'));
    }

    public function call(Request $request, $id)
    {
        $queue = Queue::findOrFail($id);
        $queue->update([
            'status'    => 'serving',
            'called_at' => now(),
        ]);

        return redirect()->route('queue.manage', ['poli_id' => $queue->poli_id])
            ->with('message', 'Pasien ' . $queue->nama_pasien . ' dipanggil.');
    }

    public function serve($id)
    {
        $queue = Queue::findOrFail($id);
        $queue->update([
            'status'   => 'serving',
            'served_at' => now(),
        ]);

        return redirect()->route('queue.manage', ['poli_id' => $queue->poli_id])
            ->with('message', 'Pelayanan dimulai.');
    }

    public function done($id)
    {
        $queue = Queue::findOrFail($id);
        $queue->update([
            'status'  => 'done',
            'done_at' => now(),
        ]);

        return redirect()->route('queue.manage', ['poli_id' => $queue->poli_id])
            ->with('message', 'Antrian ' . $queue->queue_number . ' selesai dilayani.');
    }

    public function skip($id)
    {
        $queue = Queue::findOrFail($id);
        $queue->update(['status' => 'skipped']);

        return redirect()->route('queue.manage', ['poli_id' => $queue->poli_id])
            ->with('message', 'Antrian ' . $queue->queue_number . ' dilewati.');
    }

    public function callNext(Request $request)
    {
        $poli_id = $request->poli_id;
        $today   = today();

        // Selesaikan yang sedang dilayani
        Queue::whereDate('tanggal_antrian', $today)
            ->where('poli_id', $poli_id)
            ->where('status', 'serving')
            ->update(['status' => 'done', 'done_at' => now()]);

        // Panggil berikutnya
        $next = Queue::whereDate('tanggal_antrian', $today)
            ->where('poli_id', $poli_id)
            ->where('status', 'waiting')
            ->orderBy('queue_number')
            ->first();

        if ($next) {
            $next->update(['status' => 'serving', 'called_at' => now()]);
        }

        return redirect()->route('queue.manage', ['poli_id' => $poli_id]);
    }
}
