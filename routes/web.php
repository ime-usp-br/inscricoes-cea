<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MainController;
use App\Http\Controllers\SemesterController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\AttachmentController;
use App\Http\Controllers\DepositReceiptController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [MainController::class, 'index'])->name("home");

Route::get('/users/loginas', [UserController::class, 'loginas'])->name("users.loginas");
Route::resource('users', UserController::class);

Route::resource("semesters", SemesterController::class);

Route::resource("applications", ApplicationController::class);

Route::get("/attachment/download/{attachment}",[AttachmentController::class, "download"])->name("attachments.download");

Route::get("/receipt/download/{receipt}",[DepositReceiptController::class, "download"])->name("receipts.download");
