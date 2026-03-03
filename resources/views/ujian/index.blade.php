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
                                <img src="{{ asset('new-logo-ujian.jpg') }}" alt="UjianApp"
                                    style="width:250px;box-shadow: 5px 5px 15px rgba(0, 0, 0, 0.3);border-radius: 10px;">
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
                            {{-- <div class="mb-2">
                                <label class="form-label fw-semibold" style="font-size: 15px">Nomor Absen</label>
                                <input type="text" name="nomor_absen" class="form-control"
                                    placeholder="Nomor Induk Siswa" value="{{ old('nomor_absen') }}" required>
                            </div> --}}

                            <div class="mb-2">
                                <label class="form-label fw-semibold" style="font-size: 15px">Nomor Absen</label>
                                <select name="nomor_absen" class="form-select rounded-3" aria-label="Pilih Nomor Absen"
                                    required>
                                    <option value="" selected disabled>Pilih Nomor Absen</option>
                                    <option value="01">01</option>
                                    <option value="02">02</option>
                                    <option value="03">03</option>
                                    <option value="04">04</option>
                                    <option value="05">05</option>
                                    <option value="06">06</option>
                                    <option value="07">07</option>
                                    <option value="08">08</option>
                                    <option value="09">09</option>
                                    <option value="10">10</option>
                                    <option value="11">11</option>
                                    <option value="12">12</option>
                                    <option value="13">13</option>
                                    <option value="14">14</option>
                                    <option value="15">15</option>
                                    <option value="16">16</option>
                                    <option value="17">17</option>
                                    <option value="18">18</option>
                                    <option value="19">19</option>
                                    <option value="20">20</option>
                                    <option value="21">21</option>
                                    <option value="22">22</option>
                                    <option value="23">23</option>
                                    <option value="24">24</option>
                                    <option value="25">25</option>
                                    <option value="26">26</option>
                                    <option value="27">27</option>
                                    <option value="28">28</option>
                                    <option value="29">29</option>
                                    <option value="30">30</option>
                                    <option value="31">31</option>
                                    <option value="32">32</option>
                                    <option value="33">33</option>
                                    <option value="34">34</option>
                                    <option value="35">35</option>

                                    {{-- Tambahkan seterusnya sesuai jumlah siswa --}}
                                </select>
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
