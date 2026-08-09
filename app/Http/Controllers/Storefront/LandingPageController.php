<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\LandingPage;
use Illuminate\Http\Request;

class LandingPageController extends Controller
{
    public function show($slug)
    {
        $page = LandingPage::where('slug', $slug)->where('is_active', true)->firstOrFail();
        
        return view('storefront.landing.show', compact('page'));
    }
}
