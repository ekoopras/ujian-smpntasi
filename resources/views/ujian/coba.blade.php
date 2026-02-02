@extends('layouts.app')

@section('title', 'Ujian Online')

@section('content')

    <form action="/ujian/submit" method="POST" id="formUjian">
        @csrf
        <input type="hidden" name="peserta_id" value="{{ $peserta->id }}">

        <div class="bg-soal">

            {{-- HEADER --}}
            <div class="ujian-header px-4 py-3 shadow-sm">
                <div class="container-fluid">
                    <div class="row align-items-center">

                        <div class="col-6 col-md-4 d-flex align-items-center gap-2">
                            <div class="logo-circle d-flex align-items-center justify-content-center">
                                <i class="bi bi-mortarboard-fill fs-4"></i>
                            </div>
                            <span class="fw-semibold d-none d-md-inline">UJIAN ONLINE</span>
                        </div>

                        <div class="col-6 col-md-4 text-end ms-auto">
                            <div class="fw-semibold">{{ $peserta->nama }}</div>
                            <small class="text-light opacity-75">{{ $peserta->kelas }}</small>
                        </div>

                    </div>
                </div>
            </div>

            {{-- BODY --}}
            <div class="container-fluid mt-4 pb-5">
                <div class="row justify-content-center">

                    {{-- SOAL --}}
                    <div class="col-12 col-lg-8">

                        @foreach ($soals as $index => $soal)
                            <div class="soal-item {{ $index === 0 ? '' : 'd-none' }}" data-index="{{ $index }}">

                                <div class="card shadow-sm border-0 rounded-4 mb-4">
                                    <div class="card-body p-3">

                                        <span class="badge bg-primary fs-5 px-3 py-2 mb-3">
                                            Soal {{ $index + 1 }}
                                        </span>

                                        <p class="lh-lg" style="font-size:18px">
                                            {!! nl2br(e($soal->soal)) !!}
                                        </p>

                                        @if ($soal->gambar)
                                            <img src="{{ asset('storage/' . $soal->gambar) }}" class="img-fluid rounded my-2"
                                                width="260">
                                        @endif

                                    </div>
                                </div>

                                {{-- JAWABAN --}}
                                @if ($soal->tipe_soal === 'multiple_choice')
                                    @foreach ($soal->multiple_choice as $opsi)
                                        <div class="card shadow-sm border-0 rounded-4 mb-2">
                                            <div class="card-body">
                                                <label class="form-check m-0">
                                                    <input class="form-check-input" type="radio"
                                                        name="jawaban[{{ $soal->id }}]" value="{{ $opsi['opsi'] }}">
                                                    <span class="form-check-label ms-2">
                                                        {{ $opsi['opsi'] }}. {{ $opsi['jawaban'] }}
                                                    </span>
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif

                            </div>
                        @endforeach

                    </div>

                    {{-- NOMOR SOAL DESKTOP --}}
                    <div class="col-lg-4 d-none d-lg-block">
                        <div class="card shadow-sm border-0 rounded-4">
                            <div class="card-body text-center">
                                <h6 class="fw-bold mb-3">Nomor Soal</h6>

                                <div class="d-flex flex-wrap justify-content-center gap-2">
                                    @foreach ($soals as $i => $s)
                                        <button type="button" class="btn btn-outline-primary nomor-soal-btn"
                                            onclick="goToSoal({{ $i }})">
                                            {{ $i + 1 }}
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </div>

        {{-- OFFCANVAS NOMOR SOAL (MOBILE) --}}
        <div class="offcanvas offcanvas-end d-lg-none offcanvas-soal" tabindex="-1" id="offcanvasNomorSoal">
            <div class="offcanvas-header">
                <h5 class="fw-bold">Nomor Soal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
            </div>

            <div class="offcanvas-body">
                <div class="d-flex flex-wrap gap-2 justify-content-center">
                    @foreach ($soals as $i => $s)
                        <button class="btn btn-outline-primary nomor-soal-btn" onclick="goToSoal({{ $i }})"
                            data-bs-dismiss="offcanvas">
                            {{ $i + 1 }}
                        </button>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- FOOTER --}}
        <div class="ujian-footer fixed-bottom bg-white border-top shadow-sm">
            <div class="container-fluid">
                <div class="row align-items-center py-2">

                    <div class="col-3 col-lg-4">
                        <button type="button" class="btn btn-danger w-100" onclick="prevSoal()">
                            <i class="bi bi-chevron-left"></i>
                        </button>
                    </div>

                    <div class="col-3 col-lg-4">
                        <button type="button" class="btn btn-warning w-100 fw-semibold">
                            Ragu
                        </button>
                    </div>

                    <div class="col-3 col-lg-4">
                        <button type="button" class="btn btn-success w-100" onclick="nextSoal()">
                            <i class="bi bi-chevron-right"></i>
                        </button>
                    </div>

                    <div class="col-3 d-lg-none">
                        <button class="btn btn-primary w-100" data-bs-toggle="offcanvas"
                            data-bs-target="#offcanvasNomorSoal">
                            Nomor
                        </button>
                    </div>

                </div>
            </div>
        </div>

        {{-- TIMER --}}
        <div class="timer-floating shadow">
            <span id="timerText">00:00</span>
        </div>

    </form>

    {{-- STYLE --}}
    <style>
        .bg-soal {
            background: #fafafa;
            min-height: 100vh;
        }

        .ujian-header {
            background: linear-gradient(135deg, #0d6efd, #0a58ca);
            color: #fff;
        }

        .logo-circle {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: rgba(255, 255, 255, .2);
        }

        .nomor-soal-btn {
            width: 48px;
            height: 48px;
            font-weight: 600;
        }

        .offcanvas-soal {
            --bs-offcanvas-width: 260px;
        }

        .timer-floating {
            position: fixed;
            bottom: 80px;
            left: 92%;
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
            z-index: 1055;
        }
    </style>

    {{-- SCRIPT --}}
    <script>
        let currentIndex = 0;
        const soalItems = document.querySelectorAll('.soal-item');

        function showSoal(index) {
            soalItems.forEach((el, i) => {
                el.classList.toggle('d-none', i !== index);
            });
            currentIndex = index;
        }

        function nextSoal() {
            if (currentIndex < soalItems.length - 1) {
                showSoal(currentIndex + 1);
            }
        }

        function prevSoal() {
            if (currentIndex > 0) {
                showSoal(currentIndex - 1);
            }
        }

        function goToSoal(index) {
            showSoal(index);
        }

        /* TIMER */
        let waktu = 90 * 60;
        setInterval(() => {
            let m = Math.floor(waktu / 60);
            let d = waktu % 60;
            document.getElementById('timerText').innerText =
                String(m).padStart(2, '0') + ':' + String(d).padStart(2, '0');
            if (waktu > 0) waktu--;
        }, 1000);
    </script>

@endsection
