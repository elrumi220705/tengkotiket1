@extends('layouts.app')

@section('title', 'Checkout')
@section('page-title', 'Checkout Tiket')

@section('content')
<div class="container mt-4">
  <div class="card shadow">
    <div class="card-header bg-primary text-white">
      <h4 class="mb-0">Checkout: {{ $event->nama_event }}</h4>
    </div>
    <div class="card-body">
      @if ($errors->any())
        <div class="alert alert-danger">
          <ul class="mb-0">
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <form action="{{ route('ticket-orders.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="event_id" value="{{ $event->id }}">

        <div class="mb-3">
          <label for="quantity" class="form-label">Jumlah Tiket</label>
          <input type="number" name="quantity" id="quantity" class="form-control" value="1" min="1" required>
        </div>

        <div class="mb-3">
          <label for="payment_proof" class="form-label">Upload Bukti Transfer</label>
          <input type="file" name="payment_proof" id="payment_proof" class="form-control" accept="image/*" required>
          <small class="text-muted">Format gambar (JPG/PNG), maks 2MB.</small>
        </div>

        <button type="submit" class="btn btn-success">
          <i class="bi bi-check-circle"></i> Konfirmasi Pesanan
        </button>
        <a href="{{ route('shop.index') }}" class="btn btn-secondary">Batal</a>
      </form>
    </div>
  </div>
</div>
@endsection
