<?php

use App\Http\Controllers\UjianController;
use App\Http\Controllers\UjianSiswaController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/ujian', [UjianSiswaController::class, 'index']);
Route::post('/ujian/start', [UjianSiswaController::class, 'start']);
Route::post('/ujian/submit', [UjianSiswaController::class, 'submit']);
