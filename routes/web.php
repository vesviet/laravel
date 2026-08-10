<?php

use App\Http\Controllers\Admin\OrderPdfController;
use App\Http\Controllers\ProductReviewController;
use App\Http\Controllers\Storefront\AccountController;
use App\Http\Controllers\Storefront\AuthController;
use App\Http\Controllers\Storefront\CatalogController;
use App\Http\Controllers\Storefront\CheckoutController;
use App\Http\Controllers\Storefront\LandingPageController;
use App\Http\Controllers\Storefront\OrderTrackingController;
use App\Livewire\WishlistPage;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('products.index');
});

// Catalog
Route::get('/products', [CatalogController::class, 'index'])->name('products.index');
Route::get('/products/{slug}', [CatalogController::class, 'show'])->name('products.show');

// Checkout
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
Route::get('/checkout/success/{order_number}', [CheckoutController::class, 'success'])->name('checkout.success');

// Order Tracking
Route::get('/track-order', [OrderTrackingController::class, 'index'])->name('track-order.index');
Route::post('/track-order', [OrderTrackingController::class, 'track'])->name('track-order.track');

// Auth (Customer)
Route::prefix('account')->name('account.')->group(function () {
    Route::middleware('guest:customer')->group(function () {
        Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [AuthController::class, 'login']);
        Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
        Route::post('/register', [AuthController::class, 'register']);
        Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
    });

    Route::middleware('auth:customer')->group(function () {
        Route::get('/orders', [AccountController::class, 'orders'])->name('orders');
        Route::get('/wishlist', WishlistPage::class)->name('wishlist');
        Route::post('/products/{product}/reviews', [ProductReviewController::class, 'store'])->name('products.reviews.store');
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    });
});

// Admin Route for PDF — protected by web (Filament) auth guard.
// Only authenticated admin users (App\Models\User) may download order invoices.
Route::middleware('auth')->group(function () {
    Route::get('/admin/orders/{id}/pdf', [OrderPdfController::class, 'download'])->name('admin.orders.pdf');
});

// Dynamic Landing Page Route (Must be at the very end to avoid conflicts)
Route::get('/{slug}', [LandingPageController::class, 'show'])->name('landing.show');
