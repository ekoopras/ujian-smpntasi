@extends('layouts.app')

@section('title', 'Masuk Ujian')

@section('content')
    <div class="container-fluid bg-ujian">
        <div class="row justify-content-center align-items-center min-vh-100">

            <div class="col-11 col-sm-8 col-md-7 col-lg-5 col-xl-4">

                <div class="card shadow-lg border-0 rounded-4">
                    <div class="card-body p-4">

                        {{-- Icon --}}
                        <div class="text-center mb-2">
                            <div class="d-inline-flex align-items-center justify-content-center">
                                <img src="{{ asset('ico.png') }}" alt="UjianApp"
                                    style="width:100px;box-shadow: 5px 5px 15px rgba(0, 0, 0, 0.3);border-radius: 20px;">
                                {{-- <i class="bi bi-mortarboard-fill fs-3"></i> --}}
                            </div>
                        </div>


                        <h4 class="text-center fw-bold mb-2">Masuk Ujian</h4>
                        <p class="text-center text-secondary mb-3" style="font-size: 13px">
                            Silakan lengkapi data sebelum memulai ujian
                        </p>

                        {{-- ALERT --}}
                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        {{-- FORM (punya kamu) --}}
                        <form method="POST" action="/ujian/cek">
                            @csrf

                            {{-- Nama --}}
                            <div class="mb-2">
                                <label class="form-label fw-semibold" style="font-size: 15px">Nama</label>
                                <input type="text" name="nama" class="form-control" value="{{ old('nama') }}"
                                    placeholder="Masukkan nama lengkap" id="nama" required>
                            </div>

                            {{-- NIS --}}
                            <div class="mb-2">
                                <label class="form-label fw-semibold" style="font-size: 15px">NIS</label>
                                <input type="text" name="nis" class="form-control" placeholder="Nomor Induk Siswa"
                                    value="{{ old('nis') }}" required>
                            </div>

                            {{-- Kelas --}}
                            <div class="mb-2">
                                <label class="form-label fw-semibold" style="font-size: 15px">Kelas</label>
                                <select name="kelase_id" class="form-select" required>
                                    <option value="">-- Pilih Kelas --</option>
                                    @foreach ($kelas as $k)
                                        <option value="{{ $k->id }}"
                                            {{ old('kelase_id') == $k->id ? 'selected' : '' }}>
                                            {{ $k->kelas }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Kode Ujian --}}
                            <div class="mb-3">
                                <label class="form-label fw-semibold" style="font-size: 15px">Kode Ujian</label>
                                <input type="text" name="kode_ujian" class="form-control" placeholder="Contoh: UJIAN-123"
                                    value="{{ old('kode_ujian') }}" required>
                            </div>

                            {{-- Button --}}
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary rounded-3 fw-semibold">
                                    Lanjut Ujian
                                </button>
                            </div>
                        </form>

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
        document.getElementById('nama').addEventListener('input', function() {
            this.value = this.value
                .toLowerCase()
                .replace(/\b\w/g, function(char) {
                    return char.toUpperCase();
                });
        });
    </script>

@endsection
