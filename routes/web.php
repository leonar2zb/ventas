<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\RegisterController;

use Filament\Http\Middleware\Authenticate;
use Filament\Pages\Auth\Login;

Route::get('/login', Login::class)->name('login')->middleware(Authenticate::class);

Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

Route::get('/', function () {
    return view('welcome');
})->name('welcome');
