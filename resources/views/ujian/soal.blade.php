@extends('layouts.app')

@section('title', 'Ujian Online')

@section('content')

    {{-- <form action="/ujian/submit" method="POST" id="formUjian"> --}}
    <form id="formUjian" action="{{ route('ujian.submit') }}" method="POST">
        @csrf
        <input type="hidden" name="peserta_id" value="{{ $peserta->id }}">
        <input type="hidden" name="ujian_id" value="{{ $ujian->id }}">

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
                                data-soal-id="{{ $soal->id }}" {{-- Tambahkan ini untuk menyimpan status ragu di level elemen --}}
                                data-ragu="{{ isset($raguTerarsip[$soal->id]) && $raguTerarsip[$soal->id] ? '1' : '0' }}">

                                <div class="card shadow-sm border-0 rounded-4 mb-4">
                                    <div class="card-body p-3">

                                        <span class="badge bg-primary fs-5 px-3 py-2 mb-3">
                                            Soal {{ $index + 1 }}
                                        </span>

                                        {{-- <p class="lh-lg" style="font-size:18px">
                                            {!! nl2br(e($soal->soal)) !!}
                                        </p> --}}

                                        {{-- Teks Soal dengan RichEditor dan styling Bootstrap --}}
                                        <div class="lh-lg" style="font-size: 16px;">
                                            {!! $soal->soal !!}
                                        </div>

                                        @if ($soal->gambar)
                                            <img src="{{ asset('storage/' . $soal->gambar) }}"
                                                class="img-fluid rounded my-2" width="260">
                                        @endif

                                    </div>
                                </div>

                                {{-- JAWABAN --}}


                                {{-- JAWABAN --}}
                                {{-- @if ($soal->tipe_soal === 'multiple_choice')
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
                                @endif --}}

                                {{-- JAWABAN --}}
                                @if ($soal->tipe_soal === 'multiple_choice')
                                    @foreach ($soal->multiple_choice ?? [] as $opsi)
                                        <div class="card shadow-sm border-0 rounded-4 mb-2">
                                            <div class="card-body">
                                                <label class="form-check m-0">
                                                    <input class="form-check-input" type="radio"
                                                        name="jawaban[{{ $soal->id }}][]" value="{{ $opsi['opsi'] }}"
                                                        {{-- TAMBAHKAN KODE DI BAWAH INI --}}
                                                        @if (isset($jawabanTerarsip[$soal->id]) && in_array($opsi['opsi'], (array) $jawabanTerarsip[$soal->id])) checked @endif>

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
                                {{-- MATCHING --}}
                                @if ($soal->tipe_soal === 'matching')
                                    <div class="card shadow-sm border-0 rounded-4">
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="table table-bordered align-middle">
                                                    {{-- HEADER: Hanya perlu 2 kolom --}}
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th class="text-start" style="width: 40%;">Soal</th>
                                                            <th class="text-start">Pasangan Jawaban</th>
                                                        </tr>
                                                    </thead>

                                                    {{-- BODY --}}
                                                    <tbody>
                                                        @foreach ($soal->matching as $i => $match)
                                                            <tr>
                                                                <td class="text-start fw-semibold">
                                                                    {{ $match['kiri'] }}
                                                                </td>
                                                                <td class="text-start">
                                                                    <select class="form-select select-autosave"
                                                                        name="jawaban[{{ $soal->id }}][{{ $i }}]"
                                                                        data-soal-id="{{ $soal->id }}" required>
                                                                        <option value="">-- Pilih Pasangan --</option>
                                                                        @foreach ($soal->kananList as $kanan)
                                                                            <option value="{{ $kanan }}"
                                                                                @if (isset($jawabanTerarsip[$soal->id][$i]) && $jawabanTerarsip[$soal->id][$i] === $kanan) selected @endif>
                                                                                {{ $kanan }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                </td>
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

            <div id="lockScreen" class="lock-screen" style="display: none;">
                <div class="lock-box">
                    <h4>UJIAN TERKUNCI 🔒</h4>
                    <p>Anda meninggalkan halaman ujian</p>

                    <input type="password" id="unlockCode" maxlength="6" class="form-control text-center my-3"
                        placeholder="Kode 6 Digit" autocomplete="off" autocorrect="off" spellcheck="false"
                        onkeypress="handleUnlockKey(event)">

                    <div id="msgError" class="text-danger mb-3" style="display:none; font-weight:bold;"></div>

                    <button type="button" class="btn btn-primary w-100" onclick="unlockUjian()">
                        Buka Kunci
                    </button>
                </div>
            </div>


        </div>

        {{-- OFFCANVAS NOMOR SOAL (MOBILE) --}}
        <div id="modalNomor" class="custom-overlay" style="display: none;">
            <div class="overlay-content">
                <div class="header-modal d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold m-0" style="color: #333;">Pilih Nomor Soal</h5>
                    <button type="button" class="btn-close" onclick="tutupModalNomor()"
                        style="background-color: #eee; border: none; padding: 5px 10px; border-radius: 5px;"></button>
                </div>

                <div class="d-flex flex-wrap justify-content-center" style="gap: 10px; display: flex; flex-wrap: wrap;">
                    @foreach ($soals as $i => $s)
                        <button type="button" class="btn btn-outline-primary nomor-soal-btn"
                            id="btn-nomor-{{ $i }}" onclick="pindahSoalDariModal({{ $i }})"
                            style="width: 50px; height: 50px; margin: 5px; font-weight: bold; border: 1px solid #0d6efd;">
                            {{ $i + 1 }}
                        </button>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- FOOTER --}}
        <div class="ujian-footer fixed-bottom bg-white border-top shadow-sm" style="padding:10px 0 15px 0">
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
                        <button type="button" class="btn btn-primary w-100" onclick="bukaMenuNomor()">
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


        .custom-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            z-index: 10001;
            /* Pastikan di atas lock screen jika perlu */
            display: none;
            overflow-y: auto;
            /* Biar bisa discroll kalau soalnya banyak */
        }

        .overlay-content {
            background: #ffffff;
            width: 90%;
            margin: 50px auto;
            /* Jarak dari atas */
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.5);
            position: relative;
            min-height: 200px;
            /* Pastikan ada tinggi minimal */
        }

        /* Memastikan tombol nomor terlihat jelas */
        .nomor-soal-btn {
            display: inline-block !important;
            text-align: center;
            vertical-align: middle;
            cursor: pointer;
            background-color: transparent;
            color: #0d6efd;
        }


        .timer-floating {
            position: fixed;
            bottom: 100px;
            left: 88%;
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
            /* Ganti inset: 0 menjadi manual agar terbaca browser lama */
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.85);
            z-index: 9999;
        }

        .lock-box {
            background: #fff;
            padding: 24px;
            border-radius: 16px;
            width: 320px;
            text-align: center;
            color: #333;
            /* Pastikan teks tidak putih */

            /* Trik posisi tengah paling stabil untuk Android Jadul */
            position: absolute;
            top: 50%;
            left: 50%;
            -webkit-transform: translate(-50%, -50%);
            /* Untuk Chrome lama */
            -ms-transform: translate(-50%, -50%);
            transform: translate(-50%, -50%);
        }
    </style>



    <script>
        // ------- JS TIMMER ------- //

        // 1. Gunakan var (Chrome v55 tidak stabil dengan const/let)
        // PHP mengirim timestamp (detik), JS butuh milidetik (dikali 1000)
        var targetSelesai = {{ $waktuSelesai }} * 1000;

        function jalankanTimer() {
            // 2. Ambil waktu sekarang dari sistem
            var sekarang = new Date().getTime();
            var sisaWaktu = targetSelesai - sekarang;

            // 3. Jika waktu habis
            if (sisaWaktu <= 0) {
                clearInterval(intervalUjian);
                document.getElementById('timerText').innerHTML = "00:00";

                // Auto submit
                document.getElementById('formUjian').submit();
                return;
            }

            // 4. Hitung menit dan detik secara manual
            var menit = Math.floor((sisaWaktu % (1000 * 60 * 60)) / (1000 * 60));
            var detik = Math.floor((sisaWaktu % (1000 * 60)) / 1000);

            // 5. Format 00:00 (Browser lama tidak dukung .padStart)
            var tampilMenit = menit < 10 ? "0" + menit : menit;
            var tampilDetik = detik < 10 ? "0" + detik : detik;

            // 6. Update tampilan
            document.getElementById('timerText').innerHTML = tampilMenit + ":" + tampilDetik;
        }

        // Jalankan setiap 1 detik
        var intervalUjian = setInterval(function() {
            jalankanTimer();
        }, 1000);

        // Jalankan langsung saat halaman load agar tidak ada delay 1 detik
        jalankanTimer();

        // Tambahan agar saat layar menyala kembali, timer langsung update
        document.addEventListener('visibilitychange', function() {
            if (document.visibilityState === 'visible') {
                // Panggil fungsi update timer secara instan tanpa menunggu detik berikutnya
                jalankanTimer();
            }
        });
    </script>

    <script>
        // -------- JS JAWAB SOAL ----- //
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
        // ----- JS TOMBOL RAGU-RAGU ----- //
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

    {{-- <script>
        // ---- JS LOCK UJIAN ----- ///
        // 1. Fungsi menampilkan lock screen (Gunakan .style agar lebih stabil)
        function showLockScreen() {
            var ls = document.getElementById('lockScreen');
            if (ls) ls.style.display = 'block';
        }

        // 2. Logika Deteksi Pelanggaran
        //var ujianLocked = false;
        var ujianLocked = {{ $is_locked ? 'true' : 'false' }};

        // Tambahkan ini tepat di bawah variabel ujianLocked
        document.addEventListener("DOMContentLoaded", function() {
            if (ujianLocked) {
                showLockScreen();
            }
        });

        document.addEventListener("visibilitychange", function() {
            if (document.hidden) {
                lockUjian();
            }
        });

        window.addEventListener("blur", function() {
            lockUjian();
        });

        function lockUjian() {
            if (ujianLocked) return;
            ujianLocked = true;

            // AJAX menggunakan fetch (masih didukung Chrome 55, tapi gunakan var)
            fetch("/ujian/lock", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    "Content-Type": "application/json",
                },
                body: JSON.stringify({
                    peserta_id: {{ $peserta->id }}
                })
            });

            showLockScreen();
        }

        // 3. Fungsi menangani tombol ENTER pada input kode
        function handleUnlockKey(e) {
            var key = e.keyCode || e.which;
            if (key === 13) {
                if (e.preventDefault) e.preventDefault(); // Stop submit form utama
                unlockUjian();
                return false;
            }
        }

        function unlockUjian() {
            var input = document.getElementById('unlockCode');
            var code = input.value;

            if (code === "") {
                alert("Masukkan kode!");
                return;
            }

            fetch("/ujian/unlock", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "Accept": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    },
                    body: JSON.stringify({
                        peserta_id: {{ $peserta->id }},
                        code: code
                    })
                })
                .then(function(res) {
                    if (res.ok) { // res.ok mencakup status 200-299
                        return res.json();
                    } else {
                        throw new Error("Gagal");
                    }
                })
                .then(function(data) {
                    // Jika sampai sini, berarti PIN BENAR
                    ujianLocked = false;
                    document.getElementById('unlockCode').value = "";
                    document.getElementById('lockScreen').style.display = 'none';

                    // Reload halaman agar is_locked yang 0 dari DB terbaca oleh Laravel
                    window.location.reload();
                })
                .catch(function(err) {
                    document.getElementById('unlockCode').value = "";
                    alert("Kode Salah atau Gagal Terhubung!");
                });
        }
    </script> --}}

    <script>
        // 1. Inisialisasi Status dari Laravel
        var ujianLocked = {{ $is_locked ? 'true' : 'false' }};
        var isExiting = false; // Flag untuk mendeteksi refresh agar tidak re-lock

        // 2. Fungsi Menampilkan Overlay Lock
        function showLockScreen() {
            var ls = document.getElementById('lockScreen');
            if (ls) ls.style.display = 'block';
        }

        // 3. Deteksi Jika Halaman Sedang Reload (F5) atau Berpindah
        window.addEventListener('beforeunload', function() {
            isExiting = true;
        });

        // 4. Jalankan Lock Screen jika status dari DB adalah Locked
        document.addEventListener("DOMContentLoaded", function() {
            if (ujianLocked) {
                showLockScreen();
            }
        });

        // 5. Fungsi Mengunci Ujian (Kirim ke Server)
        function lockUjian() {
            // JANGAN LOCK jika: sedang proses refresh (isExiting) atau sudah terkunci
            if (isExiting || ujianLocked) return;

            ujianLocked = true;

            fetch("/ujian/lock", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    "Content-Type": "application/json",
                },
                body: JSON.stringify({
                    peserta_id: {{ $peserta->id }}
                })
            });

            showLockScreen();
        }

        // 6. Listener Deteksi Kecurangan (Pindah Tab/Minimize)
        // Diberi delay 300ms untuk memastikan ini bukan proses Refresh
        window.addEventListener("blur", function() {
            setTimeout(function() {
                if (!isExiting) lockUjian();
            }, 300);
        });

        document.addEventListener("visibilitychange", function() {
            if (document.hidden) {
                setTimeout(function() {
                    if (!isExiting) lockUjian();
                }, 300);
            }
        });

        // 7. Fungsi Menangani Tombol ENTER pada input PIN
        function handleUnlockKey(e) {
            var key = e.keyCode || e.which;
            if (key === 13) {
                if (e.preventDefault) e.preventDefault();
                unlockUjian();
                return false;
            }
        }

        // 8. Fungsi Buka Kunci (Unlock)
        function unlockUjian() {
            var input = document.getElementById('unlockCode');
            var code = input.value;

            if (code === "") {
                alert("Masukkan kode!");
                return;
            }

            fetch("/ujian/unlock", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "Accept": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    },
                    body: JSON.stringify({
                        peserta_id: {{ $peserta->id }},
                        code: code
                    })
                })
                .then(function(res) {
                    if (res.status === 200) {
                        // Berhasil Unlock!
                        isExiting = true; // Tandai agar saat reload tidak kena lock lagi
                        ujianLocked = false;
                        input.value = "";
                        document.getElementById('lockScreen').style.display = 'none';

                        // Reload halaman untuk sinkronisasi data soal
                        window.location.reload();
                    } else {
                        input.value = "";
                        alert("Kode Salah!");
                    }
                })
                .catch(function(err) {
                    alert("Gagal terhubung ke server. Cek koneksi!");
                });
        }
    </script>

    <script>
        // --- VARIABEL GLOBAL ---
        var currentIndex = 0;
        var soalItems = document.querySelectorAll('.soal-item');

        // --- FUNGSI NAVIGASI (Yang sudah kita bahas sebelumnya) ---
        function showSoal(index) {
            /* ... kode fungsi showSoal Anda ... */
        }

        document.addEventListener('change', function(e) {
            var target = e.target;

            // Memastikan yang diklik adalah pilihan jawaban (input radio atau select)
            if (target.name && target.name.indexOf('jawaban') !== -1) {

                var soalContainer = target.closest('.soal-item');
                var soalId = soalContainer.getAttribute('data-soal-id');
                var pesertaId = "{{ $peserta->id }}";

                var dataJawaban = {};

                // 1. AMBIL DATA DARI RADIO BUTTON (Untuk Multiple Choice)
                var inputs = soalContainer.querySelectorAll('input:checked');
                for (var i = 0; i < inputs.length; i++) {
                    var input = inputs[i];
                    var nameParts = input.name.split('[');
                    if (nameParts.length > 2) {
                        var subIndex = nameParts[2].replace(']', '');
                        if (subIndex === '') {
                            if (!Array.isArray(dataJawaban)) dataJawaban = [];
                            dataJawaban.push(input.value);
                        } else {
                            if (typeof dataJawaban !== 'object' || Array.isArray(dataJawaban)) dataJawaban = {};
                            dataJawaban[subIndex] = input.value;
                        }
                    }
                }

                // 2. AMBIL DATA DARI SELECT DROPDOWN (Untuk Matching)
                var selects = soalContainer.querySelectorAll('select');
                if (selects.length > 0) {
                    if (typeof dataJawaban !== 'object' || Array.isArray(dataJawaban)) dataJawaban = {};
                    for (var j = 0; j < selects.length; j++) {
                        var sel = selects[j];
                        var selNameParts = sel.name.split('[');
                        if (selNameParts.length > 2 && sel.value !== "") {
                            var selSubIndex = selNameParts[2].replace(']', '');
                            dataJawaban[selSubIndex] = sel.value;
                        }
                    }
                }

                $.ajax({
                    url: "{{ route('ujian.autosave') }}",
                    method: "POST",
                    dataType: "json", // Pastikan menerima respon JSON
                    data: {
                        _token: "{{ csrf_token() }}", // Token keamanan Laravel
                        peserta_id: pesertaId,
                        soal_id: soalId,
                        jawaban: dataJawaban
                    },
                    success: function(response) {
                        console.log("Auto-save berhasil (jQuery):", dataJawaban);
                        if (typeof updateWarnaNomor === 'function') {
                            updateWarnaNomor();
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Gagal simpan otomatis (jQuery):", error);
                        // Alert sederhana untuk testing di Android lama
                        // alert("Koneksi tidak stabil, jawaban gagal terkirim!");
                    }
                });
            }
        });

        // --- FUNGSI UPDATE WARNA (Wajib ada agar nomor berubah warna saat diklik) ---
        function updateWarnaNomor() {
            /* ... kode fungsi updateWarnaNomor yang menggunakan localStorage tadi ... */
        }

        // Jalankan saat pertama kali buka agar jawaban dari database langsung mewarnai nomor
        window.onload = function() {
            updateWarnaNomor();
        };
    </script>

    <script>
        // --- 1. INISIALISASI (Hanya satu kali) ---
        var currentIndex = 0;
        var soalItems = document.querySelectorAll('.soal-item');

        // --- 2. FUNGSI NAVIGASI UTAMA ---
        function showSoal(index) {
            // Pastikan index tidak keluar batas
            if (index < 0 || index >= soalItems.length) return;

            for (var i = 0; i < soalItems.length; i++) {
                if (i === index) {
                    soalItems[i].style.display = 'block';
                    // Paksa hapus d-none agar muncul
                    soalItems[i].className = soalItems[i].className.replace(/\bd-none\b/g, "");
                } else {
                    soalItems[i].style.display = 'none';
                }
            }
            currentIndex = index;
            updateButtons();
            window.scrollTo(0, 0);
        }

        function updateButtons() {
            var isLast = currentIndex === soalItems.length - 1;
            var nWrap = document.getElementById('nextWrapper');
            var sWrap = document.getElementById('submitWrapper');

            if (nWrap) nWrap.style.display = isLast ? 'none' : 'block';
            if (sWrap) sWrap.style.display = !isLast ? 'none' : 'block';
        }

        function nextSoal() {
            showSoal(currentIndex + 1);
        }

        function prevSoal() {
            showSoal(currentIndex - 1);
        }

        function goToSoal(index) {
            showSoal(index);
        }

        // --- 3. FUNGSI MODAL NOMOR (OVERLAY) ---
        function bukaMenuNomor() {
            var modal = document.getElementById('modalNomor');
            if (modal) {
                modal.style.display = 'block';
            }
        }

        function tutupModalNomor() {
            var modal = document.getElementById('modalNomor');
            if (modal) {
                modal.style.display = 'none';
            }
        }

        function pindahSoalDariModal(index) {
            showSoal(index); // Pindah soal
            tutupModalNomor(); // Tutup popup
        }

        // Jalankan pertama kali saat halaman dimuat
        updateButtons();

        // --- FUNGSI UPDATE SEMUA WARNA NOMOR ---
        function updateWarnaNomor() {
            var items = document.querySelectorAll('.soal-item');
            // Ambil status ragu dari localStorage
            var storageRagu = JSON.parse(localStorage.getItem('ragu_status') || '{}');

            for (var i = 0; i < items.length; i++) {
                var soal = items[i];
                var soalId = soal.getAttribute('data-soal-id');
                var btn = document.getElementById('btn-nomor-' + i); // Pastikan ID tombol nomor sesuai

                if (!btn) continue;

                // 1. Cek Status Ragu (Prioritas Warna Kuning)
                var isRagu = storageRagu[soalId] === true || soal.getAttribute('data-ragu') === "1";

                // 2. Cek Apakah Sudah Dijawab
                var sudahDijawab = false;

                // Cek jika ada Radio Button yang dicentang (Multiple Choice)
                var inputs = soal.querySelectorAll('input[type="radio"], input[type="checkbox"]');
                for (var j = 0; j < inputs.length; j++) {
                    if (inputs[j].checked) {
                        sudahDijawab = true;
                        break;
                    }
                }

                // Cek jika ada Select Dropdown yang dipilih (Matching)
                // Kita anggap dijawab jika SEMUA select di soal tersebut sudah punya nilai (bukan empty string)
                var selects = soal.querySelectorAll('select');
                if (selects.length > 0) {
                    var allSelected = true;
                    for (var k = 0; k < selects.length; k++) {
                        if (selects[k].value === "") {
                            allSelected = false;
                            break;
                        }
                    }
                    // Jika ada minimal satu yang diisi, kita anggap mulai dijawab (warna biru)
                    // Atau jika ingin harus semua terisi baru biru, gunakan 'allSelected'
                    if (allSelected) sudahDijawab = true;
                }

                // 3. Terapkan Warna ke Tombol Navigasi
                if (isRagu) {
                    btn.style.backgroundColor = "#ffc107"; // Kuning
                    btn.style.color = "#000";
                    btn.style.borderColor = "#ffc107";
                } else if (sudahDijawab) {
                    btn.style.backgroundColor = "#0d6efd"; // Biru
                    btn.style.color = "#fff";
                    btn.style.borderColor = "#0d6efd";
                } else {
                    btn.style.backgroundColor = "transparent"; // Kosong
                    btn.style.color = "#0d6efd";
                    btn.style.borderColor = "#0d6efd";
                }
            }
        }

        // --- FUNGSI TOMBOL RAGU ---
        function toggleRagu() {
            var soalAktif = soalItems[currentIndex];
            if (!soalAktif) return;

            var statusRaguSaatIni = soalAktif.getAttribute('data-ragu');

            // Toggle status (1 jadi 0, null/0 jadi 1)
            if (statusRaguSaatIni === "1") {
                soalAktif.setAttribute('data-ragu', "0");
            } else {
                soalAktif.setAttribute('data-ragu', "1");
            }

            // Update warna tombol nomor setelah status berubah
            updateWarnaNomor();
        }

        // --- MONITOR PERUBAHAN JAWABAN ---
        // Setiap kali siswa klik jawaban, update warna nomor
        document.addEventListener('change', function() {
            updateWarnaNomor();
        });

        // Panggil sekali saat pertama kali halaman dimuat
        updateWarnaNomor();

        function pindahSoalDariModal(index) {
            showSoal(index);
            tutupModalNomor();
            updateWarnaNomor(); // Pastikan sinkron
        }
    </script>




@endsection
