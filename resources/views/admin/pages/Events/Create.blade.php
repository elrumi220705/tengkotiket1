@extends('admin.layouts.app')

@section('title', 'Buat Event Baru')
@section('page-title', 'Manajemen Event: Tambah Baru')

@section('content')
<div class="card shadow-lg border-0">
    <div class="card-header bg-gradient-primary text-white">
        <h3 class="card-title mb-0">
            <i class="bi bi-calendar-plus me-2"></i> Formulir Event Baru
        </h3>
    </div>
    <div class="card-body">

        <form action="{{ route('admin.events.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            {{-- Notifikasi Error --}}
            @if ($errors->any())
                <div class="alert alert-danger rounded-3 shadow-sm">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li><i class="bi bi-exclamation-circle"></i> {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="row">
                {{-- Kiri --}}
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="nama_event" class="form-label fw-bold">Nama Event <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nama_event" name="nama_event"
                            value="{{ old('nama_event') }}" required>
                    </div>

                    <div class="form-group mb-3">
                        <label for="lokasi" class="form-label fw-bold">Lokasi / Venue <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="lokasi" name="lokasi"
                            value="{{ old('lokasi') }}" required>
                    </div>

                    <div class="form-group mb-3">
                        <label for="deskripsi" class="form-label fw-bold">Deskripsi Event</label>
                        <textarea class="form-control" id="deskripsi" name="deskripsi" rows="5">{{ old('deskripsi') }}</textarea>
                    </div>

                    <div class="form-group mb-3">
                        <label for="gambar_file" class="form-label fw-bold">Gambar Event (Max 2MB)</label>
                        <input type="file" class="form-control" id="gambar_file" name="gambar_file" accept="image/*" onchange="previewImage(event)">
                        <div class="mt-2">
                            <img id="preview" src="{{ asset('images/default-event.jpg') }}"
                                 alt="Preview Gambar" class="img-fluid rounded shadow-sm"
                                 style="max-height: 200px;">
                        </div>
                    </div>
                </div>

                {{-- Kanan --}}
                <div class="col-md-6">
                    <div class="row">
                        <div class="col-md-6 form-group mb-3">
                            <label for="tanggal_mulai" class="form-label fw-bold">Tanggal Mulai <span class="text-danger">*</span></label>
                            <input type="datetime-local" class="form-control" id="tanggal_mulai"
                                   name="tanggal_mulai" value="{{ old('tanggal_mulai') }}" required>
                        </div>
                        <div class="col-md-6 form-group mb-3">
                            <label for="tanggal_selesai" class="form-label fw-bold">Tanggal Selesai (Opsional)</label>
                            <input type="datetime-local" class="form-control" id="tanggal_selesai"
                                   name="tanggal_selesai" value="{{ old('tanggal_selesai') }}">
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label for="harga_dasar" class="form-label fw-bold">Harga Dasar Tiket (Rp) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="harga_dasar" name="harga_dasar"
                               value="{{ old('harga_dasar') }}" min="1000" required>
                    </div>

                    <div class="form-group mb-3">
                        <label for="kapasitas_total" class="form-label fw-bold">Kapasitas Total <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="kapasitas_total"
                               name="kapasitas_total" value="{{ old('kapasitas_total') }}" min="1" required>
                        <small class="form-text text-muted">Stok awal akan sama dengan kapasitas total.</small>
                    </div>

                    <hr>
                    <button type="submit" class="btn btn-success w-100">
                        <i class="bi bi-save"></i> Simpan Event (Status: Draft)
                    </button>
                    <a href="{{ route('admin.events.index') }}" class="btn btn-secondary w-100 mt-2">
                        <i class="bi bi-arrow-left"></i> Batal / Kembali
                    </a>
                </div>
            </div>
        </form>

    </div>
</div>

{{-- Script Preview Gambar --}}
<script>
    function previewImage(event) {
        const reader = new FileReader();
        reader.onload = function(){
            document.getElementById('preview').src = reader.result;
        };
        reader.readAsDataURL(event.target.files[0]);
    }
</script>
@endsection
