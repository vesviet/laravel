<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderTrackingController extends Controller
{
    public function index(Request $request)
    {
        $order = null;
        $order_number = $request->query('order_number');
        
        if ($order_number) {
            $order = Order::where('order_number', $order_number)->with('items')->first();
        }
        
        return view('storefront.tracking.index', compact('order', 'order_number'));
    }
    
    public function track(Request $request)
    {
        $request->validate([
            'order_number' => 'required|string',
        ]);
        
        return redirect()->route('track-order.index', ['order_number' => $request->order_number]);
    }
}
