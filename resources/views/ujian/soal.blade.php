@extends('layouts.app')

@section('content')
    <form action="/ujian/submit" method="POST" id="formUjian">
        @csrf
        <input type="hidden" name="peserta_id" value="{{ $peserta->id }}">

        {{-- HEADER --}}
        <div class="d-flex justify-content-between align-items-center mb-4 p-3 border rounded">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-mortarboard-fill fs-3 text-primary"></i>
                <strong>Ujian Online</strong>
            </div>

            <div class="text-end">
                <div><strong>{{ $peserta->nama }}</strong></div>
                <small>{{ $peserta->kelas }}</small>
            </div>

            <div>
                <span class="badge bg-danger" id="timer">00:00</span>
            </div>
        </div>

        <div class="row">
            {{-- SOAL --}}
            <div class="col-md-9">
                @foreach ($soals as $index => $soal)
                    <div class="soal-item {{ $index === 0 ? 'active' : '' }}" data-index="{{ $index }}">
                        <div class="p-4 border rounded mb-3">

                            <h5>
                                {{ $index + 1 }}. {!! nl2br(e($soal->soal)) !!}
                            </h5>

                            {{-- GAMBAR --}}
                            @if ($soal->gambar)
                                <img src="{{ asset('storage/' . $soal->gambar) }}" class="img-fluid my-2" width="250">
                            @endif

                            {{-- MULTIPLE CHOICE --}}
                            @if ($soal->tipe_soal === 'multiple_choice')
                                @foreach ($soal->multiple_choice ?? [] as $opsi)
                                    <label class="d-block mb-2">
                                        <input type="checkbox" name="jawaban[{{ $soal->id }}][]"
                                            value="{{ $opsi['opsi'] }}">

                                        {{ $opsi['opsi'] }}. {{ $opsi['jawaban'] }}

                                        @if (!empty($opsi['jawaban_img']))
                                            <br>
                                            <img src="{{ asset('storage/' . $opsi['jawaban_img']) }}" width="120">
                                        @endif
                                    </label>
                                @endforeach
                            @endif

                            {{-- MATCHING --}}
                            @if ($soal->tipe_soal === 'matching')
                                @foreach ($soal->matching ?? [] as $i => $match)
                                    <div class="mb-2">
                                        <strong>{{ $match['kiri'] }}</strong>
                                        <select name="jawaban[{{ $soal->id }}][{{ $i }}]"
                                            class="form-select">
                                            <option value="">-- pilih --</option>
                                            @foreach ($soal->matching as $opt)
                                                <option value="{{ $opt['kanan'] }}">
                                                    {{ $opt['kanan'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endforeach
                            @endif

                        </div>
                    </div>
                @endforeach
            </div>

            {{-- NAVIGASI NOMOR --}}
            <div class="col-md-3">
                <div class="border rounded p-3">
                    <h6 class="fw-bold">Nomor Soal</h6>
                    <div class="d-flex flex-wrap gap-2">
                        @foreach ($soals as $i => $s)
                            <button type="button" class="btn btn-sm btn-outline-primary btn-nomor"
                                onclick="goToSoal({{ $i }})">
                                {{ $i + 1 }}
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- NAVIGATION BOTTOM --}}
        <div class="d-flex justify-content-between mt-4">
            <button type="button" class="btn btn-secondary" onclick="prevSoal()">Sebelumnya</button>

            <button type="button" class="btn btn-warning" onclick="toggleRagu()">
                Ragu-ragu
            </button>

            <button type="button" class="btn btn-primary" onclick="nextSoal()">Lanjut</button>
        </div>

        <div class="text-center mt-3">
            <button type="submit" class="btn btn-success px-5">
                Submit Ujian
            </button>
        </div>
    </form>





    <style>
        .soal-item {
            display: none;
        }

        .soal-item.active {
            display: block;
        }
    </style>

    <script>
        let currentIndex = 0;
        const soals = document.querySelectorAll('.soal-item');

        function showSoal(index) {
            soals.forEach(s => s.classList.remove('active'));
            soals[index].classList.add('active');
            currentIndex = index;
        }

        function nextSoal() {
            if (currentIndex < soals.length - 1) {
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

        function toggleRagu() {
            alert('Soal ditandai ragu-ragu (fitur bisa dikembangkan)');
        }
    </script>
@endsection
