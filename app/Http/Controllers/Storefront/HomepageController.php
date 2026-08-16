<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cache;

class HomepageController extends Controller
{
    public function index(): View
    {
        // Cache for 5 minutes — invalidated by ProductObserver on create/update/delete
        $featuredProducts = Cache::remember('homepage.featured', 300, function () {
            return Product::featured()
                ->with('category')
                ->take(8)
                ->get();
        });

        $newArrivals = Cache::remember('homepage.new_arrivals', 300, function () {
            return Product::active()
                ->latest()
                ->with('category')
                ->take(8)
                ->get();
        });

        $categories = Cache::remember('homepage.categories', 600, function () {
            return Category::whereNull('parent_id')
                ->orderBy('name')
                ->get();
        });

        return view('storefront.home.index', compact(
            'featuredProducts',
            'newArrivals',
            'categories'
        ));
    }
}
