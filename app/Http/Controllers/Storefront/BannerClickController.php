<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BannerClickController extends Controller
{
    /**
     * Track a click on a banner and safely redirect to the target URL.
     */
    public function track(Banner $banner, Request $request): RedirectResponse
    {
        $banner->recordClick();

        $targetUrl = $banner->link;

        if (empty($targetUrl) || trim($targetUrl) === '') {
            return redirect()->route('home');
        }

        $trimmedUrl = trim($targetUrl);

        // Reject dangerous pseudo-protocols (XSS / open data schemes)
        if (preg_match('/^(javascript|data|vbscript):/i', $trimmedUrl)) {
            return redirect()->route('home');
        }

        // External HTTP/HTTPS URL
        if (Str::startsWith($trimmedUrl, ['http://', 'https://'])) {
            return redirect()->away($trimmedUrl);
        }

        // Relative internal URL
        return redirect($trimmedUrl);
    }
}
