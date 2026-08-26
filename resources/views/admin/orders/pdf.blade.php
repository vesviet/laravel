<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice - {{ $order->order_number }}</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 14px; color: #333; }
        .header { text-align: center; margin-bottom: 30px; }
        .header h1 { margin: 0; font-size: 24px; color: #555; }
        .details { width: 100%; margin-bottom: 20px; }
        .details td { vertical-align: top; }
        .details .left { width: 50%; }
        .details .right { width: 50%; text-align: right; }
        table.items { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        table.items th, table.items td { padding: 10px; border: 1px solid #ddd; text-align: left; }
        table.items th { background-color: #f9f9f9; }
        .totals { width: 100%; text-align: right; }
        .totals td { padding: 5px 10px; }
        .totals .label { font-weight: bold; width: 80%; }
        .footer { text-align: center; margin-top: 50px; font-size: 12px; color: #777; border-top: 1px solid #ddd; padding-top: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>INVOICE</h1>
        <p>Order Number: {{ $order->order_number }}</p>
    </div>

    <table class="details">
        <tr>
            <td class="left">
                <strong>Billed To:</strong><br>
                {{ $order->customer_name }}<br>
                {{ $order->address }}<br>
                {{ $order->city }}<br>
                Email: {{ $order->email }}<br>
                Phone: {{ $order->phone }}
            </td>
            <td class="right">
                <strong>Date:</strong> {{ $order->created_at->format('F d, Y') }}<br>
                <strong>Payment Method:</strong> {{ strtoupper($order->payment_method) }}<br>
                <strong>Status:</strong> {{ ucfirst($order->status->value) }}
            </td>
        </tr>
    </table>

    <table class="items">
        <thead>
            <tr>
                <th>Item</th>
                <th>Price</th>
                <th>Qty</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
            <tr>
                <td>{{ $item->product_name }} {{ $item->variant_name ? '('.$item->variant_name.')' : '' }}</td>
                <td>{{ number_format($item->price_at_purchase, 0, ',', '.') }}₫</td>
                <td>{{ $item->quantity }}</td>
                <td>{{ number_format($item->price_at_purchase * $item->quantity, 0, ',', '.') }}₫</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <table class="totals">
        <tr>
            <td class="label">Subtotal:</td>
            <td>{{ number_format($order->subtotal, 0, ',', '.') }}₫</td>
        </tr>
        <tr>
            <td class="label">Shipping:</td>
            <td>{{ number_format($order->shipping_fee, 0, ',', '.') }}₫</td>
        </tr>
        <tr>
            <td class="label">Discount:</td>
            <td>-{{ number_format($order->discount_amount, 0, ',', '.') }}₫</td>
        </tr>
        <tr>
            <td class="label">Total Amount:</td>
            <td><strong>{{ number_format($order->total_amount, 0, ',', '.') }}₫</strong></td>
        </tr>
    </table>

    <div class="footer">
        Thank you for shopping with us!
    </div>
</body>
</html>
