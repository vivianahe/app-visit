<?php

use App\Http\Controllers\VisitController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('getVisit', [VisitController::class, 'index'])->name('getVisit');
Route::post('setVisits', [VisitController::class, 'store'])->name('setVisits');
Route::get('visitId/{id}', [VisitController::class, 'edit'])->name('visitId');
Route::put('update', [VisitController::class, 'update'])->name('update');   
Route::delete('delete/{id}', [VisitController::class, 'destroy'])->name('delete');
