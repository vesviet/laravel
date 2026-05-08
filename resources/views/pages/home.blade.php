@extends('layouts.app')

@section('title', 'Máy Điện Giải Sài Gòn - Nước Ion Kiềm Chính Hãng')

@section('content')
{{-- Hero Slider --}}
<section class="hero-slider swiper">
    <div class="swiper-wrapper">
        @foreach($banners as $banner)
        <div class="swiper-slide hero-slide" style="background-image: url('{{ str_starts_with($banner->image_path, 'http') ? $banner->image_path : asset('storage/' . $banner->image_path) }}');">
            <div class="hero-overlay"></div>
            <div class="container hero-content-wrap">
                <div class="hero-text-block">
                    @if($banner->eyebrow)
                        <p class="hero-eyebrow">{{ $banner->eyebrow }}</p>
                    @endif
                    
                    @if($banner->heading)
                        <h1 class="hero-title">{{ $banner->heading }}
                            @if($banner->sub_heading)
                                <br><span>{{ $banner->sub_heading }}</span>
                            @endif
                        </h1>
                    @endif
                    
                    @if($banner->description)
                        <p class="hero-desc">{{ $banner->description }}</p>
                    @endif
                    
                    @if($banner->button_text && $banner->button_link)
                        <div class="hero-actions">
                            <a href="{{ url($banner->button_link) }}" class="btn btn-hero-primary">{{ $banner->button_text }}</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
    <div class="swiper-pagination"></div>
    <div class="swiper-button-next"></div>
    <div class="swiper-button-prev"></div>
</section>

{{-- Features (Elomus Layout) --}}
<section class="section features-elomus">
    <div class="container">
        <div class="features-wrapper">
            <div class="features-col">
                <div class="feature-item animate-on-scroll">
                    <div class="feature-icon">🛡️</div>
                    <div>
                        <h4 class="feature-title">Bảo Hành 5 Năm</h4>
                        <p class="feature-desc">Cam kết chính hãng toàn quốc</p>
                    </div>
                </div>
                <div class="feature-item animate-on-scroll">
                    <div class="feature-icon">🔧</div>
                    <div>
                        <h4 class="feature-title">Lắp Đặt Miễn Phí</h4>
                        <p class="feature-desc">Kỹ thuật viên phục vụ tận nhà</p>
                    </div>
                </div>
            </div>
            <div class="features-center animate-on-scroll">
                <img src="https://images.unsplash.com/photo-1584622650111-993a426fbf0a?auto=format&fit=crop&q=80&w=600" alt="Máy điện giải">
            </div>
            <div class="features-col">
                <div class="feature-item animate-on-scroll">
                    <div class="feature-icon">🏥</div>
                    <div>
                        <h4 class="feature-title">Chứng Nhận Y Tế</h4>
                        <p class="feature-desc">Đạt chuẩn Bộ Y Tế Nhật Bản</p>
                    </div>
                </div>
                <div class="feature-item animate-on-scroll">
                    <div class="feature-icon">🚚</div>
                    <div>
                        <h4 class="feature-title">Giao Hàng 24h</h4>
                        <p class="feature-desc">Miễn phí vận chuyển toàn quốc</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Brand Logos --}}
<section style="padding: 2rem 0; border-bottom: 1px solid var(--color-gray-100);">
    <div class="container">
        <div style="display: flex; justify-content: space-between; align-items: center; gap: 2rem; opacity: 0.6; flex-wrap: wrap;">
            <span style="font-weight: 800; color: var(--color-navy-muted);">PANASONIC</span>
            <span style="font-weight: 800; color: var(--color-navy-muted);">KANGEN</span>
            <span style="font-weight: 800; color: var(--color-navy-muted);">FUJI SMART</span>
            <span style="font-weight: 800; color: var(--color-navy-muted);">TRIM ION</span>
            <span style="font-weight: 800; color: var(--color-navy-muted);">MITSUBISHI</span>
        </div>
    </div>
</section>

