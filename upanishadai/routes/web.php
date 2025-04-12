<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
// In routes/api.php or routes/web.php
Route::post('/broadcasting/auth', function () {
    return Auth::check() ? Auth::user() : abort(403);
})->middleware(['web', 'auth']);
