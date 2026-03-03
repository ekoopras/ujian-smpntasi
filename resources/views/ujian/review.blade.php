@extends('layouts.app')

@section('title', 'Review Data Peserta')

@section('content')
    <div class="container-fluid bg-ujian">
        <div class="row justify-content-center align-items-center min-vh-100">

            <div class="col-10 col-sm-8 col-md-7 col-lg-5 col-xl-4">

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

                        <h5 class="text-center fw-bold mb-1">Review Data Peserta</h5>
                        <p class="text-center text-secondary mb-3" style="font-size: 13px">
                            Pastikan data berikut sudah benar sebelum memulai ujian
                        </p>

                        {{-- FORM (penting: tombol ada DI DALAM form) --}}
                        <form method="POST" action="/ujian/mulai">
                            @csrf

                            {{-- Hidden data --}}
                            @foreach ($data as $key => $value)
                                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                            @endforeach
                            <input type="hidden" name="ujian_id" value="{{ $ujian->id }}">

                            {{-- Data review --}}
                            <ul class="list-group list-group-flush mb-3">

                                <li class="list-group-item px-0">
                                    <small class="text-muted">Nama</small><br>
                                    <span class="fw-semibold">{{ $data['nama'] }}</span>
                                </li>

                                <li class="list-group-item px-0">
                                    <small class="text-muted">Nomor Absen</small><br>
                                    <span class="fw-semibold">{{ $data['nomor_absen'] }}</span>
                                </li>

                                <li class="list-group-item px-0">
                                    <small class="text-muted">Kelas</small><br>
                                    {{-- <span class="fw-semibold">{{ $ujian->kelase->kelas }}</span> --}}
                                    <span class="fw-semibold">
                                        {{ $kelasDipilih->kelas }}
                                    </span>
                                </li>

                                <li class="list-group-item px-0">
                                    <small class="text-muted">Mata Pelajaran</small><br>
                                    <span class="fw-semibold">{{ $ujian->mapel->mapel }}</span>
                                </li>

                                <li class="list-group-item px-0">
                                    <small class="text-muted">Durasi Ujian</small><br>
                                    <span class="fw-semibold">{{ $ujian->durasi_menit }} menit</span>
                                </li>

                            </ul>

                            {{-- Button --}}
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary rounded-3 fw-semibold">
                                    Mulai Ujian
                                </button>
                            </div>
                        </form>

                    </div>
                </div>

            </div>

        </div>
    </div>

    {{-- Background --}}
    <style>
        .bg-ujian {
            min-height: 100vh;
            background-color: #0d6efd;
            background-image:
                radial-gradient(circle, rgba(255, 255, 255, 0.15) 1px, transparent 1px);
            background-size: 22px 22px;
        }
    </style>
@endsection
