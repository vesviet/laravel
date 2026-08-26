<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Gate;

class OrderPdfController extends Controller
{
    public function download(Order $order)
    {
        Gate::authorize('view', $order);

        $order->load(['items.product', 'customer']);

        $pdf = Pdf::loadView('admin.orders.pdf', compact('order'));

        return $pdf->download('invoice-'.$order->order_number.'.pdf');
    }
}
