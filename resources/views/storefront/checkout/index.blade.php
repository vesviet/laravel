@extends('layouts.storefront')

@section('title', 'Checkout')

@section('content')
<div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
    <div class="bg-white rounded-lg shadow-sm p-6 md:p-12">
        <h1 class="text-3xl font-bold text-gray-900 mb-8">Checkout</h1>

        @if(session('error'))
            <div class="bg-red-50 text-red-700 p-4 rounded-md mb-6" role="alert" aria-live="assertive">
                {{ session('error') }}
            </div>
        @endif

        <div class="flex flex-col lg:flex-row gap-12">
            <!-- Checkout Form -->
            <div class="w-full lg:w-2/3">
                <form action="{{ route('checkout.store') }}" method="POST" id="checkout-form">
                    @csrf
                    
                    <h2 class="text-xl font-semibold mb-4 border-b pb-2">Shipping Information</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <div>
                            <label for="customer_name" class="block text-sm font-medium text-gray-700">Full Name *</label>
                            <input type="text" name="customer_name" id="customer_name" value="{{ old('customer_name', $customer?->name) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2 px-3 border">
                            @error('customer_name') <span class="text-red-500 text-xs" role="alert">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700">Phone Number *</label>
                            <input type="text" name="phone" id="phone" value="{{ old('phone', $customer?->phone) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2 px-3 border">
                            @error('phone') <span class="text-red-500 text-xs" role="alert">{{ $message }}</span> @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label for="email" class="block text-sm font-medium text-gray-700">Email Address (Optional)</label>
                            <input type="email" name="email" id="email" value="{{ old('email', $customer?->email) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2 px-3 border">
                            @error('email') <span class="text-red-500 text-xs" role="alert">{{ $message }}</span> @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label for="address" class="block text-sm font-medium text-gray-700">Address *</label>
                            <input type="text" name="address" id="address" value="{{ old('address') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2 px-3 border">
                            @error('address') <span class="text-red-500 text-xs" role="alert">{{ $message }}</span> @enderror
                        </div>
                        
                        <div>
                            <label for="city" class="block text-sm font-medium text-gray-700">City</label>
                            <input type="text" name="city" id="city" value="{{ old('city') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2 px-3 border">
                        </div>
                        
                        <div class="md:col-span-2">
                            <label for="notes" class="block text-sm font-medium text-gray-700">Order Notes</label>
                            <textarea name="notes" id="notes" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2 px-3 border">{{ old('notes') }}</textarea>
                        </div>
                    </div>

                    <h2 class="text-xl font-semibold mb-4 border-b pb-2">Payment Method</h2>
                    
                    <div class="mb-8">
                        <div class="flex items-center">
                            <input id="payment_cod" name="payment_method" type="radio" value="cod" checked class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                            <label for="payment_cod" class="ml-3 block text-sm font-medium text-gray-700">
                                Cash on Delivery (COD)
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-blue-600 border border-transparent rounded-md shadow-sm py-3 px-4 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 min-h-[44px]">
                        Place Order
                    </button>
                </form>
            </div>

            <!-- Order Summary -->
            <div class="w-full lg:w-1/3">
                <div class="bg-gray-50 p-6 rounded-lg border border-gray-200 sticky top-24">
                    <h2 class="text-lg font-semibold mb-4 border-b pb-2">Order Summary</h2>
                    
                    <ul class="divide-y divide-gray-200 mb-6">
                        @foreach($cart as $item)
                        <li class="py-4 flex justify-between">
                            <div class="flex-1 pr-4">
                                <h4 class="text-sm font-medium text-gray-900">{{ $item['name'] }}</h4>
                                @if($item['variant_name'])
                                <p class="text-xs text-gray-500">{{ $item['variant_name'] }}</p>
                                @endif
                                <p class="text-xs text-gray-500">Qty: {{ $item['quantity'] }}</p>
                            </div>
                            <p class="text-sm font-medium text-gray-900">${{ number_format($item['price'] * $item['quantity'], 2) }}</p>
                        </li>
                        @endforeach
                    </ul>
                    
                    <div class="border-t border-gray-200 pt-4 space-y-2">
                        <div class="flex justify-between text-sm">
                            <p class="text-gray-600">Subtotal</p>
                            <p class="font-medium text-gray-900">${{ number_format($subtotal, 2) }}</p>
                        </div>
                        <div class="flex justify-between text-sm">
                            <p class="text-gray-600">Shipping</p>
                            <p class="font-medium text-gray-900">Free</p>
                        </div>
                        <div class="flex justify-between text-base font-bold pt-4 border-t border-gray-200">
                            <p>Total</p>
                            <p>${{ number_format($subtotal, 2) }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
