{{-- ════════════════════════════════
     COLLECTION BANNER — 3-col full-width
     Sober: "Cement / Wood / Lamp" style
     ════════════════════════════════ --}}
<section class="py-16 bg-white">
    <div class="section-wrapper">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

            {{-- Block 1 --}}
            <a href="{{ route('products.index') }}"
               class="group relative overflow-hidden block"
               style="height: 340px;">
                <div class="absolute inset-0 bg-gradient-to-b from-[#c5bdb5] to-[#a89e95] transition-transform duration-700 group-hover:scale-105"></div>
                <div class="absolute inset-0 flex flex-col items-center justify-center text-center p-8">
                    <p class="text-[10px] font-medium tracking-[0.25em] uppercase text-[#888888] mb-3">Danh Mục 01</p>
                    <h3 class="text-xl font-medium tracking-wide text-[#1a1a1a]">Đồ Nội Thất</h3>
                </div>
            </a>

            {{-- Block 2 — Featured center --}}
            <a href="{{ route('products.index') }}"
               class="group relative overflow-hidden block md:col-span-1"
               style="height: 340px;">
                <div class="absolute inset-0 bg-gradient-to-b from-[#3d3530] to-[#1a1a1a] transition-transform duration-700 group-hover:scale-105"></div>
                <div class="absolute inset-0 flex flex-col items-center justify-center text-center p-8">
                    <p class="text-[10px] font-medium tracking-[0.25em] uppercase text-white/50 mb-3">Danh Mục 02</p>
                    <h3 class="text-xl font-medium tracking-wide text-white">Trang Trí Nhà</h3>
                </div>
            </a>

            {{-- Block 3 --}}
            <a href="{{ route('products.index') }}"
               class="group relative overflow-hidden block"
               style="height: 340px;">
                <div class="absolute inset-0 bg-gradient-to-b from-[#e8e4df] to-[#d0c8c0] transition-transform duration-700 group-hover:scale-105"></div>
                <div class="absolute inset-0 flex flex-col items-center justify-center text-center p-8">
                    <p class="text-[10px] font-medium tracking-[0.25em] uppercase text-[#888888] mb-3">Danh Mục 03</p>
                    <h3 class="text-xl font-medium tracking-wide text-[#1a1a1a]">Phụ Kiện</h3>
                </div>
            </a>

        </div>
    </div>
</section>
