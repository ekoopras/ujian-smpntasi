@extends('layouts.app')

@section('title', 'Masuk Ujian')

@section('content')
    <div class="container-fluid bg-ujian">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-11 col-sm-8 col-md-7 col-lg-5 col-xl-4">

                <div class="card shadow-lg border-0 rounded-4">
                    <div class="card-body p-4 text-center">

                        {{-- Icon --}}
                        <div class="text-center mb-3">
                            <div class="d-inline-flex align-items-center justify-content-center">
                                <img src="{{ asset('new-logo-ujian.jpg') }}" alt="UjianApp"
                                    style="width:250px;box-shadow: 5px 5px 15px rgba(0, 0, 0, 0.3);border-radius: 10px;">
                                {{-- <i class="bi bi-mortarboard-fill fs-3"></i> --}}
                            </div>
                        </div>

                        <h4 class="fw-bold mb-2">Konfirmasi Selesai</h4>
                        <p class="text-secondary mb-4" style="font-size: 15px">
                            Terima kasih sudah berpartisipasi dalam ujian hari ini. Silakan centang pernyataan di bawah
                            untuk mengakhiri sesi.
                        </p>

                        {{-- Checkbox Pemanis (Styling Box) --}}
                        <div
                            class="p-3 border rounded-3 bg-light mb-4 d-flex align-items-center justify-content-start text-start">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="checkJujur" onchange="toggleBtn()"
                                    style="width: 1.2rem; height: 1.2rem; cursor: pointer;">
                                <label class="form-check-label ms-2 fw-medium text-dark" for="checkJujur"
                                    style="cursor: pointer; font-size: 14px;">
                                    Saya menyatakan telah mengerjakan ujian ini dengan jujur dan sungguh-sungguh.
                                </label>
                            </div>
                        </div>

                        {{-- Tombol Navigasi (Redirect ke /ujian) --}}
                        <form action="{{ route('ujian.index') }}" method="GET">
                            <button type="submit" id="btnSelesai"
                                class="btn btn-success w-100 py-3 fw-bold rounded-3 shadow-sm" disabled>
                                Selesai & Keluar Sesi Ujian
                            </button>
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
        function toggleBtn() {
            const checkbox = document.getElementById('checkJujur');
            const btn = document.getElementById('btnSelesai');

            if (checkbox.checked) {
                btn.disabled = false;
                btn.classList.remove('btn-success');
                btn.classList.add('btn-primary'); // Opsional: ubah warna saat aktif agar kontras
            } else {
                btn.disabled = true;
                btn.classList.remove('btn-primary');
                btn.classList.add('btn-success');
            }
        }
    </script>
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
