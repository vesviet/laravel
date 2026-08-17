{{-- ════════════════════════════════════════════════════════
     SECTION 5: NEW ARRIVALS (4-Column Grid)
     Sober Home v12: Section Header + Arrow Link + Products Grid
     ════════════════════════════════════════════════════════ --}}
<section class="py-12 md:py-16">
    <div class="section-wrapper">

        {{-- Section Header --}}
        <div class="section-header flex items-end justify-between mb-8 pb-3 border-b border-[#E5E5E5]/60">
            <div>
                <span class="text-[10px] font-semibold tracking-[0.2em] uppercase text-[#888888] block mb-1">New Trend</span>
                <h2 class="section-title text-2xl md:text-3xl font-medium tracking-wide text-[#23232C]">Sản Phẩm Mới</h2>
            </div>
            <a href="{{ route('products.index', ['sort' => 'newest']) }}"
               class="link-arrow text-xs tracking-[0.15em] uppercase font-medium group flex items-center gap-1.5 text-[#23232C] hover:opacity-70 transition-opacity">
                <span>Xem Tất Cả</span>
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 transition-transform duration-200 group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                </svg>
            </a>
        </div>

        {{-- Product Grid / Empty State --}}
        @if($products->isEmpty())
            <p class="text-sm text-[#888888] text-center py-12">Chưa có sản phẩm nào.</p>
        @else
            <div class="product-grid grid grid-cols-2 md:grid-cols-4 gap-6 md:gap-8">
                @foreach($products as $product)
                    <x-product-card :product="$product" />
                @endforeach
            </div>
        @endif

    </div>
</section>
