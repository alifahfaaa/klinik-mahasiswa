@extends('layouts.app')

@section('title', 'History Antrian')

@section('content')

<div class="page-header">
  <h1>History Antrian</h1>
  <p>Riwayat antrian yang telah selesai dilayani.</p>
</div>

{{-- Filter Bar --}}
<div class="card" style="margin-bottom:20px">
  <form action="{{ route('history.index') }}" method="GET" style="display:flex;gap:12px;align-items:flex-end;flex-wrap:wrap">
    <div class="form-group" style="margin:0;flex:1;min-width:160px">
      <label class="form-label">Tanggal</label>
      <input type="date" name="tanggal" class="form-control" value="{{ $tanggal }}">
    </div>
    <div class="form-group" style="margin:0;flex:1;min-width:160px">
      <label class="form-label">Poli</label>
      <select name="poli_id" class="form-control">
        <option value="">Semua Poli</option>
        @foreach($polis as $p)
          <option value="{{ $p->id }}" {{ $poliId == $p->id ? 'selected' : '' }}>{{ $p->nama }}</option>
        @endforeach
      </select>
    </div>
    <div class="form-group" style="margin:0;flex:2;min-width:200px">
      <label class="form-label">Cari Pasien</label>
      <input type="text" name="search" class="form-control" placeholder="Nama atau nomor antrian..." value="{{ $search }}">
    </div>
    <button type="submit" class="btn btn-primary" style="padding:10px 20px">🔍 Filter</button>
    <a href="{{ route('history.index') }}" class="btn btn-outline">Reset</a>
  </form>
</div>

{{-- Results --}}
<div class="card">
  <div class="flex items-center justify-between" style="margin-bottom:16px">
    <div class="card-title" style="margin:0">
      Riwayat — {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('d F Y') }}
    </div>
    <span class="badge badge-done">{{ $histories->total() }} total</span>
  </div>

  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>Nomor</th>
          <th>Nama Pasien</th>
          <th>NIM</th>
          <th>Poli</th>
          <th>Waktu Daftar</th>
          <th>Selesai</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        @forelse($histories as $h)
        <tr>
          <td><strong>{{ $h->queue_number }}</strong></td>
          <td>{{ $h->nama_pasien }}</td>
          <td class="text-muted">{{ $h->nim }}</td>
          <td>{{ $h->poli->nama }}</td>
          <td class="text-muted">{{ $h->created_at->format('H:i') }} WIB</td>
          <td class="text-muted">{{ $h->done_at ? $h->done_at->format('H:i') . ' WIB' : '-' }}</td>
          <td><span class="badge badge-done">Selesai</span></td>
        </tr>
        @empty
        <tr>
          <td colspan="7" class="text-center text-muted" style="padding:30px">
            Tidak ada riwayat antrian untuk filter yang dipilih.
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  {{-- Pagination --}}
  @if($histories->hasPages())
  <div class="pagination">
    {{ $histories->links() }}
  </div>
  @endif
</div>

@endsection
