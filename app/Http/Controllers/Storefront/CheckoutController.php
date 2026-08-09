<?php

namespace App\Http\Controllers\Storefront;

use App\Actions\ProcessCheckoutAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\CheckoutRequest;
use App\Mail\OrderConfirmationMail;
use App\Models\Order;
use App\Models\Province;
use App\Services\CartService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;

class CheckoutController extends Controller
{
    public function __construct(
        protected CartService $cartService,
        protected ProcessCheckoutAction $processCheckout
    ) {}

    public function index()
    {
        $rawCart = $this->cartService->getCart();

        if (empty($rawCart)) {
            return redirect()->route('products.index')->with('error', 'Your cart is empty.');
        }

        // Enriched items: contains name, price, variant_name, subtotal
        $cart     = $this->cartService->getCartItemsDetails();
        $subtotal = $this->cartService->calculateTotal();
        $customer = auth('customer')->user();

        // Cache provinces for 24 hours — data changes rarely
        $provinces = Cache::remember('provinces_list', 86400, fn() =>
            Province::orderBy('name')->get()
        );

        $appliedCoupon = session()->get('coupon');

        return view('storefront.checkout.index', compact('cart', 'subtotal', 'customer', 'provinces', 'appliedCoupon'));
    }

    /**
     * Process checkout — delegates entirely to ProcessCheckoutAction.
     * The outer DB::transaction is removed; ProcessCheckoutAction owns the transaction boundary.
     */
    public function store(CheckoutRequest $request)
    {
        $cart = $this->cartService->getCart();

        if (empty($cart)) {
            return redirect()->route('products.index');
        }

        try {
            $customerData = $request->validated();

            // Attach logged-in customer ID if available.
            if (auth('customer')->check()) {
                $customerData['customer_id'] = auth('customer')->id();
            }

            $couponCode = session()->get('coupon');
            $order = $this->processCheckout->execute($customerData, $couponCode);

            // Clear coupon from session after use.
            session()->forget('coupon');

            // Send order confirmation email if provided.
            if (! empty($order->email)) {
                Mail::to($order->email)->send(new OrderConfirmationMail($order));
            }

            return redirect()->route('checkout.success', ['order_number' => $order->order_number]);
        } catch (\Exception $e) {
            return back()
                ->with('error', 'There was an error processing your order. Please try again. '.$e->getMessage())
                ->withInput();
        }
    }

    public function success(string $order_number)
    {
        $order = Order::where('order_number', $order_number)->firstOrFail();

        return view('storefront.checkout.success', compact('order'));
    }
}
