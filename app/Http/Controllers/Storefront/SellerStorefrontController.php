<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\SellerPage;
use Illuminate\Http\Request;
use Spatie\Multitenancy\Models\Tenant;
use Filament\Facades\Filament;

class SellerStorefrontController extends Controller
{
    /**
     * Display the published Seller Storefront.
     */
    public function index(Request $request, $seller_subdomain)
    {
        $tenant = Tenant::current();

        if (!$tenant) {
            abort(404, 'Store not found.');
        }

        // Fetch the published page for this tenant
        $page = SellerPage::where('seller_id', $tenant->id)
            ->where('is_published', true)
            ->first();

        if (!$page) {
            // Optionally, show a "Coming Soon" page or just 404
            abort(404, 'Store page is not published yet.');
        }

        return view('storefront.seller.index', [
            'page' => $page,
            'tenant' => $tenant,
            'isPreview' => false,
        ]);
    }

    /**
     * Display the preview of the Seller Storefront for the Filament panel.
     */
    public function preview(Request $request)
    {
        // For the preview, we rely on the authenticated user's seller profile
        $user = auth()->user();
        if (!$user || !$user->sellerProfile) {
            abort(403, 'Unauthorized.');
        }

        $tenant = $user->sellerProfile;

        // Ensure we're pulling the latest data (even if not published)
        $page = SellerPage::where('seller_id', $tenant->id)->first();

        if (!$page) {
            return response('No page configured yet. Please configure and save.', 200);
        }

        return view('storefront.seller.index', [
            'page' => $page,
            'tenant' => $tenant,
            'isPreview' => true,
        ]);
    }
}
