<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'Klinik Mahasiswa') — Arselva Clinic</title>
  <meta name="description" content="Sistem Antrian Klinik Layanan Mahasiswa — Arselva Clinic">
  <link rel="stylesheet" href="{{ asset('css/klinik.css') }}">
  @stack('styles')
</head>
<body>

<div class="app-shell">

  {{-- ── Sidebar ───────────────────────────────────────────── --}}
  <aside class="sidebar">
    <div class="sidebar-brand">
      <div class="brand-icon">🏥</div>
      <div class="brand-text">
        <h2>Medical Center</h2>
        <span>Staff Portal</span>
      </div>
    </div>

    {{-- User card --}}
    <div class="sidebar-user-card">
      <div class="user-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
      <div class="user-info">
        <div class="user-name">{{ auth()->user()->name }}</div>
        <div class="user-role">{{ ucfirst(auth()->user()->role) }}</div>
      </div>
    </div>

    {{-- CTA button --}}
    <div class="sidebar-cta">
      <a href="{{ route('queue.take') }}">+ Take New Queue</a>
    </div>

    {{-- Navigation --}}
    <nav class="sidebar-nav">
      <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
        <span class="nav-icon">⊞</span> Dashboard
      </a>
      <a href="{{ route('monitor.index') }}" class="nav-item {{ request()->routeIs('monitor.*') ? 'active' : '' }}">
        <span class="nav-icon">📺</span> Queue Monitor
      </a>
      <a href="{{ route('queue.manage') }}" class="nav-item {{ request()->routeIs('queue.manage') ? 'active' : '' }}">
        <span class="nav-icon">☰</span> Manage Queue
      </a>
      <a href="{{ route('history.index') }}" class="nav-item {{ request()->routeIs('history.*') ? 'active' : '' }}">
        <span class="nav-icon">🕑</span> History
      </a>
    </nav>

    {{-- Footer --}}
    <div class="sidebar-footer">
      <a href="#" class="nav-item">
        <span class="nav-icon">❓</span> Support
      </a>
      <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit" class="nav-item" style="color:var(--danger)">
          <span class="nav-icon">↪</span> Logout
        </button>
      </form>
    </div>
  </aside>

  {{-- ── Main Content ──────────────────────────────────────── --}}
  <main class="main-content">
    <div class="content-area">
      @yield('content')
    </div>
  </main>

</div>

@stack('scripts')
</body>
</html>
