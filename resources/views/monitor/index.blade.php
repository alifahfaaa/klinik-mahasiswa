@extends('layouts.app')

@section('title', 'Queue Monitor')

@section('content')

<div class="page-header">
  <h1>Queue Monitor</h1>
</div>

{{-- ── Top Row ─────────────────────────────────────────────── --}}
<div style="display:grid; grid-template-columns:280px 1fr 200px; gap:18px; margin-bottom:22px; align-items:stretch">

  {{-- Sedang Dilayani --}}
  <div class="monitor-serving-card">
    <div class="ms-label">Sedang Dilayani</div>
    @if($serving)
      <div class="ms-poli">🏥 {{ $serving->poli->nama }}</div>
      <div class="ms-number">{{ $serving->queue_number }}</div>
      <div class="ms-name">{{ $serving->nama_pasien }}</div>
    @else
      <div class="ms-poli" style="margin-top:16px">Belum ada yang dilayani</div>
      <div class="ms-number" style="opacity:.4">—</div>
    @endif
  </div>

  {{-- Nomor Berikutnya --}}
  <div class="card" style="display:flex;flex-direction:column;justify-content:center">
    <div style="font-size:11px;color:var(--text-light);font-weight:600;letter-spacing:.5px;text-transform:uppercase;margin-bottom:6px">
      NOMOR BERIKUTNYA →
    </div>
    @if($nextQueue)
      <div style="font-size:36px;font-weight:800;color:var(--text-dark);letter-spacing:1px">
        {{ $nextQueue->queue_number }}
      </div>
      <div style="font-size:14px;font-weight:600;margin-top:4px">{{ $nextQueue->nama_pasien }}</div>
      <div style="font-size:12px;color:var(--text-light);margin-top:4px">
        🕐 Estimasi: {{ $nextQueue->estimated_time ? $nextQueue->estimated_time . ' WIB' : '± 10 Menit' }}
      </div>
    @else
      <div style="font-size:28px;font-weight:800;opacity:.3">—</div>
      <div style="font-size:13px;color:var(--text-light);margin-top:4px">Tidak ada antrian berikutnya</div>
    @endif
  </div>

  {{-- Total Menunggu --}}
  <div class="card" style="display:flex;flex-direction:column;justify-content:center;align-items:center;text-align:center">
    <div style="font-size:11px;color:var(--text-light);font-weight:600;letter-spacing:.5px;text-transform:uppercase;margin-bottom:6px">
      TOTAL MENUNGGU 👥
    </div>
    <div style="font-size:52px;font-weight:800;color:var(--text-dark);line-height:1">{{ $totalWaiting }}</div>
    <div style="font-size:13px;color:var(--text-light);margin-top:4px">Pasien</div>
  </div>

</div>

{{-- ── Bottom Row ──────────────────────────────────────────── --}}
<div style="display:grid; grid-template-columns:1fr 260px; gap:18px; align-items:start">

  {{-- Daftar Antrian Aktif --}}
  <div class="card">
    <div class="card-title">Daftar Antrian Aktif</div>
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>Nomor</th>
            <th>Nama Pasien</th>
            <th>Poli</th>
            <th>Waktu Daftar</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          @forelse($activeQueues as $q)
          <tr class="{{ $q->status === 'done' ? 'row-done' : '' }}">
            <td><strong>{{ $q->queue_number }}</strong></td>
            <td>{{ $q->nama_pasien }}</td>
            <td>{{ $q->poli->nama }}</td>
            <td class="text-muted">{{ $q->created_at->format('H:i') }} WIB</td>
            <td>
              <span class="badge badge-{{ $q->status }}">{{ $q->status_label }}</span>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="5" class="text-center text-muted" style="padding:24px">Belum ada antrian hari ini.</td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  {{-- Status Poli + Aksi Cepat --}}
  <div>
    <div class="card" style="margin-bottom:16px">
      <div class="card-title">Status Poli</div>
      @foreach($poliStats as $ps)
      <div style="display:flex;justify-content:space-between;align-items:center;padding:8px 0;border-bottom:1px solid var(--cream)">
        <div style="display:flex;align-items:center;gap:8px;font-size:13px">
          <span style="width:8px;height:8px;border-radius:50%;background:var(--brown-dark);display:inline-block"></span>
          {{ $ps['nama'] }}
        </div>
        <span style="font-size:12px;color:var(--text-mid);font-weight:600">{{ $ps['waiting'] }} Menunggu</span>
      </div>
      @endforeach
    </div>

    <div class="card">
      <div class="card-title">Aksi Cepat</div>
      <a href="{{ route('queue.take') }}" class="btn btn-outline w-full" style="margin-bottom:10px;justify-content:center">
        ➕ Daftar Pasien Manual
      </a>
      <form action="{{ route('queue.callNext') }}" method="POST">
        @csrf
        <input type="hidden" name="poli_id" value="{{ optional($serving ? $serving->poli : \App\Models\Poli::where('is_active',true)->first())->id }}">
        <button type="submit" class="btn btn-primary w-full" style="justify-content:center">
          🔊 Panggil Ulang Nomor
        </button>
      </form>
    </div>
  </div>

</div>

@endsection

@push('scripts')
<script>
  // Auto-refresh every 30 seconds
  setTimeout(() => window.location.reload(), 30000);
</script>
@endpush
