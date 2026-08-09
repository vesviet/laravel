<div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
    <h2 class="text-2xl font-bold mb-6">My Wishlist</h2>

    @if($wishlists->isEmpty())
        <div class="bg-white p-6 rounded-lg shadow-sm text-center text-gray-500">
            You haven't added any products to your wishlist yet.
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($wishlists as $wishlist)
                @if($wishlist->product)
                    <div class="bg-white rounded-lg shadow-sm overflow-hidden flex flex-col">
                        <img src="{{ $wishlist->product->image_path ?: 'https://placehold.co/400x400' }}" alt="{{ $wishlist->product->name }}" class="w-full h-48 object-cover">
                        <div class="p-4 flex flex-col flex-grow">
                            <h3 class="text-lg font-medium text-gray-900">{{ $wishlist->product->name }}</h3>
                            <p class="text-gray-500 mt-1">${{ number_format($wishlist->product->price, 2) }}</p>
                            
                            <div class="mt-4 flex justify-between items-center mt-auto">
                                <button wire:click="removeFromWishlist({{ $wishlist->id }})" class="text-sm text-red-500 hover:text-red-700">
                                    Remove
                                </button>
                                
                                <livewire:add-to-cart-button :product="$wishlist->product" :key="'add-to-cart-'.$wishlist->product->id" />
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    @endif
</div>
