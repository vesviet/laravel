{{-- ════════════════════════════════
     FEATURED PRODUCTS — 4-col grid
     ════════════════════════════════ --}}
<section class="py-16">
    <div class="section-wrapper">

        <div class="section-header">
            <h2 class="section-title">Sản Phẩm Nổi Bật</h2>
            <a href="{{ route('products.index') }}" class="link-underline">Xem Tất Cả</a>
        </div>

        @if($products->isEmpty())
            <div class="py-20 text-center">
                <p class="text-sm text-[#888888]">Chưa có sản phẩm nổi bật. Hãy đánh dấu sản phẩm trong trang quản trị.</p>
                <a href="{{ route('products.index') }}" class="btn-dark inline-block mt-6">Xem Tất Cả Sản Phẩm</a>
            </div>
        @else
            <div class="product-grid">
                @foreach($products as $product)
                    <x-product-card :product="$product" />
                @endforeach
            </div>
        @endif

    </div>
</section>
