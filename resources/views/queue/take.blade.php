@extends('layouts.app')

@section('title', 'Pengambilan Antrian')

@section('content')

{{-- Breadcrumb --}}
<div class="breadcrumb">
  <a href="{{ route('dashboard') }}">🏠 Beranda</a>
  <span class="sep">›</span>
  <span>Pengambilan Antrian</span>
</div>

{{-- Page Header --}}
<div class="page-header">
  <h1>Pengambilan Antrian Mahasiswa</h1>
  <p>Silakan isi formulir di bawah ini untuk mengambil nomor antrian. Layanan klinik khusus untuk mahasiswa aktif universitas.</p>
</div>

{{-- Flash: Success --}}
@if(session('success'))
  @php $queueId = session('success'); $queue = \App\Models\Queue::find($queueId); @endphp
  @if($queue)
  <div class="alert alert-success">
    ✅ Nomor antrian <strong>{{ $queue->queue_number }}</strong> berhasil diambil! Estimasi layanan: <strong>{{ $queue->estimated_time }} WIB</strong>
  </div>
  @endif
@endif

<div style="display:grid; grid-template-columns:1fr 340px; gap:24px; align-items:start">

  {{-- ── Form Panel ─────────────────────────────────────── --}}
  <div class="card">
    <div class="card-title">Informasi Pasien</div>

    <form action="{{ route('queue.store') }}" method="POST" id="queue-form">
      @csrf

      <div class="form-row">
        <div class="form-group">
          <label class="form-label" for="nim">NIM</label>
          <input id="nim" name="nim" type="text" class="form-control"
            placeholder="Contoh: 1234567890"
            value="{{ old('nim', auth()->user()->role === 'mahasiswa' ? auth()->user()->username : '') }}"
            oninput="updatePreview()">
          @error('nim')<div class="form-error">{{ $message }}</div>@enderror
        </div>
        <div class="form-group">
          <label class="form-label" for="nama_pasien">Nama Lengkap</label>
          <input id="nama_pasien" name="nama_pasien" type="text" class="form-control"
            placeholder="Sesuai Kartu Mahasiswa"
            value="{{ old('nama_pasien', auth()->user()->role === 'mahasiswa' ? auth()->user()->name : '') }}"
            oninput="updatePreview()">
          @error('nama_pasien')<div class="form-error">{{ $message }}</div>@enderror
        </div>
      </div>

      <div class="form-group">
        <label class="form-label" for="program_studi">Program Studi</label>
        <input id="program_studi" name="program_studi" type="text" class="form-control"
          placeholder="Masukkan Program Studi Anda"
          value="{{ old('program_studi', auth()->user()->program_studi ?? '') }}">
        @error('program_studi')<div class="form-error">{{ $message }}</div>@enderror
      </div>

      <div class="form-group">
        <label class="form-label" for="poli_id">Tujuan Poli</label>
        <select id="poli_id" name="poli_id" class="form-control" onchange="updatePreview()">
          <option value="">Pilih Layanan Poli</option>
          @foreach($polis as $poli)
            <option value="{{ $poli->id }}" data-kode="{{ $poli->kode }}" data-nama="{{ $poli->nama }}"
              {{ old('poli_id') == $poli->id ? 'selected' : '' }}>
              {{ $poli->nama }}
            </option>
          @endforeach
        </select>
        @error('poli_id')<div class="form-error">{{ $message }}</div>@enderror
      </div>

      <div class="form-group">
        <label class="form-label" for="keluhan">Keluhan Utama</label>
        <textarea id="keluhan" name="keluhan" class="form-control"
          placeholder="Jelaskan keluhan singkat Anda...">{{ old('keluhan') }}</textarea>
        @error('keluhan')<div class="form-error">{{ $message }}</div>@enderror
      </div>

      <button type="submit" class="btn btn-primary btn-full" style="margin-top:8px">
        🎫 Ambil Nomor Antrian
      </button>
    </form>
  </div>

  {{-- ── Ticket Preview ──────────────────────────────────── --}}
  <div>
    <div class="card" style="margin-bottom:16px">
      <div class="card-title" style="margin-bottom:20px">Preview Tiket</div>

      <div class="ticket-preview" id="ticket-preview">
        <div class="ticket-label">Nomor Antrian Anda</div>
        <div class="ticket-number" id="prev-number">—</div>
        <span class="badge badge-waiting" id="prev-status">Menunggu</span>
        <div style="margin-top:16px">
          <div class="ticket-info-row">
            <span class="tir-label">Nama</span>
            <span class="tir-value" id="prev-nama">[Nama Mahasiswa]</span>
          </div>
          <div class="ticket-info-row">
            <span class="tir-label">Poli</span>
            <span class="tir-value" id="prev-poli">[Poli Tujuan]</span>
          </div>
          <div class="ticket-info-row">
            <span class="tir-label">Estimasi Dilayani</span>
            <span class="tir-value" id="prev-time">-- WIB</span>
          </div>
        </div>
      </div>
    </div>

    <div class="card" style="background:var(--cream); border:none; font-size:12px; color:var(--text-mid); text-align:center; padding:16px">
      Harap datang 10 menit sebelum estimasi waktu layanan Anda.
    </div>
  </div>

</div>

@endsection

@push('scripts')
<script>
  // Real-time preview update
  const polisData = @json($polis->map(fn($p) => ['id'=>$p->id,'kode'=>$p->kode,'nama'=>$p->nama]));
  const queueCounter = @json($polis->mapWithKeys(fn($p) => [$p->id => $p->todayQueues()->whereIn('status',['waiting','serving'])->count()]));

  function updatePreview() {
    const namaEl  = document.getElementById('prev-nama');
    const poliEl  = document.getElementById('prev-poli');
    const numEl   = document.getElementById('prev-number');
    const timeEl  = document.getElementById('prev-time');

    const nama    = document.getElementById('nama_pasien').value.trim();
    const poliSel = document.getElementById('poli_id');
    const poliId  = poliSel.value;
    const opt     = poliSel.options[poliSel.selectedIndex];
    const kode    = opt ? opt.getAttribute('data-kode') : null;
    const poliNama = opt ? opt.getAttribute('data-nama') : null;

    namaEl.textContent = nama || '[Nama Mahasiswa]';
    poliEl.textContent = poliNama || '[Poli Tujuan]';

    if (kode) {
      numEl.textContent = kode + '-???';
    } else {
      numEl.textContent = '—';
    }

    // Estimate time
    const now = new Date();
    const est = new Date(now.getTime() + 10 * 60000);
    const hh  = String(est.getHours()).padStart(2,'0');
    const mm  = String(est.getMinutes()).padStart(2,'0');
    timeEl.textContent = hh + ':' + mm + ' WIB';
  }

  updatePreview();
</script>
@endpush
