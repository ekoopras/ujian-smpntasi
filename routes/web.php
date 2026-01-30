<?php

use App\Http\Controllers\UjianController;
use App\Http\Controllers\UjianSiswaController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/halaman1', function () {
    return view('theme.halaman1');
});

Route::get('/halaman2', function () {
    return view('theme.halaman2');
});

Route::get('/halaman-soal', function () {
    return view('theme.halaman-soal');
});


Route::get('/ujian', [UjianSiswaController::class, 'form']);
Route::post('/ujian/cek', [UjianSiswaController::class, 'cek']);
Route::post('/ujian/mulai', [UjianSiswaController::class, 'mulai']);
Route::post('/ujian/submit', [UjianSiswaController::class, 'submit']);
