@extends('layouts.app')

@section('title', 'Masuk Ujian')

@section('content')

    <div class="bg-soal">


        {{-- HEADER --}}
        <div class="ujian-header px-4 py-3 shadow-sm">
            <div class="container-fluid">
                <div class="row align-items-center">

                    {{-- KIRI : LOGO --}}
                    <div class="col-6 col-md-4 d-flex align-items-center gap-2">
                        <div class="logo-circle d-flex align-items-center justify-content-center">
                            <i class="bi bi-mortarboard-fill fs-4"></i>
                        </div>
                        <span class="fw-semibold d-none d-md-inline">UJIAN ONLINE</span>
                    </div>

                    {{-- TENGAH (KOSONG / FLEX) --}}
                    <div class="col-md-4 d-none d-md-block"></div>

                    {{-- KANAN : PESERTA --}}
                    <div class="col-6 col-md-4 text-end">
                        <div class="fw-semibold">Aya Rashfa Akila</div>
                        <small class="text-light opacity-75">Kelas X IPA 1</small>
                    </div>

                </div>
            </div>
        </div>


        {{-- SECTION SOAL --}}
        <div class="container-fluid mt-4" style="padding-bottom: 5rem;">
            <div class="row justify-content-center">

                {{-- KIRI : SOAL --}}
                <div class="col-12 col-lg-8 mb-3">

                    <div class="card shadow-sm border-0 rounded-4 mb-4">
                        <div class="card-body p-3">

                            {{-- Nomor & Pertanyaan --}}
                            <div class="mb-3">
                                <span class="badge bg-primary fs-5 px-2 py-2 mb-2">Soal 1</span>
                                <div class="m-2">
                                    <p class="lh-lg" style="font-size: 18px;">
                                        Disekseni para pandhereke yaiku Ngabei Rangga Panambang, Ngabei Kudana Warsa, lan
                                        Nitidana, Pangeran Samber Nyawa maringi jeneng papan mau “Mojogedhang”.
                                        Saka tetembungan Mojo utawa sedya tegese karep utawa pepinginan lan gedhene
                                        pepadhang tegese pituduh urip.

                                        Nganti seprene papan mau isih ana lan wektu iku Pangeran Samber Nyawa antuk
                                        pituduh nggayuh pepinginan lumantar anggunge manuk derkuku. Direwangi para
                                        pandhereke, Pangeran Samber Nyawa banjur mbudidaya ngoyak manuk derkuku mau
                                        nganti tekan padhepokane pendhekar wanita jenenge Nyai Dipa. Dheweke biyen
                                        dadi pejuwang negara nanging saiki seneng tapa.

                                        Pethikan crita iki saka legendha ...
                                    </p>
                                </div>

                            </div>

                        </div>
                    </div>


                    {{-- PILIHAN JAWABAN --}}
                    <div class="mt-3">

                        <div class="card shadow-sm border-0 rounded-4 mb-2">
                            <div class="card-body">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="jawaban" id="opsiA">
                                    <label class="form-check-label" for="opsiA">
                                        A. Bandung
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="card shadow-sm border-0 rounded-4 mb-2">
                            <div class="card-body">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="jawaban" id="opsiA">
                                    <label class="form-check-label" for="opsiA">
                                        B. Bandung
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="card shadow-sm border-0 rounded-4 mb-2">
                            <div class="card-body">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="jawaban" id="opsiA">
                                    <label class="form-check-label" for="opsiA">
                                        C. Bandung
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="card shadow-sm border-0 rounded-4 mb-2">
                            <div class="card-body">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="jawaban" id="opsiA">
                                    <label class="form-check-label" for="opsiA">
                                        D. Bandung
                                    </label>
                                </div>
                            </div>
                        </div>

                    </div>




                </div>

                {{-- KANAN : NOMOR SOAL DESKTOP --}}
                <div class="col-lg-4 d-none d-lg-block">
                    <div class="card shadow-sm border-0 rounded-4">
                        <div class="card-body p-3 text-center">

                            <h6 class="fw-bold mb-3">Nomor Soal</h6>

                            <div class="d-flex flex-wrap justify-content-center gap-2">
                                <button class="btn btn-outline-primary nomor-soal-btn">1</button>
                                <button class="btn btn-outline-primary nomor-soal-btn">2</button>
                                <button class="btn btn-outline-primary nomor-soal-btn">3</button>
                                <button class="btn btn-outline-primary nomor-soal-btn">4</button>
                                <button class="btn btn-outline-primary nomor-soal-btn">5</button>
                                <button class="btn btn-outline-primary nomor-soal-btn">5</button>
                                <button class="btn btn-outline-primary nomor-soal-btn">5</button>
                                <button class="btn btn-outline-primary nomor-soal-btn">5</button>
                            </div>

                        </div>
                    </div>
                </div>


            </div>
        </div>

    </div>

    <div class="offcanvas offcanvas-end d-lg-none offcanvas-soal" tabindex="-1" id="offcanvasNomorSoal">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title fw-bold">Nomor Soal</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
        </div>

        <div class="offcanvas-body">
            <div class="d-flex flex-wrap gap-2 justify-content-center">
                <button class="btn btn-outline-primary nomor-soal-btn">1</button>
                <button class="btn btn-outline-primary nomor-soal-btn">2</button>
                <button class="btn btn-outline-primary nomor-soal-btn">3</button>
                <button class="btn btn-outline-primary nomor-soal-btn">4</button>
                <button class="btn btn-outline-primary nomor-soal-btn">5</button>
            </div>
        </div>
    </div>


    {{-- FOOTER NAVIGASI --}}
    <div class="ujian-footer fixed-bottom bg-white border-top shadow-sm">
        <div class="container-fluid">
            <div class="row align-items-center py-2">

                {{-- PREV --}}
                <div class="col-3 col-lg-4">
                    <button class="btn btn-danger w-100">
                        <i class="bi bi-chevron-left"></i>
                    </button>
                </div>

                {{-- RAGU --}}
                <div class="col-3 col-lg-4">
                    <button class="btn btn-warning w-100 fw-semibold">
                        Ragu
                    </button>
                </div>

                {{-- NEXT --}}
                <div class="col-3 col-lg-4">
                    <button class="btn btn-success w-100">
                        <i class="bi bi-chevron-right"></i>
                    </button>
                </div>

                {{-- NOMOR (MOBILE ONLY) --}}
                <div class="col-3 d-lg-none">
                    <button class="btn btn-primary w-100" data-bs-toggle="offcanvas"
                        data-bs-target="#offcanvasNomorSoal">
                        Nomor
                    </button>
                </div>

            </div>
        </div>
    </div>

    {{-- TIMER FLOATING --}}
    <div class="timer-floating shadow">
        <span id="timerText">90:00</span>
    </div>





    <style>
        .bg-soal {
            background-color: #fafafa;
            min-height: 100vh;
        }

        /* .bg-soal {
                                                                                                min-height: 100vh;
                                                                                                background-color: #0d6efd;

                                                                                                background-image:
                                                                                                    radial-gradient(circle, rgba(255, 255, 255, 0.15) 1px, transparent 1px);

                                                                                                background-size: 22px 22px;
                                                                                            } */

        .offcanvas.offcanvas-soal {
            --bs-offcanvas-width: 250px;
        }



        .ujian-header {
            background: linear-gradient(135deg, #0d6efd, #0a58ca);
            color: #fff;
        }

        .logo-circle {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            color: #fff;
        }

        .timer-box {
            background: rgba(255, 255, 255, 0.25);
            font-size: 18px;
            letter-spacing: 1px;
        }

        .nomor-soal-btn {
            width: 48px;
            height: 48px;
            font-size: 16px;
            font-weight: 600;
            padding: 0;
        }

        .timer-floating {
            position: fixed;
            bottom: 80px;
            /* jarak dari footer */
            left: 95%;
            transform: translateX(-50%);
            width: 72px;
            height: 72px;
            border-radius: 50%;
            background: linear-gradient(135deg, #0d6efd, #0a58ca);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 16px;
            z-index: 1055;
            letter-spacing: 1px;
        }

        @media (max-width: 576px) {
            .timer-floating {
                left: 88%;
                width: 60px;
                height: 60px;
                font-size: 14px;
                bottom: 75px;
            }
        }
    </style>



@endsection
