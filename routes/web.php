<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AmoCRMController;

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

Route::get('/', [AmoCRMController::class, 'index'])->name('home');
Route::get('/get-contacts', [AmoCRMController::class, 'getContacts']);
Route::get('/contacts', [AmoCRMController::class, 'contacts'])->name('success');