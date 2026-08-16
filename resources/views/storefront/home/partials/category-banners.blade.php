{{-- ════════════════════════════════
     CATEGORY BANNERS — 2-column 50/50
     ════════════════════════════════ --}}
<section class="py-16">
    <div class="section-wrapper">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

            {{-- Column 1 --}}
            <a href="{{ route('products.index') }}"
               class="group relative overflow-hidden block"
               style="height: 420px;">
                <div class="absolute inset-0 bg-gradient-to-br from-[#d6cfc8] to-[#b8b0a8] transition-transform duration-700 group-hover:scale-105"></div>
                <div class="absolute inset-0 flex flex-col justify-end p-10">
                    <p class="text-[10px] font-medium tracking-[0.2em] uppercase text-[#888888] mb-2">Bộ Sưu Tập</p>
                    <h2 class="text-2xl font-medium tracking-wide text-[#1a1a1a] mb-4">Phong Cách Mùa Này</h2>
                    <span class="link-underline text-[#1a1a1a]">Khám Phá</span>
                </div>
            </a>

            {{-- Column 2 --}}
            <a href="{{ route('products.index') }}"
               class="group relative overflow-hidden block"
               style="height: 420px;">
                <div class="absolute inset-0 bg-gradient-to-br from-[#2c2c2c] to-[#1a1a1a] transition-transform duration-700 group-hover:scale-105"></div>
                <div class="absolute inset-0 flex flex-col justify-end p-10">
                    <p class="text-[10px] font-medium tracking-[0.2em] uppercase text-white/50 mb-2">Chất Liệu Cao Cấp</p>
                    <h2 class="text-2xl font-medium tracking-wide text-white mb-4">Thiết Kế Tối Giản</h2>
                    <span class="link-underline text-white border-white">Xem Ngay</span>
                </div>
            </a>

        </div>
    </div>
</section>
