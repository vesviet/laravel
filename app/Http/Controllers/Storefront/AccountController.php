<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
{
    public function orders()
    {
        $orders = Auth::guard('customer')->user()->orders()->orderBy('created_at', 'desc')->get();

        return view('storefront.account.orders', compact('orders'));
    }
}
