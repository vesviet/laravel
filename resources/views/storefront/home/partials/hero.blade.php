{{-- ════════════════════════════════════════════════════════
     SECTION 1: HERO SLIDER — Dynamic Multi-Position Banner & Alpine Auto-play
     Sober Home: Scandinavian Minimalism, WCAG 2.2 AA Accessible Carousel
     ════════════════════════════════════════════════════════ --}}
@php
    $slidesData = [];
    if (isset($heroSlides) && count($heroSlides) > 0) {
        foreach ($heroSlides as $index => $slide) {
            $isEven = ($index % 2 === 0);
            $bg = $slide->image_url
                ? 'url(\'' . addcslashes($slide->image_url, "'\\") . '\') center/cover no-repeat'
                : ($isEven ? 'linear-gradient(135deg, #e8e4df 0%, #d6cfc8 100%)' : 'linear-gradient(135deg, #2c2c2c 0%, #1a1a1a 100%)');

            $slidesData[] = [
                'id'              => $slide->id,
                'eyebrow'         => $slide->eyebrow ?: ($isEven ? 'Ends Today' : 'New Arrivals'),
                'heading'         => $slide->title,
                'sub'             => $slide->subtitle ?: '',
                'cta'             => $slide->cta_text ?: 'Khám Phá Ngay',
                'link'            => route('banner.click', $slide->id),
                'open_in_new_tab' => (bool) $slide->open_in_new_tab,
                'has_image'       => !empty($slide->image_url),
                'bg'              => $bg,
                'textDark'        => empty($slide->image_url) ? $isEven : false,
            ];
        }
    } else {
        $slidesData = [
            [
                'id'              => null,
                'eyebrow'         => 'Ends Today',
                'heading'         => 'Bộ Sưu Tập Mới',
                'sub'             => 'Phong cách tối giản, tinh tế — Giao hàng miễn phí toàn quốc cho đơn từ 500.000₫',
                'cta'             => 'Khám Phá Ngay · SHOP NOW',
                'link'            => route('products.index'),
                'open_in_new_tab' => false,
                'has_image'       => false,
                'bg'              => 'linear-gradient(135deg, #e8e4df 0%, #d6cfc8 100%)',
                'textDark'        => true,
            ],
            [
                'id'              => null,
                'eyebrow'         => 'New Arrivals',
                'heading'         => 'Thiết Kế Hiện Đại',
                'sub'             => 'Chất liệu cao cấp — Đổi trả trong 30 ngày, bảo hành chính hãng',
                'cta'             => 'Mua Ngay',
                'link'            => route('products.index'),
                'open_in_new_tab' => false,
                'has_image'       => false,
                'bg'              => 'linear-gradient(135deg, #2c2c2c 0%, #1a1a1a 100%)',
                'textDark'        => false,
            ],
        ];
    }
@endphp

