<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\BranchController;

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
    return view('auth.login');
});



Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);

Route::get('/owner', function () { return view('dashboard.owner'); })->middleware('auth');
Route::get('/manager', function () { return view('dashboard.manager'); })->middleware('auth');
Route::get('/finance', function () { return view('dashboard.finance'); })->middleware('auth');
Route::get('/warehouse', function () { return view('dashboard.warehouse'); })->middleware('auth');
Route::get('/cashier', function () { return view('dashboard.cashier'); })->middleware('auth');
Route::get('/audit-log', [AuditLogController::class, 'index'])->middleware('auth')->name('audit.log');
Route::get('/branches', [BranchController::class, 'index'])->middleware('auth')->name('branches.index');
Route::post('/branches', [BranchController::class, 'store'])->middleware('auth')->name('branches.store');