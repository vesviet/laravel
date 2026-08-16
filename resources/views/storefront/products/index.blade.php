@extends('layouts.storefront')

@pushonce('page_title')Tất Cả Sản Phẩm @endpushonce
@pushonce('meta_description')Khám phá toàn bộ bộ sưu tập sản phẩm tại MYSHOP. Chất lượng cao, thiết kế tinh tế, giao hàng toàn quốc.@endpushonce

@section('content')

<div class="py-12">
    <div class="section-wrapper">

        {{-- ── Page heading ── --}}
        <div class="mb-10 text-center">
            <h1 class="text-2xl font-medium tracking-wide mb-2">
                {{ request('category')
                    ? $categories->firstWhere('slug', request('category'))?->name ?? 'Sản Phẩm'
                    : 'Tất Cả Sản Phẩm' }}
            </h1>
            <p class="text-sm text-[#888888]">{{ $products->total() }} sản phẩm</p>
        </div>

        {{-- ── Filter + Sort Bar ── --}}
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-8 pb-6 border-b border-[#E5E5E5]">

            {{-- Category pills --}}
            <div class="flex flex-wrap gap-2" role="navigation" aria-label="Lọc theo danh mục">
                <a href="{{ route('products.index', array_filter(['sort' => request('sort')])) }}"
                   class="filter-pill {{ !request('category') ? 'active' : '' }}">
                    Tất Cả
                </a>
                @foreach($categories as $category)
                    <a href="{{ route('products.index', array_filter(['category' => $category->slug, 'sort' => request('sort')])) }}"
                       class="filter-pill {{ request('category') === $category->slug ? 'active' : '' }}">
                        {{ $category->name }}
                    </a>
                @endforeach
            </div>

            {{-- Sort select --}}
            <div class="flex items-center gap-3 shrink-0">
                <label for="sort-select" class="text-[10px] tracking-[0.15em] uppercase text-[#888888]">Sắp Xếp</label>
                <select id="sort-select"
                        class="text-[11px] tracking-wide border border-[#E5E5E5] bg-white px-3 py-2 outline-none focus:border-[#1a1a1a] transition-colors cursor-pointer"
                        onchange="window.location.href = this.value">
                    <option value="{{ route('products.index', array_filter(['category' => request('category'), 'sort' => 'newest'])) }}"
                            {{ $sort === 'newest' ? 'selected' : '' }}>
                        Mới Nhất
                    </option>
                    <option value="{{ route('products.index', array_filter(['category' => request('category'), 'sort' => 'price_asc'])) }}"
                            {{ $sort === 'price_asc' ? 'selected' : '' }}>
                        Giá Tăng Dần
                    </option>
                    <option value="{{ route('products.index', array_filter(['category' => request('category'), 'sort' => 'price_desc'])) }}"
                            {{ $sort === 'price_desc' ? 'selected' : '' }}>
                        Giá Giảm Dần
                    </option>
                </select>
            </div>
        </div>

        {{-- ── Product Grid ── --}}
        @if($products->isEmpty())
            <div class="py-24 text-center" role="status">
                <p class="text-sm text-[#888888] mb-6">Không tìm thấy sản phẩm nào.</p>
                <a href="{{ route('products.index') }}" class="link-underline">Xem tất cả sản phẩm</a>
            </div>
        @else
            <div class="product-grid">
                @foreach($products as $product)
                    <x-product-card :product="$product" />
                @endforeach
            </div>

            {{-- ── Pagination ── --}}
            @if($products->hasPages())
                <div class="mt-12 flex justify-center">
                    {{ $products->links() }}
                </div>
            @endif
        @endif

    </div>
</div>

@endsection
