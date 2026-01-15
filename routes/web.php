<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StockApiController;

// Route::get('/', function () {
//     return view('welcome');
// });
// Landing Page - List all products
Route::get('/', [ProductController::class, 'landing'])->name('landing');

// Product Detail Page
Route::get('/product', [ProductController::class, 'detail'])
    ->name('product.detail');
// stock check dengan whitelist
Route::get('/product/nextProduct', [ProductController::class, 'nextProduct'])
    // ->middleware('whitelist.api')
    ->name('product.next');

// Stock Check (dengan blacklist middleware)
Route::post('/product/stock', [ProductController::class, 'checkStock'])
    ->middleware('blacklistip')
    ->name('product.stock.check');

// Mock API Routes (untuk aplikasi kedua di port 8001)
Route::get('/api/product/stock/check', [StockApiController::class, 'check'])->name('api.stock.check');

// Route::get('/', [ProductController::class, 'index'])->name('products.index');
// Route::post('/product/stock', [ProductController::class, 'checkStock'])->name('product.stock.check');
// Mock API Routes
// Route::get('/api/product/stock/check', [StockApiController::class, 'check'])->name('api.stock.check');
// Route::post('/product/stock', [ProductController::class, 'checkStock'])
//     ->middleware('block.local.ip')
//     ->name('product.stock.check');

// Route::get('/catalog', function () {
//     return view('landing');
// });
