{{-- ════════════════════════════════════════════════════════
     SECTION 2: 2-COLUMN PROMO BANNERS (50/50 Split)
     Dynamic Multi-Position Banner & Scandinavian Fallback
     ════════════════════════════════════════════════════════ --}}
<section class="py-12 md:py-16">
    <div class="section-wrapper">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 md:gap-8">
            @if(isset($promoBanners) && count($promoBanners) > 0)
                @foreach($promoBanners->take(2) as $index => $banner)
                    @php
                        $isDark = ($index % 2 === 1);
                        $hasImage = !empty($banner->image_url);
                    @endphp
                    <a href="{{ route('banner.click', $banner->id) }}"
                       target="{{ $banner->open_in_new_tab ? '_blank' : '_self' }}"
                       @if($banner->open_in_new_tab) rel="noopener noreferrer" @endif
                       class="group relative overflow-hidden block h-[320px] md:h-[400px] {{ $isDark ? 'bg-primary-dark' : 'bg-[#EAE7E2]' }} focus-visible:ring-2 focus-visible:ring-primary-dark focus-visible:outline-none"
                       aria-label="Khám phá {{ $banner->title }}">
                        @if($hasImage)
                            <div class="absolute inset-0 bg-cover bg-center transition-transform duration-700 ease-out group-hover:scale-105"
                                 style="background-image: url('{{ $banner->image_url }}');"></div>
                            <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/30 to-transparent"></div>
                        @else
                            <div class="absolute inset-0 bg-gradient-to-br {{ $isDark ? 'from-[#2c2c34] to-[#1a1a20]' : 'from-[#e2ded9] to-[#cbc4bc]' }} transition-transform duration-700 ease-out group-hover:scale-105"></div>
                        @endif

                        <div class="absolute inset-0 flex flex-col justify-end p-8 md:p-10 z-10">
                            @if($banner->eyebrow)
                                <p class="text-[10px] font-semibold tracking-[0.2em] uppercase {{ ($hasImage || $isDark) ? 'text-white/70' : 'text-muted-text' }} mb-2">
                                    {{ $banner->eyebrow }}
                                </p>
                            @endif
                            <h2 class="text-2xl md:text-3xl font-medium tracking-wide {{ ($hasImage || $isDark) ? 'text-white' : 'text-primary-dark' }} mb-2">
                                {{ $banner->title }}
                            </h2>
                            @if($banner->subtitle)
                                <p class="text-xs {{ ($hasImage || $isDark) ? 'text-white/80' : 'text-muted-text' }} font-light mb-4">
                                    {{ $banner->subtitle }}
                                </p>
                            @endif
                            <div>
                                <span class="link-underline {{ ($hasImage || $isDark) ? 'text-white border-white' : 'text-primary-dark' }} text-xs tracking-[0.18em] uppercase">
                                    {{ $banner->cta_text ?: 'Khám Phá · SHOP NOW' }}
                                </span>
                            </div>
                        </div>
                        @if($banner->open_in_new_tab)
                            <span class="sr-only">(mở trong tab mới)</span>
                        @endif
                    </a>
                @endforeach
            @else
                {{-- Column 1: Lighting on Express / Phong Cách Mùa Này --}}
                <a href="{{ route('products.index') }}"
                   class="group relative overflow-hidden block h-[320px] md:h-[400px] bg-[#EAE7E2] focus-visible:ring-2 focus-visible:ring-primary-dark focus-visible:outline-none"
                   aria-label="Khám phá Lighting on Express — Phong Cách Mùa Này">
                    <div class="absolute inset-0 bg-gradient-to-br from-[#e2ded9] to-[#cbc4bc] transition-transform duration-700 ease-out group-hover:scale-105"></div>
                    <div class="absolute inset-0 flex flex-col justify-end p-8 md:p-10 z-10">
                        <p class="text-[10px] font-semibold tracking-[0.2em] uppercase text-muted-text mb-2">Lighting on Express · Bộ Sưu Tập</p>
                        <h2 class="text-2xl md:text-3xl font-medium tracking-wide text-primary-dark mb-2">Phong Cách Mùa Này</h2>
                        <p class="text-xs text-muted-text font-light mb-4">Giao hàng nhanh trong tuần, thiết kế tinh xảo</p>
                        <div>
                            <span class="link-underline text-primary-dark text-xs tracking-[0.18em] uppercase">Khám Phá · SHOP NOW</span>
                        </div>
                    </div>
                </a>

                {{-- Column 2: Dining Chairs / Thiết Kế Tối Giản --}}
                <a href="{{ route('products.index') }}"
                   class="group relative overflow-hidden block h-[320px] md:h-[400px] bg-primary-dark focus-visible:ring-2 focus-visible:ring-primary-dark focus-visible:outline-none"
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
            @endif
        </div>
    </div>
</section>

