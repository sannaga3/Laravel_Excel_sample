<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

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

Route::post('users/export', [UserController::class, 'export'])->name('users.export');
Route::get('users/import/index', [UserController::class, 'importIndex'])->name('users.import.index');
Route::post('users/import/store', [UserController::class, 'importStore'])->name('users.import.store');
Route::resource('/users', UserController::class);

Route::get('/', function () {
    return view('welcome');
});