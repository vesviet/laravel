<div>
    @if($product->variants->count() > 0)
    <div class="mb-6">
        <label for="variant" class="block text-sm font-medium text-gray-700 mb-2">Options</label>
        <select wire:model.live="variantId" id="variant" class="block w-full rounded-md border-gray-300 py-2 pl-3 pr-10 text-base focus:border-blue-500 focus:outline-none focus:ring-blue-500 sm:text-sm">
            @foreach($product->variants as $variant)
                <option value="{{ $variant->id }}">{{ $variant->name }} - {{ number_format($variant->price, 0, ',', '.') }}₫</option>
            @endforeach
        </select>
    </div>
    @endif

    <div class="flex items-center gap-4 mb-6">
        <label for="quantity" class="block text-sm font-medium text-gray-700 sr-only">Quantity</label>
        <div class="flex items-center border border-gray-300 rounded-md">
            <button type="button" wire:click="decrement" aria-label="Decrease quantity" class="px-4 py-2 text-gray-600 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 min-w-[44px] min-h-[44px] rounded-l-md">-</button>
            <input type="number" wire:model="quantity" id="quantity" min="1" class="w-16 border-0 text-center py-2 focus:ring-0 sm:text-sm" readonly aria-live="polite">
            <button type="button" wire:click="increment" aria-label="Increase quantity" class="px-4 py-2 text-gray-600 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 min-w-[44px] min-h-[44px] rounded-r-md">+</button>
        </div>
        <div class="text-sm text-gray-500">
            @if($product->stock > 0)
                <span class="text-green-600">{{ $product->stock }} in stock</span>
            @else
                <span class="text-red-600">Out of stock</span>
            @endif
        </div>
    </div>

    <button type="button" wire:click="addToCart" @if($product->stock <= 0) disabled @endif
        class="relative w-full bg-blue-600 border border-transparent rounded-md shadow-sm py-3 px-4 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:bg-gray-400 disabled:cursor-not-allowed min-h-[44px]">
        <span wire:loading.remove wire:target="addToCart">Add to Cart</span>
        <span wire:loading wire:target="addToCart" class="flex items-center justify-center">
            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Adding...
        </span>
    </button>
</div>
