{{-- resources/views/admin/users/show.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Detail Pengguna')

@section('content')
<div class="users-page py-6">
  <div class="max-w-7xl mx-auto px-6">

    {{-- Header --}}
    <div class="card mb-6 animate-enter">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 flex items-center justify-center rounded-lg bg-gradient-to-r from-blue-500 to-indigo-600 text-white">
          <i class="bi bi-person-fill text-xl"></i>
        </div>
        <div>
          <h1 class="title">Detail Pengguna</h1>
          <p class="muted">Informasi lengkap pengguna yang dipilih.</p>
        </div>
      </div>
    </div>

    {{-- Detail Card --}}
    <div class="card animate-enter">
      <div class="kv">
        <div class="row">
          <div class="key">ID</div>
          <div class="val">{{ $user->id }}</div>
        </div>

        <div class="row">
          <div class="key">Nama</div>
          <div class="val">{{ $user->name }}</div>
        </div>

        <div class="row">
          <div class="key">Email</div>
          <div class="val">
            <a href="mailto:{{ $user->email }}" class="link">{{ $user->email }}</a>
          </div>
        </div>

        <div class="row">
          <div class="key">Role</div>
          <div class="val">
            <span class="badge {{ $user->role==='admin' ? 'badge-danger' : 'badge-info' }}">
              {{ ucfirst($user->role) }}
            </span>
          </div>
        </div>

        <div class="row">
          <div class="key">Terdaftar</div>
          <div class="val">{{ $user->created_at?->format('Y-m-d H:i') }}</div>
        </div>
      </div>

      {{-- Actions --}}
      <div class="mt-5 flex items-center justify-between gap-3">
        <a href="{{ route('admin.users.index') }}" class="btn btn-outline">
          <i class="bi bi-arrow-left"></i> Kembali
        </a>

        @if (auth()->id() !== $user->id)
          <form action="{{ route('admin.users.destroy', $user) }}" method="POST"
                onsubmit="return confirm('Yakin hapus pengguna ini?')" class="inline-block">
            @csrf @method('DELETE')
            <button class="btn btn-danger">
              <i class="bi bi-trash"></i> Hapus
            </button>
          </form>
        @endif
      </div>
    </div>

  </div>
</div>

@push('styles')
<style>
/* Layout key–value agar seragam rasa "card" & tabel */
.kv { width:100%; }
.kv .row {
  display:grid;
  grid-template-columns: 220px 1fr;
  gap: 1rem;
  padding: .8rem 1rem;
  border-bottom: 1px solid #e5e7eb;
}
.kv .row:first-child { border-top: 1px solid #e5e7eb; }
.kv .key {
  color:#475569;           /* slate-600 */
  font-weight:600;
}
.kv .val {
  color:#111827;           /* gray-900 */
  word-break: break-word;
}

/* Responsif: tumpuk ke vertikal di layar kecil */
@media (max-width: 640px) {
  .kv .row {
    grid-template-columns: 1fr;
    gap: .25rem;
  }
  .kv .key { font-size: .9rem; }
}

/* Samakan rasa card jika belum ada */
.card {
  background:#fff;
  border-radius: .75rem;
  box-shadow: 0 10px 15px -3px rgba(0,0,0,.07), 0 4px 6px -2px rgba(0,0,0,.05);
  border: 1px solid #f1f5f9; /* slate-100 */
  padding: 1.25rem;
}

/* Animasi masuk lembut (selaras dengan index) */
.animate-enter {
  animation: enter .24s ease-out both;
}
@keyframes enter {
  from { opacity:0; transform: translateY(6px); }
  to   { opacity:1; transform: translateY(0); }
}
</style>
@endpush
@endsection
