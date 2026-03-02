@extends('layouts.app')

@section('title', 'Masuk Ujian')

@section('content')
    <div class="container-fluid bg-ujian">
        <div class="row justify-content-center align-items-center min-vh-100">

            <div class="col-11 col-sm-8 col-md-7 col-lg-5 col-xl-4">


                <div class="card shadow-lg border-0 rounded-4">
                    <div class="card-body p-3 p-sm-4 p-md-5">


                        {{-- Icon --}}
                        <div class="text-center mb-2">
                            <div class="d-inline-flex align-items-center justify-content-center">
                                <img src="{{ asset('ico.png') }}" alt="UjianApp"
                                    style="width:100px;box-shadow: 5px 5px 15px rgba(0, 0, 0, 0.3);border-radius: 20px;">
                                {{-- <i class="bi bi-mortarboard-fill fs-3"></i> --}}
                            </div>
                        </div>


                        <h4 class="text-center fw-bold mb-2">Install Aplikasi Ujian</h4>
                        <p class="text-center text-secondary mb-3" style="font-size: 13px">
                            Silahkan install sesuai OS smartphone ya!!
                        </p>

                        <button id="btnFullscreen" onclick="masukLayarPenuh()" style="padding: 10px 20px; cursor: pointer;">
                            Tampilkan Layar Penuh
                        </button>

                    </div>
                </div>

            </div>

        </div>
    </div>

    <style>
        .bg-ujian {
            min-height: 100vh;
            background-color: #0d6efd;

            background-image:
                radial-gradient(circle, rgba(255, 255, 255, 0.15) 1px, transparent 1px);

            background-size: 22px 22px;
        }
    </style>

    <script>
        function masukLayarPenuh() {
            // Mengambil seluruh elemen halaman (HTML)
            var elem = document.documentElement;

            // Cek dukungan browser dan eksekusi
            if (elem.requestFullscreen) {
                elem.requestFullscreen();
            } else if (elem.webkitRequestFullscreen) {
                /* Safari & Chrome Mobile */
                elem.webkitRequestFullscreen();
            } else if (elem.msRequestFullscreen) {
                /* Internet Explorer / Edge Lama */
                elem.msRequestFullscreen();
            }

            // Opsional: Sembunyikan tombol setelah diklik agar layar bersih
            document.getElementById('btnFullscreen').style.display = 'none';
        }

        // Listener jika Anda ingin melakukan sesuatu saat siswa keluar dari fullscreen
        document.addEventListener('fullscreenchange', function() {
            if (!document.fullscreenElement) {
                console.log("User keluar dari mode fullscreen");
                // Di sini Anda bisa memunculkan tombol kembali jika perlu
                document.getElementById('btnFullscreen').style.display = 'block';
            }
        });
    </script>

    <script src="{{ asset('pwa-install.js') }}"></script>

@endsection
