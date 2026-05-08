<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactRequest;
use App\Models\Article;
use App\Models\Banner;
use App\Models\Category;
use App\Models\ContactMessage;
use App\Models\Page;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class PageController extends Controller
{
    public function home()
    {
        $featuredProducts = Product::active()
            ->featured()
            ->orderBy('sort_order')
            ->limit(8)
            ->get();

        $categories = Category::productCategories()
            ->roots()
            ->orderBy('sort_order')
            ->get();

        $latestArticles = Article::published()
            ->latest('published_at')
            ->limit(3)
            ->get();

        $banners = Banner::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return view('pages.home', compact(
            'featuredProducts',
            'categories',
            'latestArticles',
            'banners'
        ));
    }

    public function showPage(string $slug)
    {
        $page = Page::published()
            ->where('slug', $slug)
            ->firstOrFail();

        $viewName = match ($page->template) {
            'contact' => 'pages.templates.contact',
            'about' => 'pages.templates.about',
            'faq' => 'pages.templates.faq',
            'full-width' => 'pages.templates.full-width',
            default => 'pages.templates.default',
        };

        return view($viewName, compact('page'));
    }

    public function submitContact(ContactRequest $request)
    {
        $key = 'contact-form:' . $request->ip();

        if (RateLimiter::tooManyAttempts($key, 3)) {
            $seconds = RateLimiter::availableIn($key);

            return back()
                ->withInput()
                ->with('error', "Bạn đã gửi quá nhiều tin nhắn. Vui lòng thử lại sau {$seconds} giây.");
        }

        RateLimiter::hit($key, 3600);

        ContactMessage::create($request->validated());

        return back()
            ->with('success', 'Gửi thành công! Chúng tôi sẽ liên hệ bạn trong thời gian sớm nhất.');
    }
}
