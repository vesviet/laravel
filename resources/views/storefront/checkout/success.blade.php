@extends('layouts.storefront')

@section('title', 'Order Successful')

@section('content')
<div class="max-w-3xl mx-auto py-16 px-4 sm:px-6 lg:px-8 text-center">
    <div class="bg-white rounded-lg shadow-sm p-8 md:p-16">
        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 mb-6">
            <svg class="h-8 w-8 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
        </div>
        
        <h1 class="text-3xl font-extrabold text-gray-900 mb-4">Order Successful!</h1>
        <p class="text-lg text-gray-600 mb-8">Thank you for your purchase, {{ $order->customer_name }}.</p>
        
        <div class="bg-gray-50 rounded-md p-6 mb-8 text-left border border-gray-200">
            <h3 class="text-lg font-medium text-gray-900 mb-4 border-b pb-2">Order Details</h3>
            
            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-6">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Order Number</dt>
                    <dd class="mt-1 text-sm text-gray-900 font-bold">{{ $order->order_number }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Status</dt>
                    <dd class="mt-1 text-sm text-gray-900 capitalize">{{ $order->status }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Total Amount</dt>
                    <dd class="mt-1 text-sm text-gray-900 font-bold">{{ number_format($order->total_amount, 0, ',', '.') }}₫</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Payment Method</dt>
                    <dd class="mt-1 text-sm text-gray-900 capitalize">{{ $order->payment_method === 'cod' ? 'Cash on Delivery' : $order->payment_method }}</dd>
                </div>
            </dl>
        </div>
        
        <div class="flex flex-col sm:flex-row justify-center gap-4">
            <a href="{{ route('track-order.index') }}?order_number={{ $order->order_number }}" class="inline-flex items-center justify-center px-6 py-3 border border-transparent rounded-md shadow-sm text-base font-medium text-blue-700 bg-blue-100 hover:bg-blue-200">
                Track Order
            </a>
            <a href="{{ route('products.index') }}" class="inline-flex items-center justify-center px-6 py-3 border border-transparent rounded-md shadow-sm text-base font-medium text-white bg-blue-600 hover:bg-blue-700">
                Continue Shopping
            </a>
        </div>
    </div>
</div>
@endsection
