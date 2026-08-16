{{-- ════════════════════════════════
     NEW ARRIVALS — 4-col grid
     ════════════════════════════════ --}}
<section class="py-16">
    <div class="section-wrapper">

        <div class="section-header">
            <h2 class="section-title">Sản Phẩm Mới</h2>
            <a href="{{ route('products.index', ['sort' => 'newest']) }}" class="link-underline">Xem Tất Cả</a>
        </div>

        @if($products->isEmpty())
            <p class="text-sm text-[#888888] text-center py-12">Chưa có sản phẩm nào.</p>
        @else
            <div class="product-grid">
                @foreach($products as $product)
                    <x-product-card :product="$product" />
                @endforeach
            </div>
        @endif

    </div>
</section>
