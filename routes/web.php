<?php

use App\Http\Controllers\UjianController;
use App\Http\Controllers\UjianSiswaController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('pwa.download');
});

Route::get('/ujian-selesai', function () {
    return view('ujian.selesai');
});

Route::get('/ujian/sudah', function () {
    return view('ujian.sudah');
})->name('ujian.sudah');


Route::get('/ujian', [UjianSiswaController::class, 'form']);
Route::post('/ujian/cek', [UjianSiswaController::class, 'cek']);
Route::post('/ujian/mulai', [UjianSiswaController::class, 'mulai']);
Route::post('/ujian/submit', [UjianSiswaController::class, 'submit'])->name('ujian.submit');
Route::post('/ujian/unlock', [UjianSiswaController::class, 'unlock']);
Route::post('/ujian/lock', [UjianSiswaController::class, 'lock']);
