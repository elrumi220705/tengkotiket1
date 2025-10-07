@extends('admin.layouts.app')

@section('title', 'Edit Event: ' . $event->nama_event)
@section('page-title', 'Manajemen Event: Edit')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Edit Event: {{ $event->nama_event }}</h3>
    </div>
    <div class="card-body">

        <form action="{{ route('admin.events.update', $event->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="nama_event" class="form-label">Nama Event <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nama_event" name="nama_event" value="{{ old('nama_event', $event->nama_event) }}" required>
                    </div>

                    <div class="form-group mb-3">
                        <label for="lokasi" class="form-label">Lokasi / Venue <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="lokasi" name="lokasi" value="{{ old('lokasi', $event->lokasi) }}" required>
                    </div>

                    <div class="form-group mb-3">
                        <label for="deskripsi" class="form-label">Deskripsi Event</label>
                        <textarea class="form-control" id="deskripsi" name="deskripsi" rows="5">{{ old('deskripsi', $event->deskripsi) }}</textarea>
                    </div>

                    <div class="form-group mb-3">
                        <label for="gambar_file" class="form-label">Gambar Event (Max 2MB)</label>
                        <input type="file" class="form-control" id="gambar_file" name="gambar_file" accept="image/*">
                        @if($event->gambar)
                            <small class="form-text text-muted mt-2">Gambar saat ini: <a href="{{ Storage::url($event->gambar) }}" target="_blank">Lihat Gambar</a> (Akan diganti jika upload baru).</small>
                            <img src="{{ Storage::url($event->gambar) }}" alt="{{ $event->nama_event }}" class="img-thumbnail mt-2" style="max-width: 150px;">
                        @endif
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="row">
                        <div class="col-md-6 form-group mb-3">
                            <label for="tanggal_mulai" class="form-label">Tanggal Mulai <span class="text-danger">*</span></label>
                            @php
                                $start_time = \Carbon\Carbon::parse(old('tanggal_mulai', $event->tanggal_mulai))->format('Y-m-d\TH:i');
                            @endphp
                            <input type="datetime-local" class="form-control" id="tanggal_mulai" name="tanggal_mulai" value="{{ $start_time }}" required>
                        </div>
                        <div class="col-md-6 form-group mb-3">
                            <label for="tanggal_selesai" class="form-label">Tanggal Selesai (Opsional)</label>
                            @php
                                $end_time = $event->tanggal_selesai ? \Carbon\Carbon::parse(old('tanggal_selesai', $event->tanggal_selesai))->format('Y-m-d\TH:i') : '';
                            @endphp
                            <input type="datetime-local" class="form-control" id="tanggal_selesai" name="tanggal_selesai" value="{{ $end_time }}">
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label for="harga_dasar" class="form-label">Harga Dasar Tiket (Rp) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="harga_dasar" name="harga_dasar" value="{{ old('harga_dasar', $event->harga_dasar) }}" min="1000" required>
                    </div>

                    <div class="form-group mb-3">
                        <label for="kapasitas_total" class="form-label">Kapasitas Total <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="kapasitas_total" name="kapasitas_total" value="{{ old('kapasitas_total', $event->kapasitas_total) }}" min="{{ $event->kapasitas_total - $event->stok_tersedia }}" required>
                        <small class="form-text text-muted">Stok Tersedia Saat Ini: {{ $event->stok_tersedia }}. Kapasitas total tidak boleh kurang dari jumlah tiket yang sudah terjual ({{ $event->kapasitas_total - $event->stok_tersedia }}).</small>
                    </div>

                    <div class="form-group mb-3">
                        <label for="status" class="form-label">Status Event <span class="text-danger">*</span></label>
                        <select class="form-control" id="status" name="status" required>
                            <option value="draft" {{ old('status', $event->status) == 'draft' ? 'selected' : '' }}>Draft (Belum Publik)</option>
                            <option value="published" {{ old('status', $event->status) == 'published' ? 'selected' : '' }}>Published (Siap Jual)</option>
                            <option value="cancelled" {{ old('status', $event->status) == 'cancelled' ? 'selected' : '' }}>Cancelled (Dibatalkan)</option>
                        </select>
                        <small class="form-text text-muted">Ubah ke **Published** agar event terlihat di halaman utama.</small>
                    </div>

                    <hr>
                    <button type="submit" class="btn btn-success w-100">Simpan Perubahan Event</button>
                    <a href="{{ route('admin.events.index') }}" class="btn btn-secondary w-100 mt-2">Batal / Kembali</a>
                </div>
            </div>
        </form>

    </div>
</div>
@endsection
