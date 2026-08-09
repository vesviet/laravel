<x-mail::message>
# Order Confirmation

Hi {{ $order->customer_name }},

Thank you for your order! We have received your order **{{ $order->order_number }}** and are working on it now.

### Order Summary

<x-mail::table>
| Product | Price | Quantity | Subtotal |
| :--- | :--- | :--- | :--- |
@foreach($order->items as $item)
| {{ $item->product_name }} {{ $item->variant_name ? '('.$item->variant_name.')' : '' }} | ${{ number_format($item->price_at_purchase, 2) }} | {{ $item->quantity }} | ${{ number_format($item->price_at_purchase * $item->quantity, 2) }} |
@endforeach
| | **Subtotal** | | **${{ number_format($order->subtotal, 2) }}** |
| | **Shipping** | | **${{ number_format($order->shipping_fee, 2) }}** |
| | **Discount** | | **-${{ number_format($order->discount_amount, 2) }}** |
| | **Total** | | **${{ number_format($order->total_amount, 2) }}** |
</x-mail::table>

We will notify you once your order has been shipped.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
