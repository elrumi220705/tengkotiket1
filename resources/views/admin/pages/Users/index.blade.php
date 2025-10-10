{{-- resources/views/admin/users/index.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Daftar Pengguna')

@section('content')
<div class="users-page py-6">
  <div class="max-w-7xl mx-auto px-6">

    {{-- Header + Filter --}}
    <div class="card mb-6 animate-enter">
      <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4">
        <div>
          <h1 class="title">Daftar Pengguna</h1>
          <p class="muted">Cari, filter peran, dan batasi rentang tanggal registrasi.</p>
        </div>

        <form method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:flex gap-2 w-full md:w-auto">
          <input name="q" value="{{ $search }}" placeholder="Cari nama / email / ID" class="input">
          <select name="role" class="input">
            <option value="">Semua Peran</option>
            <option value="admin" {{ $role==='admin'?'selected':'' }}>Admin</option>
            <option value="pengguna" {{ $role==='pengguna'?'selected':'' }}>Pengguna</option>
          </select>
          <button class="btn btn-primary">Filter</button>
          @if(request()->hasAny(['q','role','from','to']))
            <a href="{{ route('admin.users.index') }}" class="btn btn-ghost">Reset</a>
          @endif
        </form>
      </div>
    </div>

    {{-- Tabel --}}
    <div class="card animate-enter overflow-x-auto">
      <table class="table w-full">
        <thead>
          <tr>
            <th class="w-14">ID</th>
            <th class="w-48">Nama</th>
            <th>Email</th>
            <th class="w-32">Role</th>
            <th class="w-40">Terdaftar</th>
            <th class="w-40 text-right">Aksi</th>
          </tr>
        </thead>
        <tbody>
        @forelse ($users as $u)
          <tr>
            <td>{{ $u->id }}</td>
            <td>{{ $u->name }}</td>
            <td><a href="mailto:{{ $u->email }}" class="link">{{ $u->email }}</a></td>
            <td>
              <span class="badge {{ $u->role==='admin' ? 'badge-danger' : 'badge-info' }}">
                {{ ucfirst($u->role) }}
              </span>
            </td>
            <td>{{ $u->created_at->format('Y-m-d H:i') }}</td>
            <td class="text-right">
              <div class="inline-flex gap-2">
                <a href="{{ route('admin.users.show', $u) }}" class="btn btn-outline btn-xs">
                  <i class="bi bi-eye"></i> Detail
                </a>
                @if (auth()->id() !== $u->id)
                  <form action="{{ route('admin.users.destroy', $u) }}" method="POST" onsubmit="return confirm('Yakin hapus pengguna ini?')" class="inline-block">
                    @csrf @method('DELETE')
                    <button class="btn btn-danger btn-xs">
                      <i class="bi bi-trash"></i> Hapus
                    </button>
                  </form>
                @endif
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="6" class="empty">Tidak ada data.</td>
          </tr>
        @endforelse
        </tbody>
      </table>

      <div class="pagination">
        {{ $users->onEachSide(1)->links() }}
      </div>
    </div>

  </div>
</div>

@push('styles')
<style>
.table {
  border-collapse: separate;
  width: 100%;
  font-size: .9rem;
}
.table th {
  text-align: left;
  padding: .7rem 1rem;
  background: #f1f5f9;
  color: #1e3a8a;
  font-weight: 700;
  border-bottom: 2px solid #e2e8f0;
}
.table td {
  padding: .65rem 1rem;
  border-bottom: 1px solid #e5e7eb;
}
.table tr:nth-child(even) { background: #fafcff; }
.table tr:hover { background: #eef4ff; }

/* Link */
.link { color:#1e3a8a; font-weight:500; }
.link:hover { text-decoration: underline; }

/* Badge */
.badge {
  display:inline-block;
  padding:.2rem .6rem;
  border-radius:999px;
  font-size:.75rem;
  font-weight:600;
}
.badge-info { background:#dbeafe; color:#1e3a8a; }
.badge-danger { background:#fee2e2; color:#b91c1c; }

/* Buttons small */
.btn { border-radius:6px; font-weight:600; display:inline-flex; align-items:center; gap:.3rem; }
.btn-xs { padding:.3rem .55rem; font-size:.78rem; }
.btn-outline { border:1px solid #94baff; color:#1e3a8a; background:#fff; }
.btn-outline:hover { background:#eef4ff; }
.btn-danger { background:#dc2626; color:#fff; border:1px solid #b91c1c; }
.btn-danger:hover { background:#b91c1c; }
.empty { text-align:center; padding:2rem; color:#6b7280; }

/* Pagination */
.pagination { padding:1rem; background:#f9fbff; border-top:1px solid #e2e8f0; }
</style>
@endpush
@endsection
