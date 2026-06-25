<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login — Klinik Mahasiswa</title>
  <meta name="description" content="Masuk ke Sistem Antrian Klinik Layanan Mahasiswa">
  <link rel="stylesheet" href="{{ asset('css/klinik.css') }}">
</head>
<body>

<div class="login-shell">

  {{-- ── Left Panel ──────────────────────────────────────── --}}
  <div class="login-left">
    <h1>Sistem Antrian Klinik Layanan Mahasiswa</h1>
    <p>Layanan digital terpadu untuk memastikan pengalaman medis yang tenang, efisien, dan tanpa hambatan bagi seluruh civitas akademika.</p>
  </div>

  {{-- ── Right Panel ─────────────────────────────────────── --}}
  <div class="login-right">
    <div class="login-box">

      <h2>Login</h2>
      <p class="login-sub">Silakan masuk menggunakan kredensial Anda.</p>

      {{-- Flash Errors --}}
      @if ($errors->any())
        <div class="alert alert-error">
          ⚠️ {{ $errors->first() }}
        </div>
      @endif

      <form action="{{ route('login.post') }}" method="POST">
        @csrf

        <div class="form-group">
          <label class="form-label" for="username">Username / NIM</label>
          <input
            id="username"
            name="username"
            type="text"
            class="form-control"
            placeholder="Masukkan Username Anda"
            value="{{ old('username') }}"
            autocomplete="username"
            autofocus
          >
        </div>

        <div class="form-group">
          <label class="form-label" for="password">
            Password
            <a href="#" class="forgot-link">Lupa Password?</a>
          </label>
          <input
            id="password"
            name="password"
            type="password"
            class="form-control"
            placeholder="Masukkan Password Anda"
            autocomplete="current-password"
          >
        </div>

        <button type="submit" class="btn btn-primary btn-full" style="margin-top:8px">
          Masuk
        </button>
      </form>

      <p class="login-footer">© Klinik Layanan Mahasiswa</p>
    </div>
  </div>

</div>

</body>
</html>
