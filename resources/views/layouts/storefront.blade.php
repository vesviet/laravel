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
<body class="font-sans antialiased" x-data="{ mobileMenuOpen: false }">

    {{-- Skip to content (a11y) --}}
    <a href="#main-content"
       class="sr-only focus:not-sr-only focus:absolute focus:top-2 focus:left-2 focus:z-[200] focus:bg-white focus:text-[#1a1a1a] focus:px-4 focus:py-2 focus:text-xs focus:tracking-widest focus:uppercase focus:border focus:border-[#1a1a1a]">
        Bỏ qua điều hướng
    </a>

    {{-- Flash sale banner (existing Livewire component) --}}
    <livewire:flash-sale-banner />

    {{-- ════════════════════════════════════════
         HEADER — Sober v2: Logo · Nav · Icons
         ════════════════════════════════════════ --}}
    <header class="bg-white border-b border-[#E5E5E5] sticky top-0 z-50 h-[72px]">
        <div class="section-wrapper h-full flex items-center justify-between">

            {{-- ── Logo (LEFT) ── --}}
            <a href="{{ route('home') }}"
               class="text-lg font-semibold tracking-[0.25em] uppercase shrink-0 hover:opacity-70 transition-opacity"
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

                {{-- Account / Auth --}}
                @auth('customer')
                    <a href="{{ route('account.orders') }}"
                       class="hidden md:flex items-center gap-1 hover:opacity-60 transition-opacity"
                       aria-label="Tài khoản của bạn">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                        </svg>
                    </a>
                @else
                    <a href="{{ route('account.login') }}"
                       class="hidden md:flex items-center hover:opacity-60 transition-opacity"
                       aria-label="Đăng nhập">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                        </svg>
                    </a>
                @endauth

                {{-- Wishlist --}}
                @auth('customer')
                    <a href="{{ route('account.wishlist') }}"
                       class="hidden md:flex items-center hover:opacity-60 transition-opacity"
                       aria-label="Danh sách yêu thích">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
                        </svg>
                    </a>
                @endauth

                {{-- Cart --}}
                <button type="button"
                        @click="$dispatch('open-cart')"
                        class="relative flex items-center justify-center w-10 h-10 hover:opacity-60 transition-opacity"
                        aria-label="Giỏ hàng">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007z" />
                    </svg>
                    <livewire:cart-count />
                </button>

                {{-- Hamburger (mobile only) --}}
                <button type="button"
                        class="md:hidden flex items-center justify-center w-10 h-10 hover:opacity-60 transition-opacity"
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
         MOBILE MENU — Full-screen overlay
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
                   class="text-base font-semibold tracking-[0.25em] uppercase"
                   @click="mobileMenuOpen = false">
                    MYSHOP
                </a>
                <button @click="mobileMenuOpen = false"
                        class="w-8 h-8 flex items-center justify-center hover:opacity-60"
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
                <a href="/about" class="nav-link text-base" @click="mobileMenuOpen = false">Giới Thiệu</a>
                <a href="/contact" class="nav-link text-base" @click="mobileMenuOpen = false">Liên Hệ</a>
                <a href="{{ route('track-order.index') }}" class="nav-link text-base" @click="mobileMenuOpen = false">Tra Cứu</a>
            </nav>

            {{-- Auth links at bottom --}}
            <div class="mt-auto px-6 py-8 border-t border-[#E5E5E5] flex flex-col gap-4">
                @auth('customer')
                    <a href="{{ route('account.orders') }}" class="nav-link" @click="mobileMenuOpen = false">Đơn hàng của tôi</a>
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
    <main id="main-content" tabindex="-1" class="focus:outline-none">
        @yield('content')
    </main>

    {{-- ════════════════════════════════════════
         FOOTER — 3 layers
         ════════════════════════════════════════ --}}
    <footer>

        {{-- Layer 1: Newsletter bar --}}
        <section class="bg-[#F0F0F0] border-t border-[#E5E5E5] py-16">
            <div class="section-wrapper text-center">
                <h2 class="text-2xl font-medium tracking-wide mb-2">Newsletter</h2>
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
                            class="shrink-0 text-[10px] font-medium tracking-[0.2em] uppercase py-2 pl-4 hover:opacity-60 transition-opacity">
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

        {{-- Layer 2: 2-column widget --}}
        <div class="bg-[#F0F0F0] border-t border-[#E5E5E5] py-10">
            <div class="section-wrapper grid grid-cols-1 md:grid-cols-2 gap-12">
                <div>
                    <p class="text-[10px] font-medium tracking-[0.2em] uppercase mb-3">VỀ MYSHOP</p>
                    <p class="text-sm text-[#888888] leading-relaxed font-light">
                        Cửa hàng thời trang hiện đại với sản phẩm chất lượng cao, thiết kế tinh tế. Giao hàng toàn quốc.
                    </p>
                </div>
                <div>
                    <p class="text-[10px] font-medium tracking-[0.2em] uppercase mb-3">LIÊN KẾT NHANH</p>
                    <nav class="flex flex-col gap-2" aria-label="Footer navigation">
                        <a href="{{ route('products.index') }}" class="text-sm text-[#888888] hover:text-[#1a1a1a] transition-colors font-light">Sản Phẩm</a>
                        <a href="/about" class="text-sm text-[#888888] hover:text-[#1a1a1a] transition-colors font-light">Giới Thiệu</a>
                        <a href="/contact" class="text-sm text-[#888888] hover:text-[#1a1a1a] transition-colors font-light">Liên Hệ</a>
                        <a href="{{ route('track-order.index') }}" class="text-sm text-[#888888] hover:text-[#1a1a1a] transition-colors font-light">Tra Cứu Đơn Hàng</a>
                    </nav>
                </div>
            </div>
        </div>

        {{-- Layer 3: Copyright + social --}}
        <div class="bg-[#F0F0F0] border-t border-[#E5E5E5] py-5">
            <div class="section-wrapper flex flex-col sm:flex-row items-center justify-between gap-4">
                <p class="text-xs text-[#888888] font-light order-2 sm:order-1">
                    Copyright &copy; {{ date('Y') }} MYSHOP. Tất cả quyền được bảo lưu.
                </p>
                <div class="flex items-center gap-5 order-1 sm:order-2">
                    {{-- Facebook --}}
                    <a href="#" class="text-[#888888] hover:text-[#1a1a1a] transition-colors" aria-label="Facebook">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                        </svg>
                    </a>
                    {{-- Instagram --}}
                    <a href="#" class="text-[#888888] hover:text-[#1a1a1a] transition-colors" aria-label="Instagram">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                        </svg>
                    </a>
                    {{-- Pinterest --}}
                    <a href="#" class="text-[#888888] hover:text-[#1a1a1a] transition-colors" aria-label="Pinterest">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M12 0C5.373 0 0 5.373 0 12c0 5.084 3.163 9.426 7.627 11.174-.105-.949-.2-2.405.042-3.441.218-.937 1.407-5.965 1.407-5.965s-.359-.719-.359-1.782c0-1.668.967-2.914 2.171-2.914 1.023 0 1.518.769 1.518 1.69 0 1.029-.655 2.568-.994 3.995-.283 1.194.599 2.169 1.777 2.169 2.133 0 3.772-2.249 3.772-5.495 0-2.873-2.064-4.882-5.012-4.882-3.414 0-5.418 2.561-5.418 5.207 0 1.031.397 2.138.893 2.738a.36.36 0 0 1 .083.345l-.333 1.36c-.053.22-.174.267-.402.161-1.499-.698-2.436-2.889-2.436-4.649 0-3.785 2.75-7.262 7.929-7.262 4.163 0 7.398 2.967 7.398 6.931 0 4.136-2.607 7.464-6.227 7.464-1.216 0-2.359-.632-2.75-1.378l-.748 2.853c-.271 1.043-1.002 2.35-1.492 3.146C9.57 23.812 10.763 24 12 24c6.627 0 12-5.373 12-12S18.627 0 12 0z"/>
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
                    'bg-[#2c2c2c] text-white': n.type === 'success',
                    'bg-[#E84444] text-white': n.type === 'error',
                    'bg-[#1a1a1a] text-white': n.type === 'info',
                    'bg-[#2c2c2c] text-white': n.type !== 'success' && n.type !== 'error' && n.type !== 'info'
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