{{-- Featured Products --}}
<section class="section">
    <div class="container">
        <div class="section-header">
            <div class="section-badge">Sản phẩm nổi bật</div>
            <h2 class="section-title">Máy Điện Giải Bán Chạy</h2>
            <p class="section-desc">Những dòng máy được khách hàng tin dùng nhất</p>
        </div>

        <div class="product-grid">
            @forelse($featuredProducts as $product)
                <div class="product-card animate-on-scroll">
                    <a href="{{ route('products.show', $product->slug) }}">
                        <div class="product-card-image">
                            @if($product->getFirstMediaUrl('main_image', 'medium'))
                                <img src="{{ $product->getFirstMediaUrl('main_image', 'medium') }}" 
                                     alt="{{ $product->name }}" loading="lazy">
                            @else
                                <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;color:var(--color-gray-300);font-size:3rem;">📦</div>
                            @endif

                            @if($product->discount_percent)
                                <span class="product-card-badge">-{{ $product->discount_percent }}%</span>
                            @endif
                        </div>
                    </a>

                    <div class="product-card-body">
                        @if($product->category)
                            <div class="product-card-category">{{ $product->category->name }}</div>
                        @endif
                        <h3 class="product-card-name">
                            <a href="{{ route('products.show', $product->slug) }}">{{ $product->name }}</a>
                        </h3>
                        <div class="product-card-price">
                            <span class="price-current">{{ $product->formatted_price }}</span>
                            @if($product->formatted_original_price)
                                <span class="price-original">{{ $product->formatted_original_price }}</span>
                            @endif
                        </div>
                    </div>

                    <div class="product-card-actions">
                        @livewire('add-to-cart-button', [
                            'productId' => $product->id,
                            'productName' => $product->name,
                            'productPrice' => $product->price,
                            'productImage' => $product->getFirstMediaUrl('main_image', 'thumb'),
                            'size' => 'sm',
                        ], key('home-atc-'.$product->id))
                    </div>
                </div>
            @empty
                <p style="grid-column:1/-1;text-align:center;color:var(--color-gray-500);padding:3rem;">
                    Chưa có sản phẩm nào. Vui lòng thêm sản phẩm trong trang Quản trị.
                </p>
            @endforelse
        </div>

        @if($featuredProducts->count() > 0)
            <div style="text-align:center;margin-top:2.5rem;">
                <a href="{{ route('products.index') }}" class="btn btn-dark">Xem tất cả sản phẩm →</a>
            </div>
        @endif
    </div>
</section>

{{-- Latest Articles --}}
@if($latestArticles->count() > 0)
<section class="section" style="background: var(--color-gray-50);">
    <div class="container">
        <div class="section-header">
            <div class="section-badge">Tin tức & Kiến thức</div>
            <h2 class="section-title">Bài Viết Mới Nhất</h2>
        </div>

        <div class="product-grid" style="grid-template-columns: repeat(3, 1fr);">
            @foreach($latestArticles as $article)
                <div class="product-card animate-on-scroll">
                    <a href="{{ route('articles.show', $article->slug) }}">
                        <div class="product-card-image" style="aspect-ratio:16/10;">
                            @if($article->getFirstMediaUrl('featured_image', 'banner'))
                                <img src="{{ $article->getFirstMediaUrl('featured_image', 'banner') }}" 
                                     alt="{{ $article->title }}" loading="lazy">
                            @else
                                <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;color:var(--color-gray-300);font-size:3rem;">📰</div>
                            @endif
                        </div>
                    </a>
                    <div class="product-card-body">
                        @if($article->category)
                            <div class="product-card-category">{{ $article->category->name }}</div>
                        @endif
                        <h3 class="product-card-name">
                            <a href="{{ route('articles.show', $article->slug) }}">{{ $article->title }}</a>
                        </h3>
                        @if($article->excerpt)
                            <p style="font-size:0.875rem;color:var(--color-gray-500);margin-top:0.5rem;">{{ Str::limit($article->excerpt, 100) }}</p>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- CTA Banner --}}
<section class="section" style="background: linear-gradient(135deg, var(--color-navy) 0%, var(--color-navy-muted) 100%); color: var(--color-white); text-align: center;">
    <div class="container">
        <h2 style="font-size: var(--font-size-3xl); font-weight: 800; margin-bottom: 1rem;">Bạn cần tư vấn?</h2>
        <p style="font-size: var(--font-size-lg); color: rgba(255,255,255,0.8); margin-bottom: 2rem; max-width: 600px; margin-left: auto; margin-right: auto;">
            Liên hệ ngay để được đội ngũ chuyên gia tư vấn dòng máy phù hợp nhất cho gia đình bạn.
        </p>
        <a href="tel:0901234567" class="btn btn-primary btn-lg">📞 Gọi ngay: 0901 234 567</a>
    </div>
</section>
@endsection
