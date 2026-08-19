<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\LandingPage;
use App\Models\Page;

class PageController extends Controller
{
    /**
     * Display a static CMS page or fallback to a promotional LandingPage.
     */
    public function show(string $slug)
    {
        $page = Page::published()->where('slug', $slug)->first();

        if ($page) {
            return view('storefront.pages.show', compact('page'));
        }

        $landingPage = LandingPage::with(['product'])
            ->where('slug', $slug)
            ->where('is_active', true)
            ->first();

        if ($landingPage) {
            return view('storefront.landing.show', ['page' => $landingPage]);
        }

        abort(404);
    }
}
