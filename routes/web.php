<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookController;

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

Route::get('/', [BookController::class, 'getBooks'])->name('view.book');
Route::get('add-book', [BookController::class, 'add'])->name('add');
Route::post('add-book', [BookController::class, 'addBooks'])->name('add.book');
Route::post('del-book', [BookController::class, 'delBooks'])->name('del.book');
Route::post('update-book', [BookController::class, 'updateBooks'])->name('update.book');