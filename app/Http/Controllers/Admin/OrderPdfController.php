<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;

class OrderPdfController extends Controller
{
    public function download($id)
    {
        $order = Order::with(['items.product', 'customer'])->findOrFail($id);

        $pdf = Pdf::loadView('admin.orders.pdf', compact('order'));

        return $pdf->download('invoice-'.$order->order_number.'.pdf');
    }
}
