<?php

namespace App\Http\Controllers;

use App\Actions\ProcessCheckoutAction;
use App\Http\Requests\CheckoutRequest;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function index()
    {
        return view('pages.checkout');
    }

    public function store(CheckoutRequest $request, ProcessCheckoutAction $action)
    {
        try {
            $order = $action->execute($request->validated());

            return redirect()
                ->route('checkout.success', $order->order_number)
                ->with('success', 'Đặt hàng thành công!');
        } catch (\RuntimeException $e) {
            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    public function success(string $orderNumber)
    {
        $order = \App\Models\Order::where('order_number', $orderNumber)
            ->with('items')
            ->firstOrFail();

        return view('pages.checkout-success', compact('order'));
    }
}
