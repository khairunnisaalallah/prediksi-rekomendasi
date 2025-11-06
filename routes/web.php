<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('index');
});

// Static views copied into resources/views
Route::get('/login', function () {
    return view('authentication-login');
});

Route::get('/register', function () {
    return view('authentication-register');
});

Route::get('/data-balita', function () {
    return view('data-balita');
});

Route::get('/prediksi', function () {
    return view('prediksi');
});

Route::get('/diagnosis-gizi', function () {
    return view('diagnosis-gizi');
});

Route::get('/panduan', function () {
    return view('panduan');
});

// You can add more routes here mapping to other blade files as needed.
