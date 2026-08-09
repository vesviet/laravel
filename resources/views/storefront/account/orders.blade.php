@extends('layouts.storefront')

@section('title', 'My Orders')

@section('content')
<div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
    <div class="md:flex md:items-center md:justify-between mb-8">
        <div class="flex-1 min-w-0">
            <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                My Account
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                Welcome back, {{ auth('customer')->user()->name }}.
            </p>
        </div>
    </div>

    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6 flex justify-between items-center">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
                Order History
            </h3>
        </div>
        
        <div class="border-t border-gray-200">
            @if($orders->count() > 0)
                <ul role="list" class="divide-y divide-gray-200">
                    @foreach($orders as $order)
                        <li>
                            <a href="{{ route('track-order.index', ['order_number' => $order->order_number]) }}" class="block hover:bg-gray-50">
                                <div class="px-4 py-4 sm:px-6">
                                    <div class="flex items-center justify-between">
                                        <p class="text-sm font-medium text-blue-600 truncate">
                                            Order {{ $order->order_number }}
                                        </p>
                                        <div class="ml-2 flex-shrink-0 flex">
                                            <p class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full capitalize
                                                @if($order->status == 'pending') bg-yellow-100 text-yellow-800
                                                @elseif($order->status == 'confirmed') bg-blue-100 text-blue-800
                                                @elseif($order->status == 'shipping') bg-indigo-100 text-indigo-800
                                                @elseif($order->status == 'delivered') bg-green-100 text-green-800
                                                @else bg-red-100 text-red-800 @endif
                                            ">
                                                {{ $order->status }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="mt-2 sm:flex sm:justify-between">
                                        <div class="sm:flex">
                                            <p class="flex items-center text-sm text-gray-500">
                                                {{ $order->items->count() }} {{ Str::plural('item', $order->items->count()) }}
                                            </p>
                                        </div>
                                        <div class="mt-2 flex items-center text-sm text-gray-500 sm:mt-0">
                                            <p>
                                                Placed on <time datetime="{{ $order->created_at->format('Y-m-d') }}">{{ $order->created_at->format('M d, Y') }}</time>
                                            </p>
                                            <p class="ml-4 font-bold text-gray-900">
                                                ${{ number_format($order->total_amount, 2) }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </li>
                    @endforeach
                </ul>
            @else
                <div class="text-center py-12 px-4 sm:px-6">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No orders</h3>
                    <p class="mt-1 text-sm text-gray-500">You haven't placed any orders yet.</p>
                    <div class="mt-6">
                        <a href="{{ route('products.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Start Shopping
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
