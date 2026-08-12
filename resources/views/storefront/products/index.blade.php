@extends('layouts.storefront')

@section('title', 'Products')

@section('content')
<div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
    <div class="flex flex-col md:flex-row gap-8">
        <!-- Sidebar Filters -->
        <div class="w-full md:w-1/4">
            <h3 class="text-lg font-semibold mb-4">Categories</h3>
            <ul class="space-y-2">
                <li>
                    <a href="{{ route('products.index') }}" class="inline-block min-w-[44px] min-h-[44px] p-2 text-gray-600 hover:text-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 rounded-md {{ !request('category') ? 'font-bold' : '' }}">
                        All Products
                    </a>
                </li>
                @foreach($categories as $category)
                <li>
                    <a href="{{ route('products.index', ['category' => $category->slug]) }}" class="inline-block min-w-[44px] min-h-[44px] p-2 text-gray-600 hover:text-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 rounded-md {{ request('category') == $category->slug ? 'font-bold' : '' }}">
                        {{ $category->name }}
                    </a>
                </li>
                @endforeach
            </ul>
        </div>

        <!-- Product Grid -->
        <div class="w-full md:w-3/4">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($products as $product)
                <div class="bg-white rounded-lg shadow-sm overflow-hidden hover:shadow-md transition">
                    <a href="{{ route('products.show', $product->slug) }}" class="block focus:outline-none focus:ring-2 focus:ring-blue-500 rounded-t-lg" aria-label="View {{ $product->name }}">
                        @if($product->image_path)
                            <img src="{{ Storage::url($product->image_path) }}" alt="{{ $product->name }}" class="w-full h-48 object-cover">
                        @else
                            <div class="w-full h-48 bg-gray-200 flex items-center justify-center text-gray-500">No Image</div>
                        @endif
                    </a>
                    <div class="p-4">
                        <p class="text-sm text-gray-500 mb-1">{{ $product->category?->name }}</p>
                        <h4 class="text-lg font-semibold mb-2">
                            <a href="{{ route('products.show', $product->slug) }}" class="text-gray-900 hover:text-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 rounded-md p-1">
                                {{ $product->name }}
                            </a>
                        </h4>
                        <div class="flex items-center justify-between">
                            <span class="text-lg font-bold text-gray-900">{{ number_format($product->price, 0, ',', '.') }}₫</span>
                            <a href="{{ route('products.show', $product->slug) }}" class="text-sm text-blue-600 hover:text-blue-800 p-2 min-w-[44px] min-h-[44px] flex items-center justify-center focus:outline-none focus:ring-2 focus:ring-blue-500 rounded-md" aria-hidden="true" tabindex="-1">View Details</a>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-span-full text-center py-12 text-gray-500" role="status">
                    No products found.
                </div>
                @endforelse
            </div>
            
            <div class="mt-8">
                {{ $products->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