<script>
    window.__HERO_SLIDES__ = {!! json_encode($slidesData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!};
</script>

<section
    x-data="{
        current: 0,
        paused: false,
        timer: null,
        slides: window.__HERO_SLIDES__ || [],
        init() {
            this.startAutoplay();
        },
        startAutoplay() {
            this.stopAutoplay();
            this.timer = setInterval(() => {
                if (!this.paused && this.slides.length > 1) {
                    this.next();
                }
            }, 6000);
        },
        stopAutoplay() {
            if (this.timer) {
                clearInterval(this.timer);
                this.timer = null;
            }
        },
        pause() {
            this.paused = true;
        },
        resume() {
            this.paused = false;
        },
        next() {
            if (this.slides.length > 0) {
                this.current = (this.current + 1) % this.slides.length;
            }
        },
        prev() {
            if (this.slides.length > 0) {
                this.current = (this.current - 1 + this.slides.length) % this.slides.length;
            }
        }
    }"
    @mouseenter="pause()"
    @mouseleave="resume()"
    @focusin="pause()"
    @focusout="resume()"
    class="relative overflow-hidden w-full bg-[#F0F0F0]"
    style="height: clamp(450px, 55vw, 650px);"
    wire:ignore
    role="region"
    aria-roledescription="carousel"
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
            role="group"
            aria-roledescription="slide"
            :aria-label="`Slide ${i + 1} trên ${slides.length}`"
        >
            {{-- Dark Overlay for Image Backgrounds --}}
            <div x-show="slide.has_image" class="absolute inset-0 bg-black/40"></div>

            {{-- Content Container --}}
            <div class="section-wrapper w-full relative z-10">
                <div class="max-w-xl">
                    {{-- Eyebrow --}}
                    <p
                        x-text="slide.eyebrow"
                        class="text-xs font-semibold tracking-[0.2em] uppercase mb-2"
                        :class="slide.textDark ? 'text-[#23232C]' : 'text-white/80'"
                    ></p>

                    {{-- Main Headline --}}
                    <h1
                        x-text="slide.heading"
                        class="text-4xl md:text-6xl font-light tracking-wide mb-3 leading-[1.1]"
                        :class="slide.textDark ? 'text-[#23232C]' : 'text-white'"
                    ></h1>

                    {{-- Decorative horizontal divider --}}
                    <div
                        class="w-8 h-[2px] mb-4 transition-colors"
                        :class="slide.textDark ? 'bg-[#23232C]' : 'bg-white/80'"
                    ></div>

                    {{-- Subtitle --}}
                    <p
                        x-text="slide.sub"
                        class="text-xs md:text-sm mb-6 leading-relaxed font-light max-w-md"
                        :class="slide.textDark ? 'text-[#888888]' : 'text-white/70'"
                    ></p>

                    {{-- Underline CTA link --}}
                    <div class="flex items-center gap-4">
                        <a
                            :href="slide.link"
                            :target="slide.open_in_new_tab ? '_blank' : '_self'"
                            :rel="slide.open_in_new_tab ? 'noopener noreferrer' : ''"
                            class="link-underline text-xs tracking-[0.2em] uppercase font-medium focus-visible:ring-2 focus-visible:ring-[#23232C] focus-visible:outline-none"
                            :class="slide.textDark ? 'text-[#23232C]' : 'text-white border-white'"
                            x-text="slide.cta"
                        ></a>
                        <span x-show="slide.open_in_new_tab" class="sr-only">(mở trong tab mới)</span>
                    </div>
                </div>
            </div>
        </div>
    </template>

    {{-- Dot Indicators --}}
    <div class="absolute bottom-6 left-1/2 -translate-x-1/2 flex items-center gap-2.5 z-20" role="tablist" aria-label="Slide navigation">
        <template x-for="(slide, i) in slides" :key="i">
            <button
                @click="current = i"
                class="min-w-[24px] min-h-[24px] p-1 flex items-center justify-center focus-visible:ring-2 focus-visible:ring-[#23232C] focus-visible:outline-none rounded"
                :aria-label="`Slide ${i + 1}`"
                :aria-selected="current === i"
                :tabindex="current === i ? 0 : -1"
                role="tab"
            >
                <span
                    class="h-1.5 rounded-full transition-all duration-300 block"
                    :class="current === i ? 'bg-[#23232C] w-6' : 'bg-[#23232C]/30 w-1.5 hover:bg-[#23232C]/60'"
                ></span>
            </button>
        </template>
    </div>

    {{-- Prev Arrow Button --}}
    <button
        @click="prev()"
        class="absolute left-4 md:left-8 top-1/2 -translate-y-1/2 w-10 h-10 flex items-center justify-center bg-white/80 hover:bg-white text-[#23232C] shadow-sm transition-all z-20 hover:scale-105 focus-visible:ring-2 focus-visible:ring-[#23232C] focus-visible:outline-none"
        aria-label="Slide trước"
    >
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
        </svg>
    </button>

    {{-- Next Arrow Button --}}
    <button
        @click="next()"
        class="absolute right-4 md:right-8 top-1/2 -translate-y-1/2 w-10 h-10 flex items-center justify-center bg-white/80 hover:bg-white text-[#23232C] shadow-sm transition-all z-20 hover:scale-105 focus-visible:ring-2 focus-visible:ring-[#23232C] focus-visible:outline-none"
        aria-label="Slide tiếp theo"
    >
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
        </svg>
    </button>
</section>

