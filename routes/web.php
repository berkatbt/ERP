<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\StockMovementController;

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

// Public Routes
Route::get('/', function () {
    return view('auth.login');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);


// Protected Routes - Authenticated Users Only
Route::middleware('auth')->group(function () {
    
     Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard Routes - Role-based
    Route::get('/owner', function () { 
        return view('dashboard.owner'); 
    })->middleware('role:owner')->name('dashboard.owner');

    Route::get('/manager', function () { 
        return view('dashboard.manager'); 
    })->middleware('role:manager')->name('dashboard.manager');

    Route::get('/finance', function () { 
        return view('dashboard.finance'); 
    })->middleware('role:finance')->name('dashboard.finance');

    Route::get('/warehouse', function () { 
        return view('dashboard.warehouse'); 
    })->middleware('role:warehouse')->name('dashboard.warehouse');

    Route::get('/cashier', function () { 
        return view('dashboard.cashier'); 
    })->middleware('role:cashier')->name('dashboard.cashier');


    // Purchase Request Routes
    Route::prefix('purchase-requests')
        ->name('purchase-requests.')
        ->group(function () {
            Route::get('/', [\App\Http\Controllers\PurchaseRequestController::class, 'index'])->name('index');
            Route::post('/', [\App\Http\Controllers\PurchaseRequestController::class, 'store'])->name('store');
            Route::post('/{id}/approve', [\App\Http\Controllers\PurchaseRequestController::class, 'approve'])->name('approve');
            Route::post('/{id}/reject', [\App\Http\Controllers\PurchaseRequestController::class, 'reject'])->name('reject');
        });

    // Audit Log Routes - Owner & Manager only
    Route::get('/audit-log', [AuditLogController::class, 'index'])
        ->middleware('role:owner,manager')
        ->name('audit.log');

    // Branch Management Routes - Owner & Manager only
    Route::prefix('branches')
        ->name('branches.')
        ->middleware('role:owner,manager')
        ->group(function () {
            Route::get('/', [BranchController::class, 'index'])->name('index');
            Route::post('/', [BranchController::class, 'store'])->name('store');
            Route::get('/{branch}', [BranchController::class, 'show'])->name('show');
            Route::get('/{branch}/detail', [BranchController::class, 'detail'])->name('detail');
            Route::get('/{branch}/edit', [BranchController::class, 'edit'])->name('edit');
            Route::put('/{branch}', [BranchController::class, 'update'])->name('update');
            Route::delete('/{branch}', [BranchController::class, 'destroy'])->name('destroy');
        });

    // Product Management Routes - Owner, Manager, Warehouse
    Route::prefix('products')
        ->name('products.')
        ->middleware('role:owner,manager,warehouse')
        ->group(function () {
            Route::get('/', [ProductController::class, 'index'])->name('index');
            Route::post('/', [ProductController::class, 'store'])->name('store');
            Route::get('/{product}/edit', [ProductController::class, 'edit'])->name('edit');
            Route::put('/{product}', [ProductController::class, 'update'])->name('update');
            Route::delete('/{product}', [ProductController::class, 'destroy'])->name('destroy');
        });

    // Stock Management Routes - Owner, Manager, Warehouse
    Route::prefix('stocks')
        ->name('stocks.')
        ->middleware('role:owner,manager,warehouse')
        ->group(function () {
            Route::get('/', [StockController::class, 'index'])->name('index');
            Route::post('/', [StockController::class, 'store'])->name('store');
            Route::get('/{stock}/edit', [StockController::class, 'edit'])->name('edit');
            Route::put('/{stock}', [StockController::class, 'update'])->name('update');
            Route::delete('/{stock}', [StockController::class, 'destroy'])->middleware('role:owner,manager')->name('destroy');
        });

    // Stock Movement History Routes - Owner, Manager, Warehouse
    Route::prefix('stock-movements')
        ->name('stock-movements.')
        ->middleware('role:owner,manager,warehouse')
        ->group(function () {
            Route::get('/', [StockMovementController::class, 'index'])->name('index');
            Route::get('/summary', [StockMovementController::class, 'summary'])->name('summary');
        });

});
