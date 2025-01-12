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

use App\Http\Controllers\TareaController;

Route::get('/', [TareaController::class, 'index'])->name('tareas.index');
Route::get('/tareas/search', [TareaController::class, 'search'])->name('tareas.search');
Route::resource('tareas', TareaController::class);
