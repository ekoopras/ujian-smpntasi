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
                            <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center"
                                style="width:64px;height:64px;">
                                <i class="bi bi-mortarboard-fill fs-3"></i>
                            </div>
                        </div>


                        <h4 class="text-center fw-bold mb-2">Install Aplikasi Ujian</h4>
                        <p class="text-center text-secondary mb-3" style="font-size: 13px">
                            Silahkan install sesuai OS smartphone ya!!
                        </p>



                        <div class="accordion" id="accordionPanelsStayOpenExample">

                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed flex-column text-center" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseTwo"
                                        aria-expanded="false" aria-controls="panelsStayOpen-collapseTwo">

                                        <i class="bi bi-android2 fs-1 text-success mb-1"></i>
                                        <span>Install Android</span>

                                    </button>
                                </h2>


                                <div id="panelsStayOpen-collapseTwo" class="accordion-collapse collapse">
                                    <div class="accordion-body">

                                        <div class="d-grid">
                                            <!-- BUTTON INSTALL -->
                                            <button id="pwa-install-btn" type="button" class="btn btn-success">
                                                Install Aplikasi
                                            </button>


                                            <div class="alert alert-secondary small mt-3 mb-0">
                                                <b>Jika tombol install tidak muncul atau gagal install </b>
                                            </div>

                                            <!-- STEP LIST -->
                                            <ol class="list-group list-group-numbered text-start small">
                                                <li class="list-group-item border-0 px-0">
                                                    Ketuk menu <b>⋮</b> di pojok kanan atas browser
                                                </li>
                                                <li class="list-group-item border-0 px-0">
                                                    Pilih <b>Tambahkan ke layar utama</b>
                                                </li>
                                                <li class="list-group-item border-0 px-0">
                                                    Tekan <b>Tambah</b>
                                                </li>
                                            </ol>
                                        </div>

                                    </div>
                                </div>

                            </div>

                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed flex-column text-center" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseThree"
                                        aria-expanded="false" aria-controls="panelsStayOpen-collapseThree">

                                        <i class="bi bi-apple fs-1 mb-1"></i>
                                        <span>Install iOS</span>

                                    </button>

                                </h2>
                                <div id="panelsStayOpen-collapseThree" class="accordion-collapse collapse">
                                    <div class="accordion-body">
                                        <ol class="list-group list-group-numbered small">
                                            <li class="list-group-item border-0 px-0">
                                                Buka halaman ini menggunakan <b>Safari</b>
                                            </li>
                                            <li class="list-group-item border-0 px-0">
                                                Tekan tombol <b>Share</b>
                                                <i class="bi bi-box-arrow-up ms-1"></i>
                                                di bagian bawah layar
                                            </li>
                                            <li class="list-group-item border-0 px-0">
                                                Pilih <b>Add to Home Screen</b>
                                            </li>
                                            <li class="list-group-item border-0 px-0">
                                                Tekan <b>Add</b>
                                            </li>
                                        </ol>

                                        <div class="alert alert-secondary small mt-3 mb-0">
                                            📌 Pastikan menggunakan Safari, bukan Chrome.
                                        </div>

                                    </div>

                                </div>
                            </div>

                        </div>



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

    <script src="{{ asset('pwa-install.js') }}"></script>

@endsection
