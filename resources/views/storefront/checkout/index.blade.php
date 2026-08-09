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
                    
                    <h2 class="text-xl font-semibold mb-4 border-b pb-2">Thông tin giao hàng</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <div>
                            <label for="customer_name" class="block text-sm font-medium text-gray-700">Họ và tên *</label>
                            <input type="text" name="customer_name" id="customer_name" value="{{ old('customer_name', $customer?->name) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2 px-3 border">
                            @error('customer_name') <span class="text-red-500 text-xs" role="alert">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700">Số điện thoại *</label>
                            <input type="text" name="phone" id="phone" value="{{ old('phone', $customer?->phone) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2 px-3 border">
                            @error('phone') <span class="text-red-500 text-xs" role="alert">{{ $message }}</span> @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label for="email" class="block text-sm font-medium text-gray-700">Email (Tuỳ chọn)</label>
                            <input type="email" name="email" id="email" value="{{ old('email', $customer?->email) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2 px-3 border">
                            @error('email') <span class="text-red-500 text-xs" role="alert">{{ $message }}</span> @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label for="address" class="block text-sm font-medium text-gray-700">Địa chỉ *</label>
                            <input type="text" name="address" id="address" value="{{ old('address') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2 px-3 border">
                            @error('address') <span class="text-red-500 text-xs" role="alert">{{ $message }}</span> @enderror
                        </div>
                        
                        <!-- Province Dropdown -->
                        <div>
                            <label for="city" class="block text-sm font-medium text-gray-700">Tỉnh / Thành phố</label>
                            <select name="city" id="city" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2 px-3 border bg-white">
                                <option value="">-- Chọn tỉnh/thành --</option>
                                @foreach($provinces as $province)
                                    <option value="{{ $province->name }}" {{ old('city') === $province->name ? 'selected' : '' }}>
                                        {{ $province->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="district" class="block text-sm font-medium text-gray-700">Quận / Huyện</label>
                            <input type="text" name="district" id="district" value="{{ old('district') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2 px-3 border">
                        </div>

                        <div>
                            <label for="ward" class="block text-sm font-medium text-gray-700">Phường / Xã</label>
                            <input type="text" name="ward" id="ward" value="{{ old('ward') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2 px-3 border">
                        </div>
                        
                        <div class="md:col-span-2">
                            <label for="notes" class="block text-sm font-medium text-gray-700">Ghi chú đơn hàng</label>
                            <textarea name="notes" id="notes" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2 px-3 border">{{ old('notes') }}</textarea>
                        </div>
                    </div>

                    <h2 class="text-xl font-semibold mb-4 border-b pb-2">Phương thức thanh toán</h2>
                    
                    <div class="mb-8">
                        <div class="flex items-center">
                            <input id="payment_cod" name="payment_method" type="radio" value="cod" checked class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                            <label for="payment_cod" class="ml-3 block text-sm font-medium text-gray-700">
                                Thanh toán khi nhận hàng (COD)
                            </label>
                        </div>
                    </div>

                    <!-- Coupon Section -->
                    <div class="mb-8">
                        <h2 class="text-xl font-semibold mb-4 border-b pb-2">Mã giảm giá</h2>
                        @livewire('coupon-input', ['subtotal' => $subtotal])
                    </div>

                    <button type="submit" id="place-order-btn" class="w-full bg-blue-600 border border-transparent rounded-md shadow-sm py-3 px-4 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 min-h-[44px]">
                        Đặt hàng
                    </button>
                </form>
            </div>

            <!-- Order Summary -->
            <div class="w-full lg:w-1/3">
                <div class="bg-gray-50 p-6 rounded-lg border border-gray-200 sticky top-24">
                    <h2 class="text-lg font-semibold mb-4 border-b pb-2">Tóm tắt đơn hàng</h2>
                    
                    <ul class="divide-y divide-gray-200 mb-6">
                        @foreach($cart as $item)
                        <li class="py-4 flex justify-between">
                            <div class="flex-1 pr-4">
                                <h4 class="text-sm font-medium text-gray-900">{{ $item['product_name'] }}</h4>
                                @if($item['variant_name'])
                                <p class="text-xs text-gray-500">{{ $item['variant_name'] }}</p>
                                @endif
                                <p class="text-xs text-gray-500">Qty: {{ $item['quantity'] }}</p>
                            </div>
                            <p class="text-sm font-medium text-gray-900">{{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}₫</p>
                        </li>
                        @endforeach
                    </ul>
                    
                    <div class="border-t border-gray-200 pt-4 space-y-2" id="order-totals">
                        <div class="flex justify-between text-sm">
                            <p class="text-gray-600">Tạm tính</p>
                            <p class="font-medium text-gray-900">{{ number_format($subtotal, 0, ',', '.') }}₫</p>
                        </div>
                        <div class="flex justify-between text-sm">
                            <p class="text-gray-600">Phí vận chuyển</p>
                            <p class="font-medium text-gray-900">Miễn phí</p>
                        </div>
                        @if($appliedCoupon)
                        <div class="flex justify-between text-sm text-green-600" id="discount-row">
                            <p>Giảm giá</p>
                            <p class="font-medium" id="discount-amount">Đang tính...</p>
                        </div>
                        @endif
                        <div class="flex justify-between text-base font-bold pt-4 border-t border-gray-200">
                            <p>Tổng cộng</p>
                            <p id="total-display">{{ number_format($subtotal, 0, ',', '.') }}₫</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Listen for Livewire coupon events and update the summary sidebar
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('coupon-applied', (event) => {
            const discount = event.discount;
            const subtotal = {{ $subtotal }};
            const newTotal = Math.max(0, subtotal - discount);

            // Show or update discount row
            let discountRow = document.getElementById('discount-row');
            if (!discountRow) {
                const totals = document.getElementById('order-totals');
                const totalRow = totals.querySelector('.border-t.border-gray-200.pt-4');
                discountRow = document.createElement('div');
                discountRow.id = 'discount-row';
                discountRow.className = 'flex justify-between text-sm text-green-600';
                discountRow.innerHTML = `<p>Giảm giá</p><p class="font-medium" id="discount-amount"></p>`;
                totals.insertBefore(discountRow, totalRow);
            }

            const fmt = (n) => new Intl.NumberFormat('vi-VN').format(n) + '₫';
            document.getElementById('discount-amount').textContent = '-' + fmt(discount);
            document.getElementById('total-display').textContent = fmt(newTotal);
        });

        Livewire.on('coupon-removed', () => {
            const subtotal = {{ $subtotal }};
            const discountRow = document.getElementById('discount-row');
            if (discountRow) discountRow.remove();
            document.getElementById('total-display').textContent =
                new Intl.NumberFormat('vi-VN').format(subtotal) + '₫';
        });
    });
</script>
@endpush
@endsection
