@extends('themes.woodmart.layouts.app')

@section('title', 'Máy Điện Giải Sài Gòn - Woodmart Electronics Demo')

@section('content')
{{-- Woodmart Hero Section: Sidebar + Banner --}}
<section style="background: white; padding-bottom: 2rem;">
    <div class="container">
        <div style="display: flex; gap: 2rem;">
            {{-- Vertical Sidebar (Static for now) --}}
            <div style="width: 280px; flex-shrink: 0; display: flex; flex-direction: column; border: 1px solid #eee; border-top: none;">
                <div style="padding: 1rem; border-bottom: 1px solid #eee; font-weight: 600;">Panasonic</div>
                <div style="padding: 1rem; border-bottom: 1px solid #eee; font-weight: 600;">Kangen Enagic</div>
                <div style="padding: 1rem; border-bottom: 1px solid #eee; font-weight: 600;">Fuji Smart</div>
                <div style="padding: 1rem; border-bottom: 1px solid #eee; font-weight: 600;">Trim Ion</div>
                <div style="padding: 1rem; border-bottom: 1px solid #eee; font-weight: 600;">Mitsubishi</div>
                <div style="padding: 1rem; border-bottom: 1px solid #eee; font-weight: 600;">Lõi lọc & Phụ kiện</div>
            </div>

            {{-- Slider --}}
            <div style="flex: 1; min-width: 0;">
                <div class="hero-slider swiper" style="border-radius: 0; height: 450px;">
                    <div class="swiper-wrapper">
                        @foreach($banners as $banner)
                        <div class="swiper-slide hero-slide" style="background-image: url('{{ str_starts_with($banner->image_path, 'http') ? $banner->image_path : asset('storage/' . $banner->image_path) }}');">
                            <div style="position: absolute; inset: 0; background: rgba(0,0,0,0.1);"></div>
                            <div style="position: relative; height: 100%; display: flex; align-items: center; padding: 0 4rem;">
                                <div style="max-width: 500px;">
                                    <p style="color: var(--color-blue); font-weight: 700; margin-bottom: 0.5rem; text-transform: uppercase;">{{ $banner->eyebrow }}</p>
                                    <h2 style="font-size: 3rem; font-weight: 800; line-height: 1.1; margin-bottom: 1rem; color: #222;">
                                        {{ $banner->heading }} <span style="color: var(--color-blue);">{{ $banner->sub_heading }}</span>
                                    </h2>
                                    <a href="{{ url($banner->button_link) }}" class="btn btn-primary" style="padding: 0.75rem 2rem;">{{ $banner->button_text }}</a>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div class="swiper-pagination"></div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Featured Products (Woodmart Style) --}}
<section class="section" style="background: var(--color-gray-50);">
    <div class="container">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; border-bottom: 2px solid #eee; padding-bottom: 1rem;">
            <h2 style="font-size: 1.5rem; font-weight: 700; border-bottom: 2px solid var(--color-blue); margin-bottom: -1.1rem; padding-bottom: 1rem;">SẢN PHẨM NỔI BẬT</h2>
            <a href="{{ route('products.index') }}" style="font-size: 13px; font-weight: 700; color: #777;">XEM TẤT CẢ</a>
        </div>

        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1.5rem;">
            @foreach($featuredProducts as $product)
                <div class="product-card" style="padding: 1rem;">
                    <a href="{{ route('products.show', $product->slug) }}">
                        <div style="aspect-ratio: 1; overflow: hidden; margin-bottom: 1rem;">
                            @if($product->getFirstMediaUrl('main_image', 'medium'))
                                <img src="{{ $product->getFirstMediaUrl('main_image', 'medium') }}" style="width: 100%; height: 100%; object-fit: contain;">
                            @else
                                <div style="width: 100%; height: 100%; background: #f9f9f9; display: flex; align-items: center; justify-content: center; font-size: 3rem;">📦</div>
                            @endif
                        </div>
                        <h3 style="font-size: 14px; font-weight: 600; margin-bottom: 0.5rem; height: 40px; overflow: hidden; color: #222;">{{ $product->name }}</h3>
                        <div style="color: var(--color-blue); font-weight: 700; font-size: 16px;">{{ $product->formatted_price }}</div>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Tech CTA --}}
<section style="padding: 4rem 0; background: var(--color-blue); color: white; text-align: center;">
    <div class="container">
        <h2 style="font-size: 2rem; font-weight: 800; margin-bottom: 1rem;">HỆ THỐNG MÁY ĐIỆN GIẢI CHÍNH HÃNG</h2>
        <p style="margin-bottom: 2rem; opacity: 0.9;">Tư vấn giải pháp nước sạch cho gia đình và doanh nghiệp. Bảo hành 5-8 năm.</p>
        <div style="display: flex; gap: 1rem; justify-content: center;">
            <a href="#" class="btn btn-dark" style="background: #222; border: none; padding: 1rem 2.5rem;">XEM BÁO GIÁ</a>
            <a href="#" class="btn btn-light" style="background: white; color: var(--color-blue); border: none; padding: 1rem 2.5rem;">LIÊN HỆ NGAY</a>
        </div>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        new Swiper('.hero-slider', {
            loop: true,
            pagination: { el: '.swiper-pagination', clickable: true },
            autoplay: { delay: 5000 },
        });
    });
</script>
@endsection
