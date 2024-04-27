<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GmailController;

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

Route::get('/', [GmailController::class, 'redirectToGoogle']);
Route::get('/auth/google/callback', [GmailController::class, 'handleGoogleCallback']);
Route::get('/gmail/dashboard', [GmailController::class, 'dashboard'])->name('gmail.dashboard');
Route::get('/gmail/inbox', [GmailController::class, 'getInbox'])->name('gmail.inbox');
Route::post('/gmail/send', [GmailController::class, 'sendEmail'])->name('gmail.send');
