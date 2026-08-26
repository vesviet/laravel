<div class="max-w-md mx-auto bg-white rounded-xl shadow-md overflow-hidden md:max-w-2xl mt-10">
    <div class="md:flex">
        <div class="md:shrink-0">
            <img class="h-48 w-full object-cover md:h-full md:w-48" src="{{ $product->primary_image_url ?? 'https://placehold.co/400' }}" alt="{{ $product->name }}">
        </div>
        <div class="p-8 w-full">
            <div class="uppercase tracking-wide text-sm text-indigo-500 font-semibold">{{ \Spatie\Multitenancy\Models\Tenant::current()->shop_name }}</div>
            <h1 class="block mt-1 text-lg leading-tight font-medium text-black">{{ $product->name }}</h1>
            <p class="mt-2 text-slate-500">{{ $product->formatted_price }}</p>

            @if($orderComplete)
                <div class="mt-6 bg-green-50 p-4 rounded-lg border border-green-200">
                    <h3 class="text-green-800 font-bold mb-2">🎉 Đặt hàng thành công!</h3>
                    <p class="text-green-700 text-sm">Mã đơn hàng của bạn: <strong>{{ $orderNumber }}</strong></p>
                    
                    @if($qrUrl)
                        <div class="mt-4 text-center">
                            <p class="text-sm font-medium text-gray-700 mb-2">Quét mã VietQR để thanh toán</p>
                            <img src="{{ $qrUrl }}" alt="VietQR" class="mx-auto w-48 h-48 rounded-lg shadow-sm border">
                        </div>
                    @else
                        <p class="text-green-700 text-sm mt-2">Chúng tôi sẽ liên hệ với bạn trong thời gian sớm nhất để xác nhận đơn hàng.</p>
                    @endif
                </div>
            @else
                <form wire:submit="submit" class="mt-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Số lượng</label>
                        <input type="number" wire:model="quantity" min="1" max="{{ $product->stock }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        @error('quantity') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Họ tên</label>
                        <input type="text" wire:model="customer_name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        @error('customer_name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Số điện thoại</label>
                        <input type="tel" wire:model="phone" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        @error('phone') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Địa chỉ giao hàng</label>
                        <textarea wire:model="address" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"></textarea>
                        @error('address') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Phương thức thanh toán</label>
                        <select wire:model="payment_method" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <option value="cod">Thanh toán khi nhận hàng (COD)</option>
                            @if(\Spatie\Multitenancy\Models\Tenant::current()->bank_code)
                                <option value="vietqr">Chuyển khoản (VietQR)</option>
                            @endif
                        </select>
                    </div>

                    <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <span wire:loading.remove wire:target="submit">Đặt hàng ngay</span>
                        <span wire:loading wire:target="submit">Đang xử lý...</span>
                    </button>
                </form>
            @endif
        </div>
    </div>
</div>
