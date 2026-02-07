@extends('layouts.app')

@section('content')
    <div class="container text-center mt-5">
        <h2 class="fw-bold">📱 Install Aplikasi Ujian</h2>
        <p class="text-muted mt-2">
            Pasang aplikasi agar ujian lebih fokus dan stabil
        </p>

        <button id="btnInstall" class="btn btn-primary btn-lg mt-4 d-none">
            Install Aplikasi
        </button>

        <div id="manualHint" class="mt-4 d-none">
            <p class="text-muted">
                Jika tombol install tidak muncul:
            </p>
            <ul class="text-start d-inline-block">
                <li>Chrome Android → ⋮ → Install App</li>
                <li>Safari iPhone → Share → Add to Home Screen</li>
            </ul>
        </div>
    </div>
@endsection
