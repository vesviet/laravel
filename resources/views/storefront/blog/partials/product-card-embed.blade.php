<div class="my-8 p-4 sm:p-5 bg-[#F9F9F9] border border-[#E5E5E5] rounded-none flex flex-col sm:flex-row items-center gap-4 sm:gap-6 not-prose shadow-sm hover:border-[#23232C] transition-colors">
    {{-- Thumbnail --}}
    <a href="{{ route('products.show', $product->slug) }}" class="w-full sm:w-28 h-28 shrink-0 bg-white border border-[#EBEBEB] overflow-hidden flex items-center justify-center group">
        @if($product->image_path)
            <img src="{{ asset('storage/' . $product->image_path) }}" 
                 alt="{{ $product->name }}" 
                 class="w-full h-full object-contain p-2 group-hover:scale-105 transition-transform duration-300">
        @else
            <div class="w-full h-full bg-[#F0F0F0] flex items-center justify-center text-xs text-[#888888]">No image</div>
        @endif
    </a>

    {{-- Product Information --}}
    <div class="flex-1 min-w-0 text-center sm:text-left">
        <div class="flex items-center justify-center sm:justify-start gap-2 mb-1">
            <span class="text-[10px] uppercase tracking-wider font-semibold px-2 py-0.5 bg-[#23232C] text-white">Sản phẩm gợi ý</span>
            @if($product->category)
                <span class="text-xs text-[#888888]">{{ $product->category->name }}</span>
            @endif
        </div>
        
        <h4 class="text-base font-semibold text-[#23232C] truncate hover:text-[#E84444] transition-colors">
            <a href="{{ route('products.show', $product->slug) }}">{{ $product->name }}</a>
        </h4>

        <div class="mt-1 flex items-center justify-center sm:justify-start gap-3">
            <span class="text-sm font-bold text-[#E84444]">{{ number_format($product->price, 0, ',', '.') }}₫</span>
            @if($product->stock > 0)
                <span class="text-[11px] text-emerald-600 font-medium">Còn {{ $product->stock }} sản phẩm</span>
            @else
                <span class="text-[11px] text-[#888888]">Hết hàng</span>
            @endif
        </div>
    </div>

    {{-- Action CTA --}}
    <div class="shrink-0 flex items-center gap-2 w-full sm:w-auto">
        <a href="{{ route('products.show', $product->slug) }}" 
           class="flex-1 sm:flex-none px-4 py-2.5 text-xs font-semibold uppercase tracking-wider border border-[#23232C] text-[#23232C] hover:bg-[#23232C] hover:text-white transition-all text-center">
            Xem chi tiết
        </a>
    </div>
</div>
