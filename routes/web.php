<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VisitController;
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

Auth::routes(['register' => false]);
Route::get('/', [App\Http\Controllers\HomeController::class, 'index']);
Route::get('logs', [\Rap2hpoutre\LaravelLogViewer\LogViewerController::class, 'index']);

Route::get('getInitialRedirectPath', [UserController::class, 'getInitialRedirectPath'])->name('getInitialRedirectPath');

//VIEW ROUTES
Route::group(['middleware' => ['auth']], function () {
    Route::view('/maps', 'home')->name('maps');
    Route::view('/users', 'home')->name('users');
});

//VISITS
Route::group(['middleware' => ['web', 'permission:visit_management', 'auth']], function () {
    Route::get('getVisits', [VisitController::class, 'index'])->name('getVisits');
    Route::post('setVisit', [VisitController::class, 'store'])->name('setVisit');
    Route::delete('deleteVisit/{id}', [VisitController::class, 'destroy'])->name('deleteVisit');
    Route::get('getVisitId/{id}', [VisitController::class, 'edit'])->name('getVisitId');
    Route::put('updateVisit', [VisitController::class, 'update'])->name('updateVisit');
});

//USER
Route::group(['middleware' => ['web', 'permission:user_management', 'auth']], function () {
    Route::get('getUserData', [UserController::class, 'index'])->name('getUserData');
    Route::post('addUser', [UserController::class, 'store'])->name('addUser');
    Route::delete('deleteUser/{id}', [UserController::class, 'destroy'])->name('deleteUser');
    Route::get('getUserId/{id}', [UserController::class, 'edit'])->name('getUserId');
    Route::post('updateUser', [UserController::class, 'update'])->name('updateUser');
    Route::get('getAccessHistory/{id}', [UserController::class, 'getAccessHistory'])->name('getAccessHistory');
    Route::post('updateState', [UserController::class, 'updateState'])->name('updateState');
    Route::get('getRol', [UserController::class, 'getRol'])->name('getRol');
});

