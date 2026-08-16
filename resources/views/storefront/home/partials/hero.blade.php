{{-- ════════════════════════════════
     HERO SLIDER — Alpine pure auto-play
     Gradient placeholders until real images added
     ════════════════════════════════ --}}
<section
    x-data="{
        current: 0,
        slides: [
            {
                heading: 'Bộ Sưu Tập Mới',
                sub: 'Phong cách tối giản, tinh tế — Giao hàng miễn phí toàn quốc',
                cta: 'Khám Phá Ngay',
                bg: 'linear-gradient(135deg, #e8e4df 0%, #d6cfc8 100%)',
                textDark: true
            },
            {
                heading: 'Thiết Kế Hiện Đại',
                sub: 'Chất liệu cao cấp — Đổi trả trong 30 ngày',
                cta: 'Mua Ngay',
                bg: 'linear-gradient(135deg, #2c2c2c 0%, #1a1a1a 100%)',
                textDark: false
            }
        ],
        next() { this.current = (this.current + 1) % this.slides.length; },
        prev() { this.current = (this.current - 1 + this.slides.length) % this.slides.length; }
    }"
    x-init="setInterval(() => next(), 5500)"
    class="relative overflow-hidden"
    style="height: clamp(420px, 55vw, 620px);"
    wire:ignore
    aria-label="Hero banner"
>
    <template x-for="(slide, i) in slides" :key="i">
        <div
            x-show="current === i"
            x-transition:enter="transition-opacity duration-700 ease-in-out"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity duration-500 ease-in-out"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="absolute inset-0 flex items-center"
            :style="`background: ${slide.bg};`"
            style="display: none;"
        >
            {{-- Content overlay --}}
            <div class="section-wrapper w-full">
                <div class="max-w-lg">
                    <h1
                        x-text="slide.heading"
                        class="text-4xl md:text-5xl font-light tracking-wide mb-4 leading-tight"
                        :class="slide.textDark ? 'text-[#1a1a1a]' : 'text-white'"
                    ></h1>
                    <p
                        x-text="slide.sub"
                        class="text-sm mb-8 leading-relaxed"
                        :class="slide.textDark ? 'text-[#888888]' : 'text-white/70'"
                    ></p>
                    <a
                        href="{{ route('products.index') }}"
                        class="link-underline"
                        :class="slide.textDark ? 'text-[#1a1a1a]' : 'text-white border-white'"
                        x-text="slide.cta"
                    ></a>
                </div>
            </div>
        </div>
    </template>

    {{-- Dot indicators --}}
    <div class="absolute bottom-6 left-1/2 -translate-x-1/2 flex gap-2" role="tablist" aria-label="Slide navigation">
        <template x-for="(slide, i) in slides" :key="i">
            <button
                @click="current = i"
                class="w-1.5 h-1.5 rounded-full transition-all duration-300"
                :class="current === i ? 'bg-[#1a1a1a] w-5' : 'bg-[#1a1a1a]/30'"
                :aria-label="`Slide ${i + 1}`"
                :aria-selected="current === i"
                role="tab"
            ></button>
        </template>
    </div>

    {{-- Arrow prev --}}
    <button @click="prev()"
            class="absolute left-4 top-1/2 -translate-y-1/2 w-10 h-10 flex items-center justify-center bg-white/80 hover:bg-white transition-colors"
            aria-label="Slide trước">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
        </svg>
    </button>

    {{-- Arrow next --}}
    <button @click="next()"
            class="absolute right-4 top-1/2 -translate-y-1/2 w-10 h-10 flex items-center justify-center bg-white/80 hover:bg-white transition-colors"
            aria-label="Slide tiếp theo">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
        </svg>
    </button>
</section>
