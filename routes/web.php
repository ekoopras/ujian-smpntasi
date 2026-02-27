<?php

use App\Http\Controllers\UjianController;
use App\Http\Controllers\UjianSiswaController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('pwa.download');
});

//Route::get('/ujian', [UjianSiswaController::class, 'form']);
Route::get('/ujian', [UjianSiswaController::class, 'form'])->name('ujian.index');
Route::post('/ujian/cek', [UjianSiswaController::class, 'cek']);
//Route::post('/ujian/mulai', [UjianSiswaController::class, 'mulai']);
Route::post('/ujian/unlock', [UjianSiswaController::class, 'unlock']);
Route::post('/ujian/lock', [UjianSiswaController::class, 'lock']);
// Route untuk memproses submit
Route::post('/ujian/submit', [UjianSiswaController::class, 'submit'])->name('ujian.submit');
// Route untuk halaman selesai (yang dituju setelah redirect)
Route::get('/ujian/selesai', [UjianSiswaController::class, 'selesai'])->name('ujian.selesai');
// Proses mendaftarkan peserta & set waktu mulai (Hanya sekali)
Route::post('/ujian/mulai', [UjianSiswaController::class, 'mulai'])->name('ujian.mulai');
// Halaman soal yang bisa di-refresh tanpa kirim ulang data POST
Route::get('/ujian/soal/{id}', [UjianSiswaController::class, 'soal'])->name('ujian.soal');
Route::post('/ujian/autosave', [UjianSiswaController::class, 'autosave'])->name('ujian.autosave');
