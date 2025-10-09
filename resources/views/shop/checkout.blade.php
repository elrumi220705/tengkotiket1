@extends('layouts.app')

@section('title', 'Checkout')
@section('page-title', 'Checkout Tiket')

@push('styles')
<style>
  .checkout-wrap { max-width: 1000px; margin: 0 auto; }
  .bank-box {
    background:#f8fafc; border:1px solid #e6edf5; border-radius:12px; padding:16px;
  }
  .bank-box h6 { margin-bottom: 8px; font-weight:700; }
  .summary-card {
    border:1px solid #e6edf5; border-radius:12px; padding:16px; background:#fff;
    box-shadow: 0 4px 16px rgba(0,0,0,.04);
  }
  .total-line { display:flex; justify-content:space-between; align-items:center; }
  .total-amount { font-size:20px; font-weight:800; color:#0d6efd; }
</style>
@endpush

@section('content')
<div class="checkout-wrap mt-4">
  <div class="card shadow">
    <div class="card-header bg-primary text-white">
      <h4 class="mb-0">Checkout: {{ $event->nama_event }}</h4>
    </div>

    <div class="card-body">
      @if (session('ok'))
        <div class="alert alert-success">{{ session('ok') }}</div>
      @endif

      @if ($errors->any())
        <div class="alert alert-danger">
          <ul class="mb-0">
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <div class="row g-4">
        {{-- FORM --}}
        <div class="col-lg-7">
          <form action="{{ route('ticket-orders.store') }}" method="POST" enctype="multipart/form-data" id="checkoutForm">
            @csrf
            <input type="hidden" name="event_id" value="{{ $event->id }}">

            <div class="mb-3">
              <label for="quantity" class="form-label">Jumlah Tiket</label>
              <input
                type="number"
                name="quantity"
                id="quantity"
                class="form-control"
                value="{{ old('quantity', 1) }}"
                min="1"
                required
              >
            </div>

            <div class="mb-3">
              <label for="payment_proof" class="form-label">Upload Bukti Transfer</label>
              <input
                type="file"
                name="payment_proof"
                id="payment_proof"
                class="form-control"
                accept="image/jpeg,image/png,image/webp,application/pdf"
                required
              >
              <small class="text-muted">
                File: JPG/PNG/WebP/PDF, maks 2MB. Pastikan nominal & nama terlihat jelas.
              </small>
            </div>

            <div class="d-flex gap-2">
              <button type="submit" class="btn btn-success" id="submitBtn">
                <i class="bi bi-check-circle"></i> Kirim & Minta Verifikasi
              </button>
              <a href="{{ route('shop.show', $event) }}" class="btn btn-secondary">Batal</a>
            </div>
          </form>
        </div>

        {{-- RINGKASAN + INSTRUKSI --}}
        <div class="col-lg-5">
          <div class="summary-card mb-3">
            <div class="d-flex align-items-center gap-3 mb-3">
              @php
                $imageUrl = $event->gambar ? asset('storage/'.$event->gambar) : asset('images/default-event.jpg');
              @endphp
              <img src="{{ $imageUrl }}" alt="{{ $event->nama_event }}" style="width:80px;height:60px;object-fit:cover;border-radius:8px;">
              <div>
                <div class="fw-bold">{{ $event->nama_event }}</div>
                <div class="text-muted small">
                  <i class="bi bi-calendar"></i>
                  {{ \Carbon\Carbon::parse($event->tanggal_mulai)->translatedFormat('d F Y H:i') }}
                </div>
              </div>
            </div>

            <div class="total-line">
              <span class="text-muted">Harga dasar × jumlah</span>
              <span id="linePrice">Rp{{ number_format($event->harga_dasar,0,',','.') }} × <span id="qtyPreview">1</span></span>
            </div>
            <hr class="my-2">
            <div class="total-line">
              <span class="fw-semibold">Total yang ditransfer</span>
              <span class="total-amount" id="totalAmount">Rp{{ number_format($event->harga_dasar,0,',','.') }}</span>
            </div>
          </div>

          <div class="bank-box">
            <h6><i class="bi bi-bank"></i> Instruksi Pembayaran Manual</h6>
            <ol class="small mb-2">
              <li>Transfer total sebesar <strong id="totalInline">Rp{{ number_format($event->harga_dasar,0,',','.') }}</strong>.</li>
              <li>Bank: <strong>BCA</strong> — No. Rek: <strong>1234567890</strong> <strong>PT Ticketin ADT</strong>.</li>
              <li>Upload bukti transfer pada form, lalu klik <em>Kirim & Minta Verifikasi</em>.</li>
            </ol>
            <div class="small text-muted">
              Admin akan memverifikasi secara manual. Status pesanan bisa dilihat di menu <strong>Akun → Pesanan Saya</strong>.
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>

{{-- UX: hitung total + cegah double submit --}}
<script>
  (function(){
    const price = {{ (int)$event->harga_dasar }};
    const qtyEl = document.getElementById('quantity');
    const qtyPrev = document.getElementById('qtyPreview');
    const totalEl = document.getElementById('totalAmount');
    const totalInline = document.getElementById('totalInline');
    const form = document.getElementById('checkoutForm');
    const btn = document.getElementById('submitBtn');

    function rupiah(n){
      return 'Rp' + n.toLocaleString('id-ID');
    }

    function updateTotal(){
      const qty = Math.max(1, parseInt(qtyEl.value || '1', 10));
      qtyPrev.textContent = qty;
      const total = price * qty;
      totalEl.textContent = rupiah(total);
      totalInline.textContent = rupiah(total);
    }

    qtyEl.addEventListener('input', updateTotal);
    updateTotal();

    form?.addEventListener('submit', function(){
      if (btn){ btn.disabled = true; btn.innerHTML = 'Mengirim...'; }
    });
  })();
</script>
@endsection
