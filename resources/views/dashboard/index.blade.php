@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

{{-- Page Header --}}
<div class="page-header">
  <h1>Dashboard Overview</h1>
</div>

{{-- Stats Grid --}}
<div class="stats-grid">
  <div class="stat-card">
    <div class="stat-info">
      <div class="stat-label">Total Hari Ini</div>
      <div class="stat-value">{{ $stats['total'] }}</div>
    </div>
    <div class="stat-icon blue">👥</div>
  </div>
  <div class="stat-card">
    <div class="stat-info">
      <div class="stat-label">Menunggu</div>
      <div class="stat-value">{{ $stats['waiting'] }}</div>
    </div>
    <div class="stat-icon orange">⏳</div>
  </div>
  <div class="stat-card">
    <div class="stat-info">
      <div class="stat-label">Sedang Dilayani</div>
      <div class="stat-value">{{ $stats['serving'] }}</div>
    </div>
    <div class="stat-icon pink">🩺</div>
  </div>
  <div class="stat-card">
    <div class="stat-info">
      <div class="stat-label">Selesai</div>
      <div class="stat-value">{{ $stats['done'] }}</div>
    </div>
    <div class="stat-icon green">✅</div>
  </div>
</div>

{{-- Menu Utama --}}
<div class="page-header mb-2" style="margin-bottom:14px">
  <h2 style="font-size:16px;font-weight:700">Menu Utama</h2>
</div>

<div class="menu-grid">

  <a href="{{ route('queue.take') }}" style="text-decoration:none">
    <div class="menu-card">
      <div class="menu-icon">➕</div>
      <h3>Pengambilan Antrian</h3>
      <p>Daftarkan pasien baru ke dalam sistem antrian klinik.</p>
      <span class="menu-link">Mulai →</span>
    </div>
  </a>

  <a href="{{ route('monitor.index') }}" style="text-decoration:none">
    <div class="menu-card">
      <div class="menu-icon">📺</div>
      <h3>Monitoring Antrian</h3>
      <p>Lihat status realtime pasien yang sedang menunggu dan dilayani.</p>
      <span class="menu-link">Lihat Monitor →</span>
    </div>
  </a>

  <a href="{{ route('queue.manage') }}" style="text-decoration:none">
    <div class="menu-card">
      <div class="menu-icon">⚙️</div>
      <h3>Kelola Antrian Petugas</h3>
      <p>Atur panggilan pasien, skip, atau selesaikan layanan untuk loket/poli.</p>
      <span class="menu-link">Kelola →</span>
    </div>
  </a>

</div>

{{-- Antrian Aktif Per Poli --}}
<div class="card" style="margin-top:28px">
  <div class="card-title">Status Poli Hari Ini</div>
  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>Poli</th>
          <th>Lokasi</th>
          <th>Menunggu</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        @forelse($polis as $poli)
        <tr>
          <td><strong>{{ $poli->nama }}</strong></td>
          <td>{{ $poli->lokasi_ruang ?? '-' }}</td>
          <td>
            <span class="badge badge-waiting">{{ $poli->waitingCount() }} pasien</span>
          </td>
          <td>
            <span class="badge badge-active">● Aktif</span>
          </td>
        </tr>
        @empty
        <tr><td colspan="4" class="text-center text-muted" style="padding:20px">Belum ada poli aktif.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

@endsection
