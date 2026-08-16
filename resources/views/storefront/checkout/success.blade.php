@extends('layouts.storefront')

@pushonce('page_title')Đặt Hàng Thành Công @endpushonce
@pushonce('meta_description')Đơn hàng của bạn đã được xác nhận. Cảm ơn bạn đã mua sắm tại MYSHOP.@endpushonce

@section('content')

<div class="py-16 md:py-24">
    <div class="section-wrapper">
        <div class="max-w-lg mx-auto text-center">

            {{-- Success icon --}}
            <div class="flex items-center justify-center mb-8">
                <div class="w-16 h-16 border border-[#1a1a1a] flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 text-[#1a1a1a]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                    </svg>
                </div>
            </div>

            {{-- Heading --}}
            <h1 class="text-2xl font-medium tracking-wide mb-3">Đặt Hàng Thành Công</h1>
            <p class="text-sm text-[#888888] font-light mb-10 leading-relaxed">
                Cảm ơn bạn đã mua sắm, <strong class="text-[#1a1a1a] font-medium">{{ $order->customer_name }}</strong>.<br>
                Chúng tôi sẽ liên hệ xác nhận qua số điện thoại bạn đã cung cấp.
            </p>

            {{-- Order details card --}}
            <div class="bg-white border border-[#E5E5E5] mb-10">
                <div class="px-8 py-5 border-b border-[#E5E5E5]">
                    <p class="text-[10px] font-medium tracking-[0.2em] uppercase text-[#888888]">Chi Tiết Đơn Hàng</p>
                </div>
                <dl class="px-8 py-6 grid grid-cols-2 gap-x-6 gap-y-5 text-left">
                    <div>
                        <dt class="text-[10px] tracking-[0.15em] uppercase text-[#888888] mb-1">Mã đơn hàng</dt>
                        <dd class="text-sm font-medium">{{ $order->order_number }}</dd>
                    </div>
                    <div>
                        <dt class="text-[10px] tracking-[0.15em] uppercase text-[#888888] mb-1">Trạng thái</dt>
                        <dd class="text-sm font-light">Đang xử lý</dd>
                    </div>
                    <div>
                        <dt class="text-[10px] tracking-[0.15em] uppercase text-[#888888] mb-1">Tổng tiền</dt>
                        <dd class="text-sm font-medium">{{ number_format($order->total_amount, 0, ',', '.') }}₫</dd>
                    </div>
                    <div>
                        <dt class="text-[10px] tracking-[0.15em] uppercase text-[#888888] mb-1">Thanh toán</dt>
                        <dd class="text-sm font-light">
                            {{ $order->payment_method === 'cod' ? 'COD — Khi nhận hàng' : $order->payment_method }}
                        </dd>
                    </div>
                </dl>
            </div>

            {{-- CTA buttons --}}
            <div class="flex flex-col sm:flex-row justify-center gap-4">
                <a href="{{ route('track-order.index') }}?order_number={{ $order->order_number }}"
                   class="btn-outline">
                    Tra Cứu Đơn Hàng
                </a>
                <a href="{{ route('products.index') }}"
                   class="btn-dark">
                    Tiếp Tục Mua Sắm
                </a>
            </div>

        </div>
    </div>
</div>

@endsection
