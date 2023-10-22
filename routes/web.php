<?php

use App\Http\Controllers\CSVCompareController;
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

Route::get('/', [CSVCompareController::class, 'index'])->name('index');
Route::post('/upload_files', [CSVCompareController::class, 'uploadFiles'])->name('uploadFiles');
