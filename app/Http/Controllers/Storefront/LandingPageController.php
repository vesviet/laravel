<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\LandingPage;

class LandingPageController extends Controller
{
    public function show(string $slug)
    {
        $page = LandingPage::with(['product'])
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        return view('storefront.landing.show', compact('page'));
    }
}
