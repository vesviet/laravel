{{-- ════════════════════════════════════════════════════════
     SECTION 6: FEATURED COLLECTIONS (3-Column Grid)
     Sober Home v12: "Copenhague Desk", "Cement Wood Lamp", "Arte 60 Stool"
     ════════════════════════════════════════════════════════ --}}
<section class="py-16 md:py-20 bg-white">
    <div class="section-wrapper">

        {{-- Centered Section Header --}}
        <div class="text-center max-w-2xl mx-auto mb-12">
            <span class="text-[10px] font-semibold tracking-[0.25em] uppercase text-[#888888] block mb-2">Curated Spaces</span>
            <h2 class="text-2xl md:text-3xl font-medium tracking-tight text-[#23232C] mb-3">Bộ Sưu Tập Tiêu Biểu</h2>
            <p class="text-xs md:text-sm text-[#888888] font-light leading-relaxed">
                Khám phá các thiết kế nội thất Scandinavian tiêu biểu mang phong cách tối giản và ấm cúng
            </p>
        </div>

        {{-- 3-Column Card Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 md:gap-8">

            {{-- Card 1: Copenhague Desk / Đồ Nội Thất --}}
            <a href="{{ route('products.index') }}"
               class="group relative overflow-hidden block h-[380px] md:h-[440px] bg-[#E8E4DF]"
               aria-label="Bộ sưu tập Copenhague Desk — Đồ Nội Thất">
                <div class="absolute inset-0 bg-gradient-to-b from-[#d8d2cb] to-[#beb5ab] transition-transform duration-700 ease-out group-hover:scale-105"></div>
                <div class="absolute inset-0 flex flex-col items-center justify-start text-center pt-10 px-6 z-10">
                    <p class="text-[10px] font-semibold tracking-[0.25em] uppercase text-[#888888] mb-2">Copenhague Desk · Danh Mục 01</p>
                    <h3 class="text-xl md:text-2xl font-medium tracking-wide text-[#23232C] mb-2">Đồ Nội Thất</h3>
                    <p class="text-xs text-[#888888] font-light mb-4">Giao hàng miễn phí & lắp đặt tận nơi</p>
                    <span class="link-underline text-[#23232C] text-[10px] tracking-[0.2em] uppercase">SEE COLLECTIONS</span>
                </div>
            </a>

            {{-- Card 2: Cement Wood Lamp / Trang Trí Nhà --}}
            <a href="{{ route('products.index') }}"
               class="group relative overflow-hidden block h-[380px] md:h-[440px] bg-[#23232C]"
               aria-label="Bộ sưu tập Cement Wood Lamp — Trang Trí Nhà">
                <div class="absolute inset-0 bg-gradient-to-b from-[#35353d] to-[#1a1a20] transition-transform duration-700 ease-out group-hover:scale-105"></div>
                <div class="absolute inset-0 flex flex-col items-center justify-end text-center pb-10 px-6 z-10">
                    <p class="text-[10px] font-semibold tracking-[0.25em] uppercase text-white/60 mb-2">Cement Wood Lamp · Danh Mục 02</p>
                    <h3 class="text-xl md:text-2xl font-medium tracking-wide text-white mb-2">Trang Trí Nhà</h3>
                    <p class="text-xs text-white/70 font-light mb-4">Ánh sáng ấm cúng cho không gian sống</p>
                    <span class="link-underline text-white border-white text-[10px] tracking-[0.2em] uppercase">SEE COLLECTIONS</span>
                </div>
            </a>

            {{-- Card 3: Arte 60 Stool / Phụ Kiện --}}
            <a href="{{ route('products.index') }}"
               class="group relative overflow-hidden block h-[380px] md:h-[440px] bg-[#EAE7E2]"
               aria-label="Bộ sưu tập Arte 60 Stool — Phụ Kiện">
                <div class="absolute inset-0 bg-gradient-to-b from-[#eeeae5] to-[#d5cec6] transition-transform duration-700 ease-out group-hover:scale-105"></div>
                <div class="absolute inset-0 flex flex-col items-center justify-start text-center pt-10 px-6 z-10">
                    <p class="text-[10px] font-semibold tracking-[0.25em] uppercase text-[#888888] mb-2">Arte 60 Stool · Danh Mục 03</p>
                    <h3 class="text-xl md:text-2xl font-medium tracking-wide text-[#23232C] mb-2">Phụ Kiện</h3>
                    <p class="text-xs text-[#888888] font-light mb-4">Điểm nhấn nghệ thuật tối giản Bắc Âu</p>
                    <span class="link-underline text-[#23232C] text-[10px] tracking-[0.2em] uppercase">SEE COLLECTIONS</span>
                </div>
            </a>

        </div>

    </div>
</section>
