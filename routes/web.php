<?php

use App\Http\Controllers\PageController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\CheckoutController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes — Máy Điện Giải Sài Gòn
|--------------------------------------------------------------------------
*/

// Home
Route::get('/', [PageController::class, 'home'])->name('home');

// Products
Route::get('/san-pham', [ProductController::class, 'index'])->name('products.index');
Route::get('/san-pham/{slug}', [ProductController::class, 'show'])->name('products.show');

// Articles/Blog
Route::get('/tin-tuc', [ArticleController::class, 'index'])->name('articles.index');
Route::get('/tin-tuc/{slug}', [ArticleController::class, 'show'])->name('articles.show');

// Checkout
Route::get('/thanh-toan', [CheckoutController::class, 'index'])->name('checkout');
Route::post('/thanh-toan', [CheckoutController::class, 'store'])->name('checkout.store');
Route::get('/dat-hang-thanh-cong/{orderNumber}', [CheckoutController::class, 'success'])->name('checkout.success');

// Contact & About aliases
Route::get('/lien-he', [PageController::class, 'showPage'])->defaults('slug', 'lien-he')->name('contact');
Route::get('/gioi-thieu', [PageController::class, 'showPage'])->defaults('slug', 'gioi-thieu')->name('about');
Route::post('/lien-he/submit', [PageController::class, 'submitContact'])->name('contact.submit');

// Dynamic CMS pages (must be after all fixed routes)
Route::get('/page/{slug}', [PageController::class, 'showPage'])->name('pages.show');
