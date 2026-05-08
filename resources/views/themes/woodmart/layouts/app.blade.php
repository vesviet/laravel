<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Máy Điện Giải Sài Gòn - Giao diện Woodmart')</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/themes/' . $activeTheme . '.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    @livewireStyles
</head>
<body class="theme-woodmart">
    <header class="woodmart-header">
        {{-- Top Bar --}}
        <div class="header-top">
            <div class="container" style="display: flex; justify-content: space-between; align-items: center; height: 100%;">
                <div>Chào mừng bạn đến với Máy Điện Giải Sài Gòn!</div>
                <div style="display: flex; gap: 1.5rem;">
                    <a href="#">Hotline: 0901 234 567</a>
                    <a href="#">Kiểm tra đơn hàng</a>
                </div>
            </div>
        </div>

        {{-- Main Header --}}
        <div class="header-main">
            <div class="container" style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
                <div class="logo">
                    <a href="{{ route('home') }}" style="font-size: 1.75rem; font-weight: 800; color: var(--color-blue);">
                        MDG<span style="color: #333;">SAIGON</span>
                    </a>
                </div>

                <div class="search-wrapper">
                    <form action="#" class="woodmart-search">
                        <input type="text" placeholder="Tìm kiếm sản phẩm máy điện giải...">
                        <button type="submit">TÌM KIẾM</button>
                    </form>
                </div>

                <div class="header-icons" style="display: flex; gap: 1.5rem; align-items: center;">
                    <div style="text-align: center; cursor: pointer;">
                        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        <div style="font-size: 10px; font-weight: 700;">TÀI KHOẢN</div>
                    </div>
                    @livewire('cart-icon')
                </div>
            </div>
        </div>

        {{-- Bottom Navigation --}}
        <div class="header-bottom">
            <div class="container" style="display: flex; align-items: center; height: 100%;">
                <div class="vertical-menu-wrapper" style="height: 100%; display: flex; align-items: center;">
                    <div class="vertical-menu-title" style="height: 100%; width: 100%;">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
                        DANH MỤC SẢN PHẨM
                    </div>
                </div>
                <nav style="display: flex; gap: 2rem; margin-left: 2rem; font-weight: 600; font-size: 14px;">
                    <a href="{{ route('home') }}">TRANG CHỦ</a>
                    <a href="{{ route('products.index') }}">MÁY ĐIỆN GIẢI</a>
                    <a href="{{ route('articles.index') }}">TIN TỨC</a>
                    <a href="#">LIÊN HỆ</a>
                </nav>
            </div>
        </div>
    </header>

    <main>
        @yield('content')
    </main>

    <footer class="site-footer" style="background: #222; color: white; padding: 4rem 0; margin-top: 4rem;">
        <div class="container">
            <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 2rem;">
                <div>
                    <h4 style="margin-bottom: 1.5rem; color: var(--color-blue);">VỀ CHÚNG TÔI</h4>
                    <p style="font-size: 14px; color: #aaa;">Chuyên cung cấp máy điện giải nước ion kiềm chính hãng, đem đến nguồn nước sạch và tốt cho sức khỏe.</p>
                </div>
                <div>
                    <h4 style="margin-bottom: 1.5rem;">SẢN PHẨM</h4>
                    <ul style="list-style: none; padding: 0; font-size: 14px; color: #aaa;">
                        <li>Máy Panasonic</li>
                        <li>Máy Kangen</li>
                        <li>Máy Fuji Smart</li>
                    </ul>
                </div>
                <div>
                    <h4 style="margin-bottom: 1.5rem;">HỖ TRỢ</h4>
                    <ul style="list-style: none; padding: 0; font-size: 14px; color: #aaa;">
                        <li>Chính sách bảo hành</li>
                        <li>Hướng dẫn mua hàng</li>
                        <li>Liên hệ</li>
                    </ul>
                </div>
                <div>
                    <h4 style="margin-bottom: 1.5rem;">NEWSLETTER</h4>
                    <p style="font-size: 14px; color: #aaa; margin-bottom: 1rem;">Đăng ký nhận tin khuyến mãi sớm nhất.</p>
                    <input type="email" placeholder="Email của bạn..." style="width: 100%; padding: 0.5rem; background: #333; border: none; color: white;">
                </div>
            </div>
        </div>
    </footer>

    @livewire('cart-drawer')
    @livewireScripts
</body>
</html>
