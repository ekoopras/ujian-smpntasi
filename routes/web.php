<?php

use App\Http\Controllers\UjianController;
use App\Http\Controllers\UjianSiswaController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/ujian', [UjianSiswaController::class, 'form']);
Route::post('/ujian/cek', [UjianSiswaController::class, 'cek']);
Route::post('/ujian/mulai', [UjianSiswaController::class, 'mulai']);
Route::post('/ujian/submit', [UjianSiswaController::class, 'submit']);
