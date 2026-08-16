<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
{
    public function orders()
    {
        // R5: Eager load items to prevent N+1 when view displays item count/details.
        // Paginate to prevent OOM for customers with many orders.
        $orders = Auth::guard('customer')
            ->user()
            ->orders()
            ->with('items')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('storefront.account.orders', compact('orders'));
    }
}
