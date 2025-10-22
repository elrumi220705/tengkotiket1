@extends('layouts.app')

@section('title', 'Contact Us')
@section('page-title', 'Contact Us')

@push('styles')
<link href="{{ asset('css/contact.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="contact-container">
    {{-- Header --}}
    <div class="contact-header text-center mb-5">
        <h1 class="contact-title fw-bold">Hubungi Kami</h1>
        <p class="contact-subtitle text-muted">
            Tim support kami siap membantu jika Anda memiliki pertanyaan atau ingin mengajukan refund tiket.
        </p>
    </div>

    {{-- Grid Informasi --}}
    <div class="contact-grid">
        <div class="contact-card">
            <div class="contact-icon bg-primary text-white">
                <i class="bi bi-envelope-fill"></i>
            </div>
            <h5 class="contact-label">Email</h5>
            <p class="contact-value">
                <a href="mailto:admin@ticketin.com" class="text-dark text-decoration-none">admin@ticketin.com</a>
            </p>
        </div>

        <div class="contact-card">
            <div class="contact-icon bg-success text-white">
                <i class="bi bi-whatsapp"></i>
            </div>
            <h5 class="contact-label">WhatsApp</h5>
            <p class="contact-value">
                <a href="https://wa.me/6281234567890?text=Halo%20Admin,%20saya%20ingin%20refund%20tiket"
                   target="_blank" class="text-dark text-decoration-none">
                   +62 838-0784-4386
                </a>
            </p>
        </div>

        <div class="contact-card">
            <div class="contact-icon bg-danger text-white">
                <i class="bi bi-geo-alt-fill"></i>
            </div>
            <h5 class="contact-label">Alamat</h5>
            <p class="contact-value">Jl. Pamayahan No.123, Indramayu</p>
        </div>
    </div>
</div>
@endsection
