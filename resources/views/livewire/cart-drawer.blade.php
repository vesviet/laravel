<div>
    <!-- Background backdrop -->
    <div x-data="{ open: @entangle('isOpen') }" 
         x-show="open" 
         x-transition:enter="ease-in-out duration-500" 
         x-transition:enter-start="opacity-0" 
         x-transition:enter-end="opacity-100" 
         x-transition:leave="ease-in-out duration-500" 
         x-transition:leave-start="opacity-100" 
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity z-50" 
         style="display: none;" 
         aria-hidden="true" 
         @click="open = false"></div>

    <!-- Drawer panel -->
    <div x-data="{ open: @entangle('isOpen') }" 
         x-show="open" 
         x-transition:enter="transform transition ease-in-out duration-500 sm:duration-700" 
         x-transition:enter-start="translate-x-full" 
         x-transition:enter-end="translate-x-0" 
         x-transition:leave="transform transition ease-in-out duration-500 sm:duration-700" 
         x-transition:leave-start="translate-x-0" 
         x-transition:leave-end="translate-x-full"
         class="fixed inset-y-0 right-0 max-w-full flex z-50 pointer-events-none" 
         style="display: none;">
        
        <div class="w-screen max-w-md pointer-events-auto" role="dialog" aria-modal="true" aria-labelledby="slide-over-title">
            <div class="h-full flex flex-col bg-white shadow-xl overflow-y-scroll">
                <div class="flex-1 py-6 overflow-y-auto px-4 sm:px-6">
                    <div class="flex items-start justify-between">
                        <h2 class="text-lg font-medium text-gray-900" id="slide-over-title">Shopping cart</h2>
                        <div class="ml-3 h-7 flex items-center">
                            <button type="button" class="-m-2 p-2 text-gray-400 hover:text-gray-500 min-w-[44px] min-h-[44px] flex items-center justify-center focus:outline-none focus:ring-2 focus:ring-blue-500 rounded-md" wire:click="closeCart">
                                <span class="sr-only">Close panel</span>
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="mt-8">
                        <div class="flow-root">
                            @if(count($cart) > 0)
                            <ul role="list" aria-live="polite" class="-my-6 divide-y divide-gray-200">
                                @foreach($cart as $key => $item)
                                <li class="py-6 flex">
                                    <div class="flex-shrink-0 w-24 h-24 border border-gray-200 rounded-md overflow-hidden">
                                        @if($item['image_path'])
                                            <img src="{{ Storage::url($item['image_path']) }}" alt="{{ $item['name'] }}" class="w-full h-full object-center object-cover">
                                        @else
                                            <div class="w-full h-full bg-gray-200 flex items-center justify-center text-xs text-gray-500">No Image</div>
                                        @endif
                                    </div>

                                    <div class="ml-4 flex-1 flex flex-col">
                                        <div>
                                            <div class="flex justify-between text-base font-medium text-gray-900">
                                                <h3>{{ $item['name'] }}</h3>
                                                <p class="ml-4">${{ number_format($item['price'], 2) }}</p>
                                            </div>
                                            @if($item['variant_name'])
                                            <p class="mt-1 text-sm text-gray-500">{{ $item['variant_name'] }}</p>
                                            @endif
                                        </div>
                                        <div class="flex-1 flex items-end justify-between text-sm">
                                            <div class="flex items-center border border-gray-300 rounded">
                                                <button type="button" wire:click="updateQuantity('{{ $key }}', {{ $item['quantity'] - 1 }})" aria-label="Decrease quantity" class="px-2 py-1 min-w-[32px] min-h-[32px] flex items-center justify-center text-gray-600 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 rounded-l">-</button>
                                                <span class="px-2 min-w-[32px] text-center" aria-live="polite">{{ $item['quantity'] }}</span>
                                                <button type="button" wire:click="updateQuantity('{{ $key }}', {{ $item['quantity'] + 1 }})" aria-label="Increase quantity" class="px-2 py-1 min-w-[32px] min-h-[32px] flex items-center justify-center text-gray-600 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 rounded-r">+</button>
                                            </div>

                                            <div class="flex">
                                                <button type="button" wire:click="removeItem('{{ $key }}')" aria-label="Remove {{ $item['name'] }}" class="font-medium text-blue-600 hover:text-blue-500 p-2 min-w-[44px] min-h-[44px] focus:outline-none focus:ring-2 focus:ring-blue-500 rounded-md">Remove</button>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                @endforeach
                            </ul>
                            @else
                            <div class="text-center py-12">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                <p class="mt-4 text-gray-500 text-lg">Your cart is empty.</p>
                                <button type="button" wire:click="closeCart" class="mt-4 text-blue-600 hover:text-blue-500 font-medium p-2 min-w-[44px] min-h-[44px] focus:outline-none focus:ring-2 focus:ring-blue-500 rounded-md">Continue Shopping</button>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                @if(count($cart) > 0)
                <div class="border-t border-gray-200 py-6 px-4 sm:px-6">
                    <div class="flex justify-between text-base font-medium text-gray-900">
                        <p>Subtotal</p>
                        <p>${{ number_format($this->subtotal, 2) }}</p>
                    </div>
                    <p class="mt-0.5 text-sm text-gray-500">Shipping and taxes calculated at checkout.</p>
                    <div class="mt-6">
                        <a href="{{ route('checkout.index') }}" class="flex justify-center items-center px-6 py-3 border border-transparent rounded-md shadow-sm text-base font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 min-h-[44px]">Checkout</a>
                    </div>
                    <div class="mt-6 flex justify-center text-sm text-center text-gray-500">
                        <p>
                            or <button type="button" wire:click="closeCart" class="text-blue-600 font-medium hover:text-blue-500 p-2 focus:outline-none focus:ring-2 focus:ring-blue-500 rounded-md min-h-[44px]">Continue Shopping<span aria-hidden="true"> &rarr;</span></button>
                        </p>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
