@extends('layouts.storefront')

@pushonce('page_title')
    {{ $selectedCategory ? $selectedCategory->name . ' — ' : '' }}
    {{ $searchKeyword ? 'Tìm kiếm: ' . $searchKeyword . ' — ' : '' }}
    Tất Cả Sản Phẩm — 
@endpushonce

@pushonce('meta_description')Khám phá toàn bộ bộ sưu tập nội thất, đèn trang trí và đồ trang trí nhà cửa cao cấp tại {{ config('app.name', 'Sober Furniture') }}.@endpushonce

@section('content')

<div class="py-10 md:py-14 bg-white">
    <div class="section-wrapper">

        {{-- ── Header Section ── --}}
        <div class="mb-10 text-center">
            <h1 class="text-2xl md:text-3xl font-light tracking-wide text-[#23232C] mb-2 uppercase">
                @if($searchKeyword)
                    Kết Quả Tìm Kiếm: "{{ $searchKeyword }}"
                @elseif($selectedCategory)
                    {{ $selectedCategory->name }}
                @else
                    Tất Cả Sản Phẩm
                @endif
            </h1>
            <p class="text-xs md:text-sm text-[#888888] font-light">
                Tìm thấy <strong>{{ $products->total() }}</strong> sản phẩm phù hợp
            </p>
        </div>

        {{-- ── Search & Filter Controls Bar ── --}}
        <div class="mb-8 space-y-6">
            
            {{-- Category Filter Pills --}}
            <div class="flex flex-wrap items-center justify-center gap-2" role="navigation" aria-label="Lọc theo danh mục">
                <a href="{{ route('products.index', array_filter(['sort' => $sort, 'q' => $searchKeyword, 'in_stock' => $inStockOnly ? 1 : null])) }}"
                   class="px-4 py-2 text-xs font-medium uppercase tracking-wider transition-all border {{ empty($selectedCategory) ? 'bg-[#23232C] text-white border-[#23232C]' : 'bg-[#F7F7F7] text-[#23232C] border-[#E5E5E5] hover:border-[#23232C]' }}">
                    Tất Cả
                </a>
                @foreach($categories as $category)
                    <a href="{{ route('products.index', array_filter(['category' => $category->slug, 'sort' => $sort, 'q' => $searchKeyword, 'in_stock' => $inStockOnly ? 1 : null])) }}"
                       class="px-4 py-2 text-xs font-medium uppercase tracking-wider transition-all border {{ ($selectedCategory && $selectedCategory->id === $category->id) ? 'bg-[#23232C] text-white border-[#23232C]' : 'bg-[#F7F7F7] text-[#23232C] border-[#E5E5E5] hover:border-[#23232C]' }}">
                        {{ $category->name }}
                        @if($category->products_count > 0)
                            <span class="text-[10px] opacity-70 ml-1">({{ $category->products_count }})</span>
                        @endif
                    </a>
                @endforeach
            </div>

            {{-- Secondary Bar: Search Form, Stock Toggle & Sort Selector --}}
            <div class="flex flex-col md:flex-row items-stretch md:items-center justify-between gap-4 pt-4 border-t border-[#E5E5E5]">
                
                {{-- Search Input --}}
                <form action="{{ route('products.index') }}" method="GET" class="flex items-center w-full md:w-80 border border-[#E5E5E5] bg-[#F9F9F9] focus-within:border-[#23232C] transition-colors">
                    @if($selectedCategory)
                        <input type="hidden" name="category" value="{{ $selectedCategory->slug }}">
                    @endif
                    @if($sort)
                        <input type="hidden" name="sort" value="{{ $sort }}">
                    @endif
                    @if($inStockOnly)
                        <input type="hidden" name="in_stock" value="1">
                    @endif

                    <input type="text"
                           name="q"
                           value="{{ $searchKeyword }}"
                           placeholder="Tìm tên sản phẩm, mã SKU..."
                           class="w-full bg-transparent px-3.5 py-2 text-xs text-[#23232C] placeholder-[#888888] outline-none">
                    
                    <button type="submit" class="px-3 text-[#888888] hover:text-[#23232C] transition-colors" aria-label="Tìm kiếm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                        </svg>
                    </button>
                </form>

                {{-- In-stock Toggle & Sorter --}}
                <div class="flex flex-wrap items-center justify-between md:justify-end gap-4">
                    
                    {{-- In-stock checkbox --}}
                    <label class="flex items-center gap-2 cursor-pointer select-none text-xs text-[#23232C]">
                        <input type="checkbox"
                               class="rounded-none border-[#D1D5DB] text-[#23232C] focus:ring-0 cursor-pointer"
                               {{ $inStockOnly ? 'checked' : '' }}
                               onchange="window.location.href = '{{ route('products.index', array_filter(['category' => request('category'), 'sort' => $sort, 'q' => $searchKeyword, 'in_stock' => $inStockOnly ? null : 1])) }}'">
                        <span>Chỉ sản phẩm còn hàng</span>
                    </label>

                    {{-- Sort Selector --}}
                    <div class="flex items-center gap-2">
                        <label for="sort-select" class="text-[10px] tracking-[0.15em] uppercase text-[#888888]">Sắp Xếp</label>
                        <select id="sort-select"
                                class="text-xs border border-[#E5E5E5] bg-white px-3 py-2 outline-none focus:border-[#23232C] transition-colors cursor-pointer"
                                onchange="window.location.href = this.value">
                            <option value="{{ route('products.index', array_filter(['category' => request('category'), 'q' => $searchKeyword, 'in_stock' => $inStockOnly ? 1 : null, 'sort' => 'newest'])) }}"
                                    {{ $sort === 'newest' ? 'selected' : '' }}>
                                Mới Nhất
                            </option>
                            <option value="{{ route('products.index', array_filter(['category' => request('category'), 'q' => $searchKeyword, 'in_stock' => $inStockOnly ? 1 : null, 'sort' => 'featured'])) }}"
                                    {{ $sort === 'featured' ? 'selected' : '' }}>
                                Sản Phẩm Nổi Bật
                            </option>
                            <option value="{{ route('products.index', array_filter(['category' => request('category'), 'q' => $searchKeyword, 'in_stock' => $inStockOnly ? 1 : null, 'sort' => 'price_asc'])) }}"
                                    {{ $sort === 'price_asc' ? 'selected' : '' }}>
                                Giá: Thấp Đến Cao
                            </option>
                            <option value="{{ route('products.index', array_filter(['category' => request('category'), 'q' => $searchKeyword, 'in_stock' => $inStockOnly ? 1 : null, 'sort' => 'price_desc'])) }}"
                                    {{ $sort === 'price_desc' ? 'selected' : '' }}>
                                Giá: Cao Đến Thấp
                            </option>
                            <option value="{{ route('products.index', array_filter(['category' => request('category'), 'q' => $searchKeyword, 'in_stock' => $inStockOnly ? 1 : null, 'sort' => 'name_asc'])) }}"
                                    {{ $sort === 'name_asc' ? 'selected' : '' }}>
                                Tên: A — Z
                            </option>
                        </select>
                    </div>

                </div>

            </div>

            {{-- Active Filter Tags Row (if any filter active) --}}
            @if($activeFiltersCount > 0)
                <div class="flex items-center flex-wrap gap-2 pt-2 text-xs">
                    <span class="text-[#888888] font-light">Bộ lọc đang chọn:</span>
                    
                    @if($selectedCategory)
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-[#F0F0F0] text-[#23232C] border border-[#E5E5E5]">
                            Danh mục: <strong>{{ $selectedCategory->name }}</strong>
                            <a href="{{ route('products.index', array_filter(['sort' => $sort, 'q' => $searchKeyword, 'in_stock' => $inStockOnly ? 1 : null])) }}" class="text-[#888888] hover:text-[#E84444]">×</a>
                        </span>
                    @endif

                    @if($searchKeyword)
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-[#F0F0F0] text-[#23232C] border border-[#E5E5E5]">
                            Từ khóa: <strong>"{{ $searchKeyword }}"</strong>
                            <a href="{{ route('products.index', array_filter(['category' => request('category'), 'sort' => $sort, 'in_stock' => $inStockOnly ? 1 : null])) }}" class="text-[#888888] hover:text-[#E84444]">×</a>
                        </span>
                    @endif

                    @if($inStockOnly)
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-[#F0F0F0] text-[#23232C] border border-[#E5E5E5]">
                            <strong>Chỉ còn hàng</strong>
                            <a href="{{ route('products.index', array_filter(['category' => request('category'), 'sort' => $sort, 'q' => $searchKeyword])) }}" class="text-[#888888] hover:text-[#E84444]">×</a>
                        </span>
                    @endif

                    <a href="{{ route('products.index') }}" class="ml-2 text-xs text-[#E84444] hover:underline font-medium">
                        Xóa tất cả bộ lọc
                    </a>
                </div>
            @endif

        </div>

        {{-- ── Product Grid ── --}}
        @if($products->isEmpty())
            <div class="py-24 text-center bg-[#F9F9F9] border border-[#E5E5E5] p-8" role="status">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 mx-auto text-[#888888] mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                </svg>
                <h3 class="text-base font-medium text-[#23232C] mb-2">Không tìm thấy sản phẩm nào.</h3>
                <p class="text-xs text-[#888888] max-w-md mx-auto mb-6">
                    Không có sản phẩm nào phù hợp với điều kiện tìm kiếm của bạn. Hãy thử thay đổi danh mục hoặc bỏ các bộ lọc đang áp dụng.
                </p>
                <a href="{{ route('products.index') }}" class="inline-block px-6 py-2.5 text-xs font-semibold uppercase tracking-wider bg-[#23232C] text-white hover:bg-black transition-colors">
                    Xem tất cả sản phẩm
                </a>
            </div>
        @else
            <div class="product-grid">
                @foreach($products as $product)
                    <x-product-card :product="$product" />
                @endforeach
            </div>

            {{-- ── Pagination ── --}}
            @if($products->hasPages())
                <div class="mt-14 flex justify-center">
                    {{ $products->links() }}
                </div>
            @endif
        @endif

    </div>
</div>

@endsection
