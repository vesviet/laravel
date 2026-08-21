<?php

use App\Http\Controllers\Admin\OrderPdfController;
use App\Http\Controllers\ProductReviewController;
use App\Http\Controllers\Storefront\AccountController;
use App\Http\Controllers\Storefront\AuthController;
use App\Http\Controllers\Storefront\BannerClickController;
use App\Http\Controllers\Storefront\BlogController;
use App\Http\Controllers\Storefront\CatalogController;
use App\Http\Controllers\Storefront\CheckoutController;
use App\Http\Controllers\Storefront\FeedController;
use App\Http\Controllers\Storefront\HomepageController;
use App\Http\Controllers\Storefront\LandingPageController;
use App\Http\Controllers\Storefront\NewsletterController;
use App\Http\Controllers\Storefront\OrderTrackingController;
use App\Http\Controllers\Storefront\PageController;
use App\Livewire\WishlistPage;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomepageController::class, 'index'])->name('home');
Route::view('/about', 'storefront.pages.about')->name('about');
Route::view('/contact', 'storefront.pages.contact')->name('contact');

// Blog & Knowledge Hub
Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show');

// Catalog
Route::get('/products', [CatalogController::class, 'index'])->name('products.index');
Route::get('/products/{slug}', [CatalogController::class, 'show'])->name('products.show');

// Checkout
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store')->middleware('throttle:checkout');
Route::get('/checkout/success/{order_number}', [CheckoutController::class, 'success'])->name('checkout.success');

// Order Tracking
Route::get('/track-order', [OrderTrackingController::class, 'index'])->name('track-order.index');
Route::post('/track-order', [OrderTrackingController::class, 'track'])->name('track-order.track')->middleware('throttle:20,1');

// Newsletter
Route::post('/newsletter/subscribe', [NewsletterController::class, 'store'])->name('newsletter.subscribe')->middleware('throttle:newsletter');

// Auth (Customer)
Route::prefix('account')->name('account.')->group(function () {
    Route::middleware('guest:customer')->group(function () {
        Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1');
        Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
        Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:5,1');
        // B1: Forgot password flow
        Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
        Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email')->middleware('throttle:5,1');
        Route::get('/reset-password/{token}', [AuthController::class, 'showResetForm'])->name('password.reset');
        Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
    });

    Route::middleware('auth:customer')->group(function () {
        Route::get('/orders', [AccountController::class, 'orders'])->name('orders');
        Route::get('/orders/{order_number}', [AccountController::class, 'orderDetail'])->name('orders.show');
        Route::post('/orders/{order_number}/cancel', [AccountController::class, 'cancelOrder'])->name('orders.cancel');
        Route::post('/orders/{order_number}/reorder', [AccountController::class, 'reorder'])->name('orders.reorder');
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

// SEO Feeds & XML Sitemaps
Route::get('/sitemap.xml', [FeedController::class, 'sitemapIndex'])->name('sitemap.index');
Route::get('/sitemap-products.xml', [FeedController::class, 'productsSitemap'])->name('sitemap.products');
Route::get('/sitemap-categories.xml', [FeedController::class, 'categoriesSitemap'])->name('sitemap.categories');
Route::get('/sitemap-posts.xml', [FeedController::class, 'postsSitemap'])->name('sitemap.posts');
Route::get('/sitemap-pages.xml', [FeedController::class, 'pagesSitemap'])->name('sitemap.pages');
Route::get('/feeds/google-merchant.xml', [FeedController::class, 'googleMerchantFeed'])->name('feed.google-merchant');
Route::get('/feed', [FeedController::class, 'blogRssFeed'])->name('feed.rss');
Route::get('/rss.xml', [FeedController::class, 'blogRssFeed'])->name('feed.rss.xml');

// Banner Click Tracking
Route::get('/banner/click/{banner}', [BannerClickController::class, 'track'])->name('banner.click');

// Dynamic CMS & Landing Page Route (Must be at the very end to avoid conflicts)
Route::get('/pages/{slug}', [PageController::class, 'show'])->name('pages.show');
Route::get('/{slug}', [PageController::class, 'show'])->name('page.show');
