@extends('layouts.app')

@section('title', 'Kelola Antrian Petugas')

@section('content')

{{-- Page Header --}}
<div class="page-header">
  <h1>Kelola Antrian Petugas</h1>
  <p>Fokus operasional: {{ $poli->nama }}, {{ $poli->lokasi_ruang ?? 'Ruang Utama' }}</p>
</div>

{{-- Flash Message --}}
@if(session('message'))
  <div class="alert alert-success">✅ {{ session('message') }}</div>
@endif

{{-- Poli Selector --}}
<div class="flex gap-2 mb-3" style="margin-bottom:20px">
  @foreach($polis as $p)
    <a href="{{ route('queue.manage', ['poli_id' => $p->id]) }}"
       class="btn {{ $p->id == $poli->id ? 'btn-primary' : 'btn-outline' }}">
      {{ $p->nama }}
    </a>
  @endforeach
</div>

{{-- Currently Serving Card --}}
@if($serving)
<div class="status-card">
  <div class="sc-badge">● In Progress</div>
  <div class="sc-poli">{{ $serving->poli->nama }}</div>
  <div class="sc-number">{{ $serving->queue_number }}</div>
  <div class="sc-name">{{ $serving->nama_pasien }}</div>
  <div class="sc-time">
    🕐 Mulai: {{ $serving->served_at ? $serving->served_at->format('H:i') . ' WIB (' . $serving->served_at->diffForHumans() . ')' : ($serving->called_at ? $serving->called_at->format('H:i') . ' WIB' : 'Baru dipanggil') }}
  </div>
  <div class="sc-actions">
    {{-- Panggil Berikutnya --}}
    <form action="{{ route('queue.callNext') }}" method="POST">
      @csrf
      <input type="hidden" name="poli_id" value="{{ $poli->id }}">
      <button type="submit" class="btn btn-call btn-lg">
        ⏭ Panggil Berikutnya
      </button>
    </form>

    {{-- Mulai Pelayanan --}}
    <form action="{{ route('queue.serve', $serving->id) }}" method="POST">
      @csrf
      <button type="submit" class="btn btn-serve btn-lg">
        ▶ Mulai Pelayanan
      </button>
    </form>

    {{-- Selesaikan --}}
    <form action="{{ route('queue.done', $serving->id) }}" method="POST">
      @csrf
      <button type="submit" class="btn btn-done btn-lg">
        ✔ Selesaikan Pelayanan
      </button>
    </form>
  </div>
</div>
@else
<div class="card" style="text-align:center; padding:32px; margin-bottom:22px">
  <div style="font-size:40px; margin-bottom:8px">📋</div>
  <p style="color:var(--text-mid); margin-bottom:16px">Belum ada pasien yang sedang dilayani.</p>

  {{-- Panggil Pertama --}}
  <form action="{{ route('queue.callNext') }}" method="POST" style="display:inline">
    @csrf
    <input type="hidden" name="poli_id" value="{{ $poli->id }}">
    <button type="submit" class="btn btn-primary">⏭ Panggil Pasien Pertama</button>
  </form>
</div>
@endif

{{-- Queue List --}}
<div class="card">
  <div class="flex items-center justify-between" style="margin-bottom:16px">
    <div class="card-title" style="margin:0">Daftar Antrian</div>
    <form action="{{ route('queue.manage') }}" method="GET" style="display:flex;gap:8px;align-items:center">
      <input type="hidden" name="poli_id" value="{{ $poli->id }}">
      <div class="search-bar">
        <span class="search-icon">🔍</span>
        <input type="text" name="search" placeholder="Cari Pasien..." value="{{ $search }}">
      </div>
      <button type="submit" class="btn btn-outline" style="padding:8px 12px">Cari</button>
    </form>
  </div>

  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>Nomor</th>
          <th>Nama Pasien</th>
          <th>Status</th>
          <th>Waktu Daftar</th>
          <th style="text-align:right">Aksi Operasional</th>
        </tr>
      </thead>
      <tbody>
        @forelse($queues as $q)
        <tr class="{{ $q->status === 'done' ? 'row-done' : ($q->status === 'serving' ? 'row-serving' : '') }}">
          <td><strong>{{ $q->queue_number }}</strong></td>
          <td>{{ $q->nama_pasien }}</td>
          <td>
            <span class="badge badge-{{ $q->status }}">
              {{ $q->status_label }}
            </span>
          </td>
          <td class="text-muted">{{ $q->created_at->format('H:i') }} WIB</td>
          <td style="text-align:right">
            @if($q->status === 'waiting')
              <div style="display:flex;gap:6px;justify-content:flex-end">
                {{-- Panggil --}}
                <form action="{{ route('queue.call', $q->id) }}" method="POST">
                  @csrf
                  <button type="submit" class="btn btn-icon" title="Panggil">🔊</button>
                </form>
                {{-- Mulai --}}
                <form action="{{ route('queue.serve', $q->id) }}" method="POST">
                  @csrf
                  <button type="submit" class="btn btn-icon" title="Mulai Layanan">▶</button>
                </form>
              </div>
            @elseif($q->status === 'serving')
              <form action="{{ route('queue.done', $q->id) }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-icon" title="Selesai" style="background:var(--brown-dark);color:#fff;border-color:var(--brown-dark)">✔</button>
              </form>
            @else
              <span style="color:var(--text-light);font-size:18px">✔</span>
            @endif
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="5" style="text-align:center; padding:30px; color:var(--text-light)">
            Tidak ada antrian untuk poli ini hari ini.
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

@endsection

@push('styles')
<style>
  .sc-actions { flex-wrap: wrap; }
  .btn-call  { background:var(--white); border:1px solid var(--cream-border); color:var(--text-dark); }
  .btn-call:hover  { background:var(--cream); }
  .btn-serve { background:var(--cream-dark); border:1px solid var(--cream-border); color:var(--text-dark); }
  .btn-serve:hover { background:var(--cream-border); }
  .btn-done  { background:var(--brown-dark); color:#fff; }
  .btn-done:hover  { background:var(--brown-mid); }
  tr.row-serving td { font-weight:600; }
  tr.row-serving { background: rgba(61,31,13,.04); }
</style>
@endpush
