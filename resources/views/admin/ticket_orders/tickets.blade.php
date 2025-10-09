@extends('admin.layouts.app')

@section('content')
<div class="card">
  <div class="card-header">
    Tickets — Order #{{ $ticketOrder->id }} ({{ $ticketOrder->user->name }}) — {{ $ticketOrder->event->nama_event }}
  </div>
  <div class="card-body">
    @if($ticketOrder->tickets->isEmpty())
      <div class="alert alert-info">Belum ada tiket (order belum paid).</div>
    @else
      <div class="row g-3">
        @foreach($ticketOrder->tickets as $t)
          <div class="col-md-3">
            <div class="card h-100">
              <div class="card-body text-center">
                @if($t->qr_path)
                  <img src="{{ asset('storage/'.$t->qr_path) }}" alt="QR {{ $t->code }}" class="img-fluid" style="max-height:220px;">
                @endif
                <div class="mt-2 small">Kode: <code>{{ $t->code }}</code></div>
                <div>
                  @if($t->used_at)
                    <span class="badge bg-secondary">Used</span>
                  @else
                    <span class="badge bg-success">Valid</span>
                  @endif
                </div>
              </div>
            </div>
          </div>
        @endforeach
      </div>
    @endif
  </div>
</div>
@endsection
