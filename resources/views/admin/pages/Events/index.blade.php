@extends('admin.layouts.app')
@section('title', 'Daftar Event')
@section('page-title', 'Manajemen Event')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title">Daftar Event Tersedia</h3>
        <a href="{{ route('admin.events.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Tambah Event Baru
        </a>
    </div>
    <div class="card-body p-0">

        @if(session('success'))
            <div class="alert alert-success m-3">
                {{ session('success') }}
            </div>
        @endif

        <table class="table table-striped table-hover mb-0">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama Event</th>
                    <th>Tanggal</th>
                    <th>Harga Dasar</th>
                    <th>Stok Tersedia</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($events as $event)
                    <tr>
                        <td>{{ $event->id }}</td>
                        <td>{{ $event->nama_event }}</td>
                        <td>{{ \Carbon\Carbon::parse($event->tanggal_mulai)->translatedFormat('d M Y') }}</td>
                        <td>Rp{{ number_format($event->harga_dasar, 0, ',', '.') }}</td>
                        <td>
                            {{ $event->stok_tersedia }} / {{ $event->kapasitas_total }}
                        </td>
                        <td>
                            {{-- Tampilkan status Event dengan badge yang berwarna --}}
                            @php
                                $badgeClass = match($event->status) {
                                    'published' => 'badge bg-success',
                                    'draft' => 'badge bg-warning',
                                    'cancelled' => 'badge bg-danger',
                                    default => 'badge bg-secondary',
                                };
                            @endphp
                            <span class="{{ $badgeClass }}">{{ ucfirst($event->status) }}</span>
                        </td>
                        <td>
                            <a href="{{ route('admin.events.edit', $event->id) }}" class="btn btn-sm btn-info text-white me-1" title="Detail/Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('admin.events.destroy', $event->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" title="Hapus" onclick="return confirm('APAKAH ANDA YAKIN INGIN MENGHAPUS EVENT INI SECARA PERMANEN?')">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center">Belum ada Event yang dibuat. Klik 'Tambah Event Baru' untuk memulai.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
