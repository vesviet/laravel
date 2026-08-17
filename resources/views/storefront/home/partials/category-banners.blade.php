{{-- ════════════════════════════════════════════════════════
     SECTION 2: 2-COLUMN PROMO BANNERS (50/50 Split)
     Sober Home v12: "Lighting on Express" & "Dining Chairs"
     ════════════════════════════════════════════════════════ --}}
<section class="py-12 md:py-16">
    <div class="section-wrapper">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 md:gap-8">

            {{-- Column 1: Lighting on Express / Phong Cách Mùa Này --}}
            <a href="{{ route('products.index') }}"
               class="group relative overflow-hidden block h-[320px] md:h-[400px] bg-[#EAE7E2]"
               aria-label="Khám phá Lighting on Express — Phong Cách Mùa Này">
                <div class="absolute inset-0 bg-gradient-to-br from-[#e2ded9] to-[#cbc4bc] transition-transform duration-700 ease-out group-hover:scale-105"></div>
                <div class="absolute inset-0 flex flex-col justify-end p-8 md:p-10 z-10">
                    <p class="text-[10px] font-semibold tracking-[0.2em] uppercase text-[#888888] mb-2">Lighting on Express · Bộ Sưu Tập</p>
                    <h2 class="text-2xl md:text-3xl font-medium tracking-wide text-[#23232C] mb-2">Phong Cách Mùa Này</h2>
                    <p class="text-xs text-[#888888] font-light mb-4">Giao hàng nhanh trong tuần, thiết kế tinh xảo</p>
                    <div>
                        <span class="link-underline text-[#23232C] text-xs tracking-[0.18em] uppercase">Khám Phá · SHOP NOW</span>
                    </div>
                </div>
            </a>

            {{-- Column 2: Dining Chairs / Thiết Kế Tối Giản --}}
            <a href="{{ route('products.index') }}"
               class="group relative overflow-hidden block h-[320px] md:h-[400px] bg-[#23232C]"
               aria-label="Khám phá Dining Chairs — Thiết Kế Tối Giản">
                <div class="absolute inset-0 bg-gradient-to-br from-[#2c2c34] to-[#1a1a20] transition-transform duration-700 ease-out group-hover:scale-105"></div>
                <div class="absolute inset-0 flex flex-col justify-end p-8 md:p-10 z-10">
                    <p class="text-[10px] font-semibold tracking-[0.2em] uppercase text-white/60 mb-2">Dining Chairs · Chất Liệu Cao Cấp</p>
                    <h2 class="text-2xl md:text-3xl font-medium tracking-wide text-white mb-2">Thiết Kế Tối Giản</h2>
                    <p class="text-xs text-white/70 font-light mb-4">Nâng tầm không gian phòng ăn phong cách Bắc Âu</p>
                    <div>
                        <span class="link-underline text-white border-white text-xs tracking-[0.18em] uppercase">Xem Ngay · SEE MORE</span>
                    </div>
                </div>
            </a>

        </div>
    </div>
</section>
