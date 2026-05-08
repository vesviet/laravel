@extends('layouts.app')

@section('title', ($product->meta_title ?: $product->name) . ' - Máy Điện Giải Sài Gòn')
@section('meta_description', $product->meta_description ?: $product->short_description)

@section('content')
<div style="height: var(--header-height);"></div>

<section class="section">
    <div class="container">
        {{-- Breadcrumb --}}
        <nav style="font-size:0.875rem;color:var(--color-gray-500);margin-bottom:2rem;">
            <a href="{{ route('home') }}">Trang chủ</a> /
            <a href="{{ route('products.index') }}">Sản phẩm</a> /
            @if($product->category)
                <a href="{{ route('products.index', ['category' => $product->category->slug]) }}">{{ $product->category->name }}</a> /
            @endif
            <span style="color:var(--color-navy);">{{ $product->name }}</span>
        </nav>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:3rem;align-items:start;">
            {{-- Gallery --}}
            <div>
                <div style="border-radius:var(--radius-xl);overflow:hidden;background:var(--color-gray-50);aspect-ratio:1;">
                    @if($product->getFirstMediaUrl('main_image', 'large'))
                        <img src="{{ $product->getFirstMediaUrl('main_image', 'large') }}" alt="{{ $product->name }}" style="width:100%;height:100%;object-fit:cover;">
                    @else
                        <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;font-size:5rem;color:var(--color-gray-300);">📦</div>
                    @endif
                </div>

                @php $galleryMedia = $product->getMedia('gallery'); @endphp
                @if($galleryMedia->count())
                <div style="display:flex;gap:0.75rem;margin-top:1rem;overflow-x:auto;">
                    @foreach($galleryMedia as $media)
                        <div style="width:80px;height:80px;border-radius:var(--radius-md);overflow:hidden;flex-shrink:0;border:2px solid var(--color-gray-200);cursor:pointer;">
                            <img src="{{ $media->getUrl('thumb') }}" alt="" style="width:100%;height:100%;object-fit:cover;">
                        </div>
                    @endforeach
                </div>
                @endif
            </div>

            {{-- Product Info --}}
            <div>
                @if($product->category)
                    <div style="font-size:0.875rem;color:var(--color-teal-dark);font-weight:600;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:0.5rem;">
                        {{ $product->category->name }}
                    </div>
                @endif

                <h1 style="font-size:var(--font-size-3xl);font-weight:800;color:var(--color-navy);margin-bottom:0.5rem;">{{ $product->name }}</h1>

                @if($product->sku)
                    <p style="font-size:0.875rem;color:var(--color-gray-500);margin-bottom:1.5rem;">SKU: {{ $product->sku }}</p>
                @endif

                <div style="display:flex;align-items:baseline;gap:0.75rem;margin-bottom:1.5rem;">
                    <span style="font-size:var(--font-size-4xl);font-weight:800;color:var(--color-teal-dark);">{{ $product->formatted_price }}</span>
                    @if($product->formatted_original_price)
                        <span style="font-size:var(--font-size-xl);color:var(--color-gray-500);text-decoration:line-through;">{{ $product->formatted_original_price }}</span>
                        <span style="background:var(--color-danger);color:white;padding:0.25rem 0.75rem;border-radius:var(--radius-full);font-size:0.75rem;font-weight:700;">
                            -{{ $product->discount_percent }}%
                        </span>
                    @endif
                </div>

                @if($product->short_description)
                    <p style="color:var(--color-gray-700);line-height:1.7;margin-bottom:1.5rem;">{{ $product->short_description }}</p>
                @endif

                {{-- Add to Cart (Livewire Interactive Island) --}}
                @livewire('add-to-cart-button', [
                    'productId' => $product->id,
                    'productName' => $product->name,
                    'productPrice' => $product->price,
                    'productImage' => $product->getFirstMediaUrl('main_image', 'thumb'),
                    'showQuantity' => true,
                    'size' => 'lg',
                ], key('pdp-atc-'.$product->id))

                {{-- Trust Badges --}}
                <div class="trust-badges">
                    <div class="trust-badge">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/></svg>
                        Bảo hành 5 năm
                    </div>
                    <div class="trust-badge">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 00-10.026 0 1.106 1.106 0 00-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12"/></svg>
                        Miễn phí lắp đặt
                    </div>
                    <div class="trust-badge">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z"/></svg>
                        Chứng nhận y tế
                    </div>
                </div>
            </div>
        </div>

        {{-- Accordion Tabs (Description / Specs / Warranty) --}}
        <div style="margin-top:4rem;">
            <div class="accordion-item open">
                <button class="accordion-trigger">
                    Mô tả sản phẩm
                    <svg class="chevron" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
                </button>
                <div class="accordion-content" style="max-height:2000px;">
                    <div class="accordion-content-inner">
                        {!! $product->description ?: '<p>Chưa có mô tả chi tiết cho sản phẩm này.</p>' !!}
                    </div>
                </div>
            </div>

            @if($product->features && count($product->features) > 0)
            <div class="accordion-item">
                <button class="accordion-trigger">
                    Thông số kỹ thuật
                    <svg class="chevron" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
                </button>
                <div class="accordion-content">
                    <div class="accordion-content-inner">
                        <table style="width:100%;border-collapse:collapse;">
                            @foreach($product->features as $key => $value)
                                <tr style="border-bottom:1px solid var(--color-gray-100);">
                                    <td style="padding:0.75rem 1rem;font-weight:600;width:40%;color:var(--color-navy);">{{ $key }}</td>
                                    <td style="padding:0.75rem 1rem;color:var(--color-gray-700);">{{ $value }}</td>
                                </tr>
                            @endforeach
                        </table>
                    </div>
                </div>
            </div>
            @endif

            <div class="accordion-item">
                <button class="accordion-trigger">
                    Chính sách bảo hành
                    <svg class="chevron" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
                </button>
                <div class="accordion-content">
                    <div class="accordion-content-inner">
                        <ul style="padding-left:1.25rem;">
                            <li>Bảo hành chính hãng 5 năm cho tất cả máy điện giải</li>
                            <li>Miễn phí bảo trì định kỳ trong 2 năm đầu</li>
                            <li>Đổi mới sản phẩm trong 30 ngày nếu lỗi nhà sản xuất</li>
                            <li>Hỗ trợ kỹ thuật 24/7 qua hotline</li>
                            <li>Linh kiện thay thế chính hãng, có sẵn trong kho</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        {{-- Related Products --}}
        @if($relatedProducts->count())
        <div style="margin-top:4rem;">
            <h2 class="section-title" style="text-align:center;">Sản Phẩm Liên Quan</h2>
            <div class="product-grid" style="margin-top:2rem;">
                @foreach($relatedProducts as $rp)
                    <div class="product-card">
                        <a href="{{ route('products.show', $rp->slug) }}">
                            <div class="product-card-image">
                                @if($rp->getFirstMediaUrl('main_image', 'medium'))
                                    <img src="{{ $rp->getFirstMediaUrl('main_image', 'medium') }}" alt="{{ $rp->name }}" loading="lazy">
                                @else
                                    <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;color:var(--color-gray-300);font-size:3rem;">📦</div>
                                @endif
                            </div>
                        </a>
                        <div class="product-card-body">
                            <h3 class="product-card-name"><a href="{{ route('products.show', $rp->slug) }}">{{ $rp->name }}</a></h3>
                            <div class="product-card-price">
                                <span class="price-current">{{ $rp->formatted_price }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</section>

{{-- Mobile Sticky CTA --}}
<div class="mobile-sticky-cta">
    <div style="flex:1;">
        <div style="font-size:0.75rem;color:var(--color-gray-500);">Giá bán</div>
        <div style="font-size:1.25rem;font-weight:800;color:var(--color-teal-dark);">{{ $product->formatted_price }}</div>
    </div>
    @livewire('add-to-cart-button', [
        'productId' => $product->id,
        'productName' => $product->name,
        'productPrice' => $product->price,
        'productImage' => $product->getFirstMediaUrl('main_image', 'thumb'),
    ], key('mobile-atc-'.$product->id))
</div>
@endsection
