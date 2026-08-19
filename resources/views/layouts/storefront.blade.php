<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- SEO: each page pushes its own title/description/OG via @pushonce --}}
    <title>@stack('page_title')MYSHOP</title>
    <meta name="description" content="@stack('meta_description')@unless($__env->hasSection('meta_description_set'))Cửa hàng thời trang hiện đại — chất lượng cao, thiết kế tinh tế.@endunless">
    @stack('og_tags')

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="font-sans antialiased bg-[#F0F0F0] text-[#23232C]" x-data="{ mobileMenuOpen: false, searchOpen: false }">

    {{-- Skip to content (a11y) --}}
    <a href="#main-content"
       class="sr-only focus:not-sr-only focus:absolute focus:top-2 focus:left-2 focus:z-[200] focus:bg-white focus:text-[#1a1a1a] focus:px-4 focus:py-2 focus:text-xs focus:tracking-widest focus:uppercase focus:border focus:border-[#1a1a1a]">
        Bỏ qua điều hướng
    </a>

    {{-- Flash sale banner (Livewire component) --}}
    <livewire:flash-sale-banner />

    {{-- ════════════════════════════════════════
         HEADER — Sober v2 (Wrapped): Logo · Nav · Icons
         ════════════════════════════════════════ --}}
    <header class="bg-white border-b border-[#E5E5E5] sticky top-0 z-50 h-[72px]">
        <div class="section-wrapper h-full flex items-center justify-between">

            {{-- ── Logo (LEFT) ── --}}
            <a href="{{ route('home') }}"
               class="text-lg font-semibold tracking-[0.25em] uppercase shrink-0 hover:opacity-70 transition-opacity text-[#23232C]"
               aria-label="MYSHOP — Về trang chủ">
                MYSHOP
            </a>

            {{-- ── Nav (CENTER) — hidden on mobile ── --}}
            <nav role="navigation" aria-label="Điều hướng chính"
                 class="hidden md:flex items-center gap-10">
                <a href="{{ route('home') }}"
                   class="nav-link {{ request()->routeIs('home') ? 'border-b border-[#1a1a1a]' : '' }}">
                    Trang Chủ
                </a>
                <a href="{{ route('products.index') }}"
                   class="nav-link {{ request()->routeIs('products.*') ? 'border-b border-[#1a1a1a]' : '' }}">
                    Sản Phẩm
                </a>
                <a href="{{ route('blog.index') }}"
                   class="nav-link {{ request()->routeIs('blog.*') ? 'border-b border-[#1a1a1a]' : '' }}">
                    Blog
                </a>
                <a href="/about"
                   class="nav-link {{ request()->is('about') ? 'border-b border-[#1a1a1a]' : '' }}">
                    Giới Thiệu
                </a>
                <a href="/contact"
                   class="nav-link {{ request()->is('contact') ? 'border-b border-[#1a1a1a]' : '' }}">
                    Liên Hệ
                </a>
                <a href="{{ route('track-order.index') }}"
                   class="nav-link {{ request()->routeIs('track-order.*') ? 'border-b border-[#1a1a1a]' : '' }}">
                    Tra Cứu
                </a>
            </nav>

            {{-- ── Icons (RIGHT) ── --}}
            <div class="flex items-center gap-5">

                {{-- Search Modal Trigger --}}
                <button type="button"
                        @click="searchOpen = true"
                        class="flex items-center justify-center w-10 h-10 hover:opacity-60 transition-opacity text-[#23232C]"
                        aria-label="Tìm kiếm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                    </svg>
                </button>

                {{-- Account / Auth --}}
                @auth('customer')
                    <a href="{{ route('account.orders') }}"
                       class="hidden md:flex items-center gap-1 hover:opacity-60 transition-opacity text-[#23232C]"
                       aria-label="Tài khoản của bạn">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                        </svg>
                    </a>
                @else
                    <a href="{{ route('account.login') }}"
                       class="hidden md:flex items-center hover:opacity-60 transition-opacity text-[#23232C]"
                       aria-label="Đăng nhập">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                        </svg>
                    </a>
                @endauth

                {{-- Wishlist --}}
                @auth('customer')
                    <a href="{{ route('account.wishlist') }}"
                       class="hidden md:flex items-center hover:opacity-60 transition-opacity text-[#23232C]"
                       aria-label="Danh sách yêu thích">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
                        </svg>
                    </a>
                @endauth

                {{-- Cart --}}
                <button type="button"
                        @click="$dispatch('open-cart')"
                        class="relative flex items-center justify-center w-10 h-10 hover:opacity-60 transition-opacity text-[#23232C]"
                        aria-label="Giỏ hàng">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007z" />
                    </svg>
                    <livewire:cart-count />
                </button>

                {{-- Hamburger (mobile only) --}}
                <button type="button"
                        class="md:hidden flex items-center justify-center w-10 h-10 hover:opacity-60 transition-opacity text-[#23232C]"
                        @click="mobileMenuOpen = true"
                        aria-label="Mở menu"
                        :aria-expanded="mobileMenuOpen">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                </button>
            </div>
        </div>
    </header>

    {{-- ════════════════════════════════════════
         SEARCH MODAL — Alpine.js dialog
         ════════════════════════════════════════ --}}
    <div x-show="searchOpen"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @keydown.window.escape="searchOpen = false"
         class="fixed inset-0 z-[110] overflow-y-auto"
         role="dialog"
         aria-modal="true"
         aria-label="Tìm kiếm sản phẩm"
         style="display: none;">

        {{-- Backdrop --}}
        <div class="fixed inset-0 bg-black/50 transition-opacity" @click="searchOpen = false"></div>

        {{-- Modal Content Box --}}
        <div class="relative min-h-screen flex items-start justify-center pt-16 md:pt-24 px-4 pb-6">
            <div class="relative w-full max-w-3xl bg-white shadow-2xl p-8 md:p-12 z-10"
                 @click.away="searchOpen = false">

                {{-- Header / Close Button --}}
                <div class="flex justify-between items-center pb-5 border-b border-[#E5E5E5] mb-8">
                    <h3 class="text-xs font-semibold tracking-[0.2em] uppercase text-[#23232C]">Tìm Kiếm Sản Phẩm</h3>
                    <button type="button"
                            @click="searchOpen = false"
                            class="w-8 h-8 flex items-center justify-center text-[#888888] hover:text-[#23232C] transition-colors"
                            aria-label="Đóng tìm kiếm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                {{-- Search Form --}}
                <form action="{{ route('products.index') }}" method="GET" class="relative">
                    <div class="flex items-center border-b-2 border-[#23232C] pb-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-[#23232C] mr-3 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                        </svg>
                        <input type="search"
                               name="search"
                               placeholder="Nhập tên sản phẩm, bộ sưu tập, chất liệu..."
                               autocomplete="off"
                               class="w-full bg-transparent text-base md:text-lg font-light text-[#23232C] placeholder-[#888888] outline-none">
                        <button type="submit"
                                class="ml-3 shrink-0 btn-dark !py-2.5 !px-5 text-[10px]">
                            Tìm Kiếm
                        </button>
                    </div>

                    {{-- Quick suggestions --}}
                    <div class="mt-6">
                        <p class="text-[10px] font-medium tracking-[0.2em] uppercase text-[#888888] mb-3">Từ Khoá Phổ Biến:</p>
                        <div class="flex flex-wrap gap-2">
                            <a href="{{ route('products.index', ['search' => 'Bàn']) }}" class="filter-pill">Bàn Trà & Bàn Ăn</a>
                            <a href="{{ route('products.index', ['search' => 'Ghế']) }}" class="filter-pill">Ghế Armchair</a>
                            <a href="{{ route('products.index', ['search' => 'Đèn']) }}" class="filter-pill">Đèn Chiếu Sáng</a>
                            <a href="{{ route('products.index', ['search' => 'Sofa']) }}" class="filter-pill">Sofa Phòng Khách</a>
                            <a href="{{ route('products.index', ['search' => 'Tủ']) }}" class="filter-pill">Tủ Kệ Gỗ</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ════════════════════════════════════════
         MOBILE MENU — Full-screen overlay drawer
         ════════════════════════════════════════ --}}
    <div x-show="mobileMenuOpen"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-[100] md:hidden"
         role="dialog"
         aria-modal="true"
         aria-label="Menu điều hướng"
         style="display: none;">

        {{-- Backdrop --}}
        <div class="absolute inset-0 bg-black/40" @click="mobileMenuOpen = false"></div>

        {{-- Panel --}}
        <div x-show="mobileMenuOpen"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="-translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="-translate-x-full"
             class="absolute left-0 top-0 bottom-0 w-72 bg-white flex flex-col"
             style="display: none;">

            {{-- Panel header --}}
            <div class="flex items-center justify-between px-6 py-5 border-b border-[#E5E5E5]">
                <a href="{{ route('home') }}"
                   class="text-base font-semibold tracking-[0.25em] uppercase text-[#23232C]"
                   @click="mobileMenuOpen = false">
                    MYSHOP
                </a>
                <button @click="mobileMenuOpen = false"
                        class="w-8 h-8 flex items-center justify-center text-[#888888] hover:text-[#23232C] transition-colors"
                        aria-label="Đóng menu">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- Nav links --}}
            <nav class="flex flex-col px-6 py-8 gap-6" role="navigation" aria-label="Menu mobile">
                <a href="{{ route('home') }}" class="nav-link text-base" @click="mobileMenuOpen = false">Trang Chủ</a>
                <a href="{{ route('products.index') }}" class="nav-link text-base" @click="mobileMenuOpen = false">Sản Phẩm</a>
                <a href="{{ route('blog.index') }}" class="nav-link text-base" @click="mobileMenuOpen = false">Blog</a>
                <a href="/about" class="nav-link text-base" @click="mobileMenuOpen = false">Giới Thiệu</a>
                <a href="/contact" class="nav-link text-base" @click="mobileMenuOpen = false">Liên Hệ</a>
                <a href="{{ route('track-order.index') }}" class="nav-link text-base" @click="mobileMenuOpen = false">Tra Cứu</a>
            </nav>

            {{-- Auth links at bottom --}}
            <div class="mt-auto px-6 py-8 border-t border-[#E5E5E5] flex flex-col gap-4">
                @auth('customer')
                    <a href="{{ route('account.orders') }}" class="nav-link" @click="mobileMenuOpen = false">Đơn hàng của tôi</a>
                    <a href="{{ route('account.wishlist') }}" class="nav-link" @click="mobileMenuOpen = false">Danh sách yêu thích</a>
                    <form method="POST" action="{{ route('account.logout') }}">
                        @csrf
                        <button type="submit" class="nav-link text-left w-full">Đăng xuất</button>
                    </form>
                @else
                    <a href="{{ route('account.login') }}" class="nav-link" @click="mobileMenuOpen = false">Đăng nhập</a>
                    <a href="{{ route('account.register') }}" class="nav-link" @click="mobileMenuOpen = false">Đăng ký</a>
                @endauth
            </div>
        </div>
    </div>

    {{-- ════════════════════════════════════════
         MAIN CONTENT
         ════════════════════════════════════════ --}}
    <main id="main-content" tabindex="-1" class="focus:outline-none min-h-[60vh]">
        @yield('content')
    </main>

    {{-- ════════════════════════════════════════
         FOOTER — 4-Layer Stacked Architecture
         ════════════════════════════════════════ --}}
    <footer>

        {{-- Layer 1: Newsletter subscription bar --}}
        <section class="bg-[#F0F0F0] border-t border-[#E5E5E5] py-16 text-center">
            <div class="section-wrapper">
                <h2 class="text-2xl font-medium tracking-wide mb-2 text-[#23232C]">Newsletter</h2>
                <p class="text-sm text-[#888888] mb-8 font-light">Nhận thông tin sản phẩm mới và ưu đãi đặc biệt</p>
                <form action="{{ route('newsletter.subscribe') }}" method="POST"
                      class="flex items-center border-b border-[#1a1a1a] max-w-md mx-auto">
                    @csrf
                    <input type="email"
                           name="email"
                           placeholder="Email của bạn"
                           required
                           class="input-underline flex-1 border-none"
                           aria-label="Địa chỉ email đăng ký newsletter"
                           value="{{ old('email') }}">
                    <button type="submit"
                            class="shrink-0 text-[10px] font-medium tracking-[0.2em] uppercase py-2 pl-4 text-[#23232C] hover:opacity-60 transition-opacity">
                        Đăng Ký
                    </button>
                </form>
                @if(session('newsletter_success'))
                    <p class="mt-4 text-sm text-green-700">{{ session('newsletter_success') }}</p>
                @endif
                @error('email')
                    <p class="mt-4 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </section>

        {{-- Layer 2: 2-Column widgets --}}
        <section class="bg-[#F0F0F0] border-t border-[#E5E5E5] py-16">
            <div class="section-wrapper grid grid-cols-1 md:grid-cols-2 gap-12 md:gap-16">
                {{-- Column 1: Company Info & Contact Details --}}
                <div>
                    <p class="text-[10px] font-semibold tracking-[0.2em] uppercase mb-4 text-[#23232C]">VỀ MYSHOP</p>
                    <p class="text-sm text-[#888888] leading-relaxed font-light mb-6">
                        Cửa hàng nội thất và phong cách sống hiện đại. Sản phẩm chất lượng cao, thiết kế tối giản Bắc Âu, giao hàng toàn quốc.
                    </p>
                    <ul class="flex flex-col gap-3 text-sm text-[#888888] font-light">
                        <li class="flex items-start gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-[#23232C] shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                            </svg>
                            <span>9606 North MoPac Expressway Suite 700 Austin, TX 78759</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-[#23232C] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" />
                            </svg>
                            <a href="tel:+841900123456" class="hover:text-[#23232C] transition-colors">+84 (0) 1900 123 456</a>
                        </li>
                        <li class="flex items-center gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-[#23232C] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                            </svg>
                            <a href="mailto:support@myshop.vn" class="hover:text-[#23232C] transition-colors">support@myshop.vn</a>
                        </li>
                        <li class="flex items-center gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-[#23232C] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 017.843 4.582M12 3a8.997 8.997 0 00-7.843 4.582m15.686 0A11.953 11.953 0 0112 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0121 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0112 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 013 12c0-1.605.42-3.113 1.157-4.418" />
                            </svg>
                            <a href="https://myshop.vn" target="_blank" rel="noopener" class="hover:text-[#23232C] transition-colors">https://myshop.vn</a>
                        </li>
                    </ul>
                </div>

                {{-- Column 2: Customer Services Navigation --}}
                <div>
                    <p class="text-[10px] font-semibold tracking-[0.2em] uppercase mb-4 text-[#23232C]">LIÊN KẾT NHANH</p>
                    <nav class="flex flex-col gap-3 text-sm text-[#888888] font-light" aria-label="Customer services navigation">
                        <a href="{{ route('products.index') }}" class="hover:text-[#23232C] transition-colors">Sản Phẩm</a>
                        <a href="{{ route('blog.index') }}" class="hover:text-[#23232C] transition-colors">Blog & Kiến Thức</a>
                        <a href="/about" class="hover:text-[#23232C] transition-colors">Giới Thiệu</a>
                        <a href="/contact" class="hover:text-[#23232C] transition-colors">Liên Hệ</a>
                        <a href="{{ route('track-order.index') }}" class="hover:text-[#23232C] transition-colors">Tra Cứu Đơn Hàng</a>
                        <a href="/about" class="hover:text-[#23232C] transition-colors">Chính Sách Vận Chuyển</a>
                        <a href="/contact" class="hover:text-[#23232C] transition-colors">Chính Sách Đổi Trả & Bảo Hành</a>
                    </nav>
                </div>
            </div>
        </section>

        {{-- Layer 3: 6-Photo Instagram Grid --}}
        <section class="w-full bg-[#F0F0F0] border-t border-[#E5E5E5] overflow-hidden" aria-label="Instagram Feed">
            <div class="grid grid-cols-3 md:grid-cols-6 gap-0 w-full">
                @php
                    $instaPhotos = [
                        ['url' => 'https://images.unsplash.com/photo-1586023492125-27b2c045efd7?w=500&auto=format&fit=crop&q=80', 'alt' => 'Scandinavian Armchair Decor'],
                        ['url' => 'https://images.unsplash.com/photo-1555041469-a586c61ea9bc?w=500&auto=format&fit=crop&q=80', 'alt' => 'Modern Minimalist Living Room Sofa'],
                        ['url' => 'https://images.unsplash.com/photo-1507473885765-e6ed057f782c?w=500&auto=format&fit=crop&q=80', 'alt' => 'Minimalist Ambient Pendant Lamp'],
                        ['url' => 'https://images.unsplash.com/photo-1538688525198-9b88f6f53126?w=500&auto=format&fit=crop&q=80', 'alt' => 'Nordic Wooden Dining Table'],
                        ['url' => 'https://images.unsplash.com/photo-1513694203232-719a280e022f?w=500&auto=format&fit=crop&q=80', 'alt' => 'Contemporary Home Decor Accessories'],
                        ['url' => 'https://images.unsplash.com/photo-1519710164239-da123dc03ef4?w=500&auto=format&fit=crop&q=80', 'alt' => 'Artistic Wooden Geometric Stool'],
                    ];
                @endphp
                @foreach($instaPhotos as $photo)
                    <a href="https://instagram.com" target="_blank" rel="noopener noreferrer"
                       class="group relative aspect-square overflow-hidden bg-[#E8E4DF] block"
                       aria-label="{{ $photo['alt'] }}">
                        <img src="{{ $photo['url'] }}"
                             alt="{{ $photo['alt'] }}"
                             loading="lazy"
                             class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500 ease-out">
                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                            </svg>
                        </div>
                    </a>
                @endforeach
            </div>
        </section>

        {{-- Layer 4: Bottom Bar (Copyright & Socials) --}}
        <div class="bg-[#F0F0F0] border-t border-[#E5E5E5] py-6">
            <div class="section-wrapper flex flex-col sm:flex-row items-center justify-between gap-4">
                <p class="text-xs text-[#888888] font-light order-2 sm:order-1">
                    Copyright &copy; {{ date('Y') }} MYSHOP. Tất cả quyền được bảo lưu.
                </p>
                <div class="flex items-center gap-5 order-1 sm:order-2">
                    {{-- Facebook --}}
                    <a href="#" class="text-[#888888] hover:text-[#23232C] transition-colors" aria-label="Facebook">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                        </svg>
                    </a>
                    {{-- Instagram --}}
                    <a href="#" class="text-[#888888] hover:text-[#23232C] transition-colors" aria-label="Instagram">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                        </svg>
                    </a>
                    {{-- Pinterest --}}
                    <a href="#" class="text-[#888888] hover:text-[#23232C] transition-colors" aria-label="Pinterest">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M12 0C5.373 0 0 5.373 0 12c0 5.084 3.163 9.426 7.627 11.174-.105-.949-.2-2.405.042-3.441.218-.937 1.407-5.965 1.407-5.965s-.359-.719-.359-1.782c0-1.668.967-2.914 2.171-2.914 1.023 0 1.518.769 1.518 1.69 0 1.029-.655 2.568-.994 3.995-.283 1.194.599 2.169 1.777 2.169 2.133 0 3.772-2.249 3.772-5.495 0-2.873-2.064-4.882-5.012-4.882-3.414 0-5.418 2.561-5.418 5.207 0 1.031.397 2.138.893 2.738a.36.36 0 0 1 .083.345l-.333 1.36c-.053.22-.174.267-.402.161-1.499-.698-2.436-2.889-2.436-4.649 0-3.785 2.75-7.262 7.929-7.262 4.163 0 7.398 2.967 7.398 6.931 0 4.136-2.607 7.464-6.227 7.464-1.216 0-2.359-.632-2.75-1.378l-.748 2.853c-.271 1.043-1.002 2.35-1.492 3.146C9.57 23.812 10.763 24 12 24c6.627 0 12-5.373 12-12S18.627 0 12 0z"/>
                        </svg>
                    </a>
                    {{-- Twitter / X --}}
                    <a href="#" class="text-[#888888] hover:text-[#23232C] transition-colors" aria-label="Twitter">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>

    </footer>

    {{-- ════════════════════════════════════════
         LIVEWIRE COMPONENTS
         ════════════════════════════════════════ --}}
    <livewire:cart-drawer />

    @stack('scripts')

    {{-- ════════════════════════════════════════
         TOAST NOTIFICATION SYSTEM (U1)
         Listens for 'toast' browser events.
         Usage: $this->dispatch('toast', message: '...', type: 'success')
         ════════════════════════════════════════ --}}
    <div
        x-data="{
            notifications: [],
            show(event) {
                const n = {
                    id: Date.now(),
                    message: event.detail.message ?? event.detail[0]?.message ?? '',
                    type: event.detail.type ?? event.detail[0]?.type ?? 'success'
                };
                this.notifications.push(n);
                setTimeout(() => this.remove(n.id), 4000);
            },
            remove(id) {
                this.notifications = this.notifications.filter(n => n.id !== id);
            }
        }"
        @toast.window="show($event)"
        class="fixed bottom-6 right-6 z-[200] flex flex-col gap-3 pointer-events-none"
        role="status"
        aria-live="polite"
        aria-label="Thông báo"
    >
        <template x-for="n in notifications" :key="n.id">
            <div
                x-show="true"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-2"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="pointer-events-auto flex items-center gap-3 shadow-lg px-5 py-3 text-sm font-medium min-w-[220px] max-w-xs"
                :class="{
                    'bg-[#23232C] text-white': n.type === 'success',
                    'bg-[#E84444] text-white': n.type === 'error',
                    'bg-[#1a1a1a] text-white': n.type === 'info',
                    'bg-[#23232C] text-white': n.type !== 'success' && n.type !== 'error' && n.type !== 'info'
                }"
                role="alert"
            >
                <span x-text="n.message" class="flex-1"></span>
                <button @click="remove(n.id)"
                        class="ml-2 opacity-70 hover:opacity-100 focus:outline-none"
                        aria-label="Đóng thông báo">×</button>
            </div>
        </template>
    </div>

    @livewireScripts
</body>
</html>
