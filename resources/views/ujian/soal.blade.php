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
                                {{-- <i class="bi bi-mortarboard-fill fs-4"></i> --}}
                                <img src="{{ asset('ico.png') }}" alt="UjianApp" style="width: 4rem">
                            </div>
                        </div>

                        <div class="col-6 col-md-4 text-end ms-auto">
                            <div class="fw-semibold">{{ $peserta->nama }}</div>
                            <small class="text-light opacity-75">{{ $peserta->kelase->kelas }}</small>
                        </div>

                    </div>
                </div>
            </div>

            {{-- BODY --}}
            <div class="container-fluid mt-4 mb-5 pb-5">
                <div class="row justify-content-center">

                    {{-- SOAL --}}
                    <div class="col-12 col-lg-8">

                        @foreach ($soals as $index => $soal)
                            <div class="soal-item {{ $index === 0 ? '' : 'd-none' }}" data-index="{{ $index }}"
                                data-soal-id="{{ $soal->id }}">

                                <div class="card shadow-sm border-0 rounded-4 mb-4">
                                    <div class="card-body p-3">

                                        <span class="badge bg-primary fs-5 px-3 py-2 mb-3">
                                            Soal {{ $index + 1 }}
                                        </span>

                                        <p class="lh-lg" style="font-size:18px">
                                            {!! nl2br(e($soal->soal)) !!}
                                        </p>

                                        @if ($soal->gambar)
                                            <img src="{{ asset('storage/' . $soal->gambar) }}"
                                                class="img-fluid rounded my-2" width="260">
                                        @endif

                                    </div>
                                </div>

                                {{-- JAWABAN --}}


                                {{-- JAWABAN --}}
                                @if ($soal->tipe_soal === 'multiple_choice')
                                    @foreach ($soal->multiple_choice ?? [] as $opsi)
                                        <div class="card shadow-sm border-0 rounded-4 mb-2">
                                            <div class="card-body">

                                                <label class="form-check m-0">
                                                    <input class="form-check-input" type="radio"
                                                        name="jawaban[{{ $soal->id }}][]" value="{{ $opsi['opsi'] }}">

                                                    <span class="form-check-label ms-2">
                                                        {{ $opsi['opsi'] }}. {{ $opsi['jawaban'] }}

                                                        @if (!empty($opsi['jawaban_img']))
                                                            <br>
                                                            <img src="{{ asset('storage/' . $opsi['jawaban_img']) }}"
                                                                width="120">
                                                        @endif
                                                    </span>
                                                </label>

                                            </div>
                                        </div>
                                    @endforeach
                                @endif

                                {{-- MATCHING --}}
                                @if ($soal->tipe_soal === 'matching')
                                    @php
                                        $kananList = collect($soal->matching)->pluck('kanan')->unique();
                                    @endphp

                                    <div class="card shadow-sm border-0 rounded-4">
                                        <div class="card-body">

                                            <div class="table-responsive">
                                                <table class="table table-bordered text-center align-middle">

                                                    {{-- HEADER --}}
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th class="text-start">Soal</th>
                                                            @foreach ($soal->kananList as $kanan)
                                                                <th>{{ $kanan }}</th>
                                                            @endforeach
                                                        </tr>
                                                    </thead>

                                                    {{-- BODY --}}
                                                    <tbody>
                                                        @foreach ($soal->matching as $i => $match)
                                                            <tr>
                                                                <td class="text-start fw-semibold">
                                                                    {{ $match['kiri'] }}
                                                                </td>

                                                                @foreach ($soal->kananList as $kanan)
                                                                    <td class="text-center">
                                                                        <input type="radio" class="form-check-input"
                                                                            name="jawaban[{{ $soal->id }}][{{ $i }}]"
                                                                            value="{{ $kanan }}" required>
                                                                    </td>
                                                                @endforeach
                                                            </tr>
                                                        @endforeach
                                                    </tbody>


                                                </table>
                                            </div>

                                        </div>
                                    </div>
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
                                            onclick="goToSoal({{ $i }})" data-soal="{{ $s->id }}">
                                            {{ $i + 1 }}
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <div id="lockScreen" class="lock-screen d-none">
                <div class="lock-box">
                    <h4>UJIAN TERKUNCI 🔒</h4>
                    <p>Anda meninggalkan halaman ujian</p>

                    <input type="password" id="unlockCode" maxlength="6" class="form-control text-center my-3"
                        placeholder="Kode 6 Digit" autocomplete="off" autocorrect="off" spellcheck="false">

                    <button type="button" class="btn btn-primary w-100" onclick="unlockUjian()">
                        Buka Kunci
                    </button>
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
                            data-bs-dismiss="offcanvas" data-soal="{{ $s->id }}" type="button">
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
                        <button type="button" class="btn btn-warning w-100 fw-semibold" onclick="toggleRagu()">
                            Ragu
                        </button>
                    </div>


                    <div class="col-3 col-lg-4" id="nextWrapper">
                        <button type="button" class="btn btn-success w-100" onclick="nextSoal()">
                            <i class="bi bi-chevron-right"></i>
                        </button>
                    </div>

                    <div class="col-3 col-lg-4 " id="submitWrapper">
                        <button type="submit" class="btn btn-success w-100 fw-semibold">
                            Selesai
                        </button>
                    </div>

                    <div class="col-3 d-lg-none">
                        <button class="btn btn-primary w-100" data-bs-toggle="offcanvas"
                            data-bs-target="#offcanvasNomorSoal" type="button">
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

        .lock-screen {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, .85);
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .lock-box {
            background: #fff;
            padding: 24px;
            border-radius: 16px;
            width: 320px;
            text-align: center;
        }
    </style>

    <script>
        const DURASI_MENIT = {{ $durasiMenit }};
    </script>


    {{-- SCRIPT --}}
    <script>
        let currentIndex = 0;
        const soalItems = document.querySelectorAll('.soal-item');

        function updateButtons() {
            const isLast = currentIndex === soalItems.length - 1;

            document.getElementById('nextWrapper')
                .classList.toggle('d-none', isLast);

            document.getElementById('submitWrapper')
                .classList.toggle('d-none', !isLast);
        }

        function showSoal(index) {
            soalItems.forEach((el, i) => {
                el.classList.toggle('d-none', i !== index);
            });

            currentIndex = index;
            updateButtons(); // 🔥 panggil di sini
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

        // initial load
        updateButtons();

        /* TIMER */
        let waktu = DURASI_MENIT * 60; // menit → detik

        const timerInterval = setInterval(() => {

            let menit = Math.floor(waktu / 60);
            let detik = waktu % 60;

            document.getElementById('timerText').innerText =
                String(menit).padStart(2, '0') + ':' + String(detik).padStart(2, '0');

            if (waktu <= 0) {
                clearInterval(timerInterval);

                // auto submit ketika waktu habis
                document.getElementById('formUjian').submit();
            }

            waktu--;
        }, 1000);
    </script>

    <script>
        document.addEventListener('change', function() {

            document.querySelectorAll('.soal-item').forEach(soal => {

                const soalId = soal.dataset.soalId;
                const inputs = soal.querySelectorAll('input, select');

                let answered = false;

                inputs.forEach(el => {
                    if ((el.type === 'radio' || el.type === 'checkbox') && el.checked) {
                        answered = true;
                    }
                    if (el.tagName === 'SELECT' && el.value !== '') {
                        answered = true;
                    }
                });

                const nomorButtons = document.querySelectorAll(
                    `.nomor-soal-btn[data-soal="${soalId}"]`
                );

                nomorButtons.forEach(btn => {
                    btn.classList.toggle('btn-success', answered);
                    btn.classList.toggle('btn-outline-primary', !answered);
                });

            });

        });
    </script>


    <script>
        function toggleRagu() {

            const soal = document.querySelector('.soal-item:not(.d-none)');
            if (!soal) return;

            const soalId = soal.dataset.soalId;
            const isRagu = soal.dataset.ragu === "1";

            // toggle status ragu
            soal.dataset.ragu = isRagu ? "0" : "1";

            // ambil SEMUA tombol nomor (desktop + mobile)
            const nomorButtons = document.querySelectorAll(
                `.nomor-soal-btn[data-soal="${soalId}"]`
            );

            nomorButtons.forEach(btn => {

                btn.classList.remove(
                    'btn-success',
                    'btn-outline-primary',
                    'btn-warning'
                );

                if (!isRagu) {
                    // jadi ragu
                    btn.classList.add('btn-warning');
                } else {
                    // batal ragu → balik ke default / dijawab
                    btn.classList.add('btn-outline-primary');
                }
            });
        }
    </script>

    <script>
        function showLockScreen() {
            document.getElementById('lockScreen').classList.remove('d-none');
        }
    </script>

    <script>
        let ujianLocked = false;

        // ketika tab ditinggalkan
        document.addEventListener("visibilitychange", function() {
            if (document.hidden) {
                lockUjian();
            }
        });

        // ketika window blur (alt+tab, minimize)
        window.addEventListener("blur", function() {
            lockUjian();
        });

        function lockUjian() {
            if (ujianLocked) return;

            ujianLocked = true;

            fetch("/ujian/lock", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({
                    peserta_id: {{ $peserta->id }}
                })
            });

            showLockScreen();
        }
    </script>

    {{-- <script>
        function unlockUjian() {
            const code = document.getElementById('unlockCode').value;

            fetch("/ujian/unlock", {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({
                        peserta_id: {{ $peserta->id }},
                        code: code
                    })
                })
                .then(res => {
                    if (res.ok) {
                        ujianLocked = false;
                        document.getElementById('lockScreen').classList.add('d-none');
                    } else {
                        alert("Kode salah!");
                    }
                });
        }
    </script> --}}
    <script>
        function unlockUjian() {
            const input = document.getElementById('unlockCode');
            const code = input.value;

            fetch("/ujian/unlock", {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({
                        peserta_id: {{ $peserta->id }},
                        code: code
                    })
                })
                .then(res => {
                    if (res.ok) {
                        ujianLocked = false;

                        // 🔥 KOSONGKAN INPUT
                        input.value = "";

                        document.getElementById('lockScreen').classList.add('d-none');
                    } else {
                        // 🔥 JUGA KOSONGKAN JIKA SALAH
                        input.value = "";
                        alert("Kode salah!");
                    }
                });
        }
    </script>

@endsection
