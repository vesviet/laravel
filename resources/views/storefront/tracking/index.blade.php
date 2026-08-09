@extends('layouts.storefront')

@section('title', 'Track Order')

@section('content')
<div class="max-w-3xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
    <div class="bg-white rounded-lg shadow-sm p-6 md:p-12 text-center mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-4">Track Your Order</h1>
        <p class="text-gray-600 mb-8">Enter your order number below to check the current status of your shipment.</p>
        
        <form action="{{ route('track-order.track') }}" method="POST" class="max-w-md mx-auto flex gap-4">
            @csrf
            <input type="text" name="order_number" value="{{ old('order_number', $order_number) }}" placeholder="e.g. ORD-XXXXXXXXXX" required class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-3 px-4 border">
            <button type="submit" class="bg-blue-600 border border-transparent rounded-md shadow-sm py-3 px-6 text-base font-medium text-white hover:bg-blue-700">Track</button>
        </form>
    </div>

    @if($order_number)
        @if($order)
            <div class="bg-white rounded-lg shadow-sm p-6 md:p-8">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 pb-6 border-b">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">Order {{ $order->order_number }}</h2>
                        <p class="text-gray-500 text-sm mt-1">Placed on {{ $order->created_at->format('M d, Y H:i') }}</p>
                    </div>
                    <div class="mt-4 sm:mt-0">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium capitalize
                            @if($order->status == 'pending') bg-yellow-100 text-yellow-800
                            @elseif($order->status == 'confirmed') bg-blue-100 text-blue-800
                            @elseif($order->status == 'shipping') bg-indigo-100 text-indigo-800
                            @elseif($order->status == 'delivered') bg-green-100 text-green-800
                            @else bg-red-100 text-red-800 @endif
                        ">
                            {{ $order->status }}
                        </span>
                    </div>
                </div>
                
                <h3 class="text-lg font-medium text-gray-900 mb-4">Items Ordered</h3>
                <ul class="divide-y divide-gray-200 mb-8">
                    @foreach($order->items as $item)
                    <li class="py-4 flex justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $item->product_name }}</p>
                            @if($item->variant_name)
                                <p class="text-sm text-gray-500">{{ $item->variant_name }}</p>
                            @endif
                            <p class="text-sm text-gray-500">Qty: {{ $item->quantity }}</p>
                        </div>
                        <p class="text-sm font-medium text-gray-900">${{ number_format($item->price_at_purchase * $item->quantity, 2) }}</p>
                    </li>
                    @endforeach
                </ul>
                
                <div class="border-t border-gray-200 pt-6">
                    <dl class="flex justify-between items-center text-sm">
                        <dt class="font-medium text-gray-900">Total Amount</dt>
                        <dd class="font-bold text-gray-900 text-lg">${{ number_format($order->total_amount, 2) }}</dd>
                    </dl>
                </div>
            </div>
        @else
            <div class="bg-red-50 text-red-700 p-4 rounded-md text-center">
                We couldn't find an order with that number. Please check and try again.
            </div>
        @endif
    @endif
</div>
@endsection
