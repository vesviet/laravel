<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Máy Điện Giải Sài Gòn - Nước Ion Kiềm Chính Hãng')</title>
    <meta name="description" content="@yield('meta_description', 'Máy điện giải nước ion kiềm chính hãng - Bảo hành 5 năm, Miễn phí lắp đặt, Chứng nhận y tế quốc tế.')">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    @vite(['resources/css/themes/' . ($activeTheme ?? 'elomus') . '.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    @livewireStyles
    <style>
        /* UI/UX Micro-interactions for MegaMenu */
        .mega-bridge { position: absolute; height: 20px; width: 100%; top: 100%; left: 0; background: transparent; z-index: 99; }
        .mega-menu {
            display: none;
            opacity: 0;
            transform: translateY(10px);
            transition: opacity 0.2s ease-out, transform 0.2s ease-out;
            pointer-events: none;
        }
        .nav-item.has-mega:hover .mega-menu {
            display: block;
            opacity: 1;
            transform: translateY(0);
            pointer-events: auto;
        }
    </style>
</head>
<body>
    {{-- Announcement Bar --}}
    <div class="announcement-bar">
        🚚 MIỄN PHÍ VẬN CHUYỂN & LẮP ĐẶT TOÀN QUỐC
    </div>

    {{-- Sticky Header --}}
    <header class="site-header" id="site-header">
        <div class="header-inner">
            <div class="logo">
                <a href="{{ route('home') }}" style="font-size: 1.75rem; font-weight: 900; letter-spacing: -0.05em; color: inherit;">
                    MDG<span style="color: var(--color-red);">Saigon</span>
                </a>
            </div>

            <nav class="main-nav" id="main-nav">
                @foreach($mainMenu as $menu)
                    @if(empty($menu['is_mega']))
                        <a href="{{ url($menu['url'] ?? '#') }}" @class(['active' => request()->is(ltrim($menu['url'] ?? '', '/').'*') || request()->is(ltrim($menu['url'] ?? '', '/'))])>{{ $menu['label'] ?? '' }}</a>
                    @else
                        <div class="nav-item has-mega" aria-haspopup="true" aria-expanded="false">
                            <a href="{{ url($menu['url'] ?? '#') }}" @class(['active' => request()->is(ltrim($menu['url'] ?? '', '/').'*') || request()->is(ltrim($menu['url'] ?? '', '/'))])>{{ $menu['label'] ?? '' }}</a>
                            
                            {{-- UX: Invisible bridge to prevent Diagonal Problem --}}
                            <div class="mega-bridge"></div>
                            
                            <div class="mega-menu">
                                <div class="container mega-grid">
                                    @foreach(($menu['columns'] ?? []) as $col)
                                        @if(($col['type'] ?? 'links') === 'links')
                                            <div class="mega-column">
                                                <h4 class="mega-title">{{ $col['title'] ?? '' }}</h4>
                                                @foreach(($col['links'] ?? []) as $link)
                                                    <a href="{{ url($link['url'] ?? '#') }}">{{ $link['label'] ?? '' }}</a>
                                                @endforeach
                                            </div>
                                        @elseif(($col['type'] ?? '') === 'promo_banner')
                                            <div class="mega-column mega-promo">
                                                @if(!empty($col['image_path']))
                                                    <a href="{{ url($col['promo_url'] ?? '#') }}">
                                                        <img src="{{ asset('storage/' . $col['image_path']) }}" alt="Promo" style="border-radius: 8px; width: 100%; height: auto;">
                                                    </a>
                                                @endif
                                                @if(!empty($col['promo_text']))
                                                    <div class="promo-text" style="margin-top: 0.5rem; font-weight: 600;">{{ $col['promo_text'] }}</div>
                                                @endif
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            </nav>

            <div class="header-actions">
                <button class="action-btn" aria-label="Tìm kiếm">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </button>
                <button class="action-btn" aria-label="Tài khoản">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </button>
                <button class="action-btn" aria-label="Yêu thích">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                </button>
                @livewire('cart-icon')
                <button class="nav-toggle" id="nav-toggle" aria-label="Menu">
                    <span></span><span></span><span></span>
                </button>
            </div>
        </div>
    </header>

    {{-- Main Content --}}
    <main>
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="site-footer">
        <div class="container">
            <div class="footer-grid">
                <div>
                    <div class="footer-brand">MDG<span>Saigon</span></div>
                    <p class="footer-desc">
                        Chuyên cung cấp máy điện giải nước ion kiềm chính hãng, 
                        đem đến nguồn nước sạch và tốt cho sức khỏe gia đình bạn.
                    </p>
                    <p style="font-size: 0.875rem;">
                        📞 Hotline: <a href="tel:0901234567" style="color: var(--color-teal);">0901 234 567</a>
                    </p>
                </div>

                <div>
                    <h4 class="footer-heading">Sản phẩm</h4>
                    <ul class="footer-links">
                        <li><a href="{{ route('products.index') }}">Máy điện giải</a></li>
                        <li><a href="{{ route('products.index') }}">Phụ kiện</a></li>
                        <li><a href="{{ route('products.index') }}">Lõi lọc</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="footer-heading">Hỗ trợ</h4>
                    <ul class="footer-links">
                        <li><a href="{{ route('pages.show', 'gioi-thieu') }}">Giới thiệu</a></li>
                        <li><a href="{{ route('pages.show', 'lien-he') }}">Liên hệ</a></li>
                        <li><a href="{{ route('articles.index') }}">Tin tức</a></li>
                        @foreach(($footerPages['support'] ?? collect()) as $fp)
                            <li><a href="{{ route('pages.show', $fp->slug) }}">{{ $fp->title }}</a></li>
                        @endforeach
                    </ul>
                </div>

                <div>
                    <h4 class="footer-heading">Chính sách</h4>
                    <ul class="footer-links">
                        @forelse(($footerPages['policy'] ?? collect()) as $fp)
                            <li><a href="{{ route('pages.show', $fp->slug) }}">{{ $fp->title }}</a></li>
                        @empty
                            <li><a href="{{ route('pages.show', 'chinh-sach-bao-hanh') }}">Bảo hành</a></li>
                            <li><a href="{{ route('pages.show', 'chinh-sach-doi-tra') }}">Đổi trả</a></li>
                        @endforelse
                    </ul>
                </div>

                <div>
                    <h4 class="footer-heading">Đăng ký nhận tin</h4>
                    <p class="footer-desc" style="margin-bottom: 1rem;">Nhận thông tin khuyến mãi và kiến thức sức khỏe mới nhất.</p>
                    <form style="display: flex; gap: 0.5rem;">
                        <input type="email" placeholder="Email của bạn..." class="form-input" style="padding: 0.5rem; height: 40px; background: rgba(255,255,255,0.05); color: white; border-color: rgba(255,255,255,0.1);">
                        <button type="submit" class="btn btn-primary" style="padding: 0 1rem; height: 40px;">Gửi</button>
                    </form>
                </div>
            </div>

            <div class="footer-bottom">
                <p>&copy; {{ date('Y') }} Máy Điện Giải Sài Gòn. Tất cả quyền được bảo lưu.</p>
            </div>
        </div>
    </footer>

    {{-- Cart Drawer (Livewire Interactive Island) --}}
    @livewire('cart-drawer')

    @livewireScripts
    <script>
        // Sticky Header & Nav Toggle
        const header = document.getElementById('site-header');
        const navToggle = document.getElementById('nav-toggle');
        const mainNav = document.getElementById('main-nav');

        window.addEventListener('scroll', () => {
            if (window.scrollY > 50) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }
        });

        navToggle.addEventListener('click', () => {
            mainNav.classList.toggle('open');
        });

        // Intersection Observer for Animations
        const observerOptions = { threshold: 0.1 };
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                }
            });
        }, observerOptions);

        document.querySelectorAll('.animate-on-scroll').forEach(el => observer.observe(el));

        // Swiper Initialization
        const swiper = new Swiper('.hero-slider', {
            loop: true,
            pagination: { el: '.swiper-pagination', clickable: true },
            navigation: { nextEl: '.swiper-button-next', prevEl: '.swiper-button-prev' },
            autoplay: { delay: 5000 },
        });
    </script>
</body>
</html>
