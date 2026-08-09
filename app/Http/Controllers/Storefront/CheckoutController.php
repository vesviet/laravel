<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Mail\OrderConfirmationMail;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class CheckoutController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart', []);
        
        if (empty($cart)) {
            return redirect()->route('products.index')->with('error', 'Your cart is empty.');
        }
        
        $cartService = app(\App\Services\CartService::class);
        $cartDetails = $cartService->getCartItemsDetails();
        $subtotal = $cartService->calculateTotal();

        // Determine if logged in customer
        $customer = auth('customer')->user();

        return view('storefront.checkout.index', compact('cart', 'subtotal', 'customer'));
    }

    public function store(Request $request)
    {
        $cart = session()->get('cart', []);
        
        if (empty($cart)) {
            return redirect()->route('products.index');
        }

        $cartService = app(\App\Services\CartService::class);
        $cartDetails = $cartService->getCartItemsDetails();
        $subtotal = $cartService->calculateTotal();

        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => ['required', 'string', 'max:20', new \App\Rules\ValidPhoneVN],
            'address' => 'required|string|max:255',
            'city' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'payment_method' => 'required|in:cod',
        ]);

        DB::beginTransaction();
        try {
            $promotionEngine = app(\App\Services\PromotionEngine::class);
            $couponCode = session()->get('coupon');
            $discountAmount = $promotionEngine->calculateDiscount($subtotal, $cartDetails, $couponCode);

            $orderService = app(\App\Services\OrderService::class);
            
            // Add customer_id if logged in
            if (auth('customer')->check()) {
                $validated['customer_id'] = auth('customer')->id();
            }

            $order = $orderService->createOrder($validated, $cartDetails, $subtotal, $discountAmount, 0);

            DB::commit();
            
            // Clear cart
            session()->forget('cart');
            session()->forget('coupon');

            // Send order confirmation email
            if (!empty($order->email)) {
                Mail::to($order->email)->send(new OrderConfirmationMail($order));
            }

            return redirect()->route('checkout.success', ['order_number' => $order->order_number]);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'There was an error processing your order. Please try again. ' . $e->getMessage())->withInput();
        }
    }

    public function success($order_number)
    {
        $order = Order::where('order_number', $order_number)->firstOrFail();
        
        // Basic security check: if it belongs to customer, make sure it's them
        if ($order->customer_id && !auth('customer')->check()) {
            // Depending on requirements, we can allow guest view of success page right after checkout
        }
        
        return view('storefront.checkout.success', compact('order'));
    }
}
