<?php

use App\Http\Controllers\BalitaController;
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

Route::prefix('data-balita')->name('data-balita.')->group(function () {
    Route::get('/', [BalitaController::class, 'index'])->name('index');
    Route::post('/', [BalitaController::class, 'store'])->name('store');
    Route::put('/{balita}', [BalitaController::class, 'update'])->name('update');
    Route::delete('/{balita}', [BalitaController::class, 'destroy'])->name('destroy');
});

Route::get('/prediksi', [BalitaController::class, 'prediksi'])->name('prediksi');

Route::get('/diagnosis-gizi', [BalitaController::class, 'diagnosis'])->name('diagnosis');
Route::post('/diagnosis-gizi/{balita}', [BalitaController::class, 'storeDiagnosis'])->name('diagnosis.store');

Route::get('/panduan', function () {
    return view('panduan');
});

// You can add more routes here mapping to other blade files as needed.
