@extends('layouts.storefront')

@pushonce('page_title')Đặt Hàng Thành Công — @endpushonce
@pushonce('meta_description')Đơn hàng của bạn đã được xác nhận. Cảm ơn bạn đã mua sắm tại {{ config('app.name', 'Sober Furniture') }}.@endpushonce

@section('content')

<div class="py-12 md:py-16 bg-[#FAFAFA]">
    <div class="section-wrapper">
        <div class="max-w-2xl mx-auto">

            {{-- ── Success Header ── --}}
            <div class="text-center mb-10">
                <div class="w-16 h-16 bg-[#23232C] text-white flex items-center justify-center mx-auto mb-5 shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                    </svg>
                </div>
                <h1 class="text-2xl md:text-3xl font-light text-[#23232C] tracking-wide uppercase mb-2">Đặt Hàng Thành Công</h1>
                <p class="text-xs md:text-sm text-[#888888] font-light leading-relaxed max-w-md mx-auto">
                    Cảm ơn bạn đã mua sắm, <strong class="text-[#23232C] font-semibold">{{ $order->customer_name }}</strong>.<br>
                    Chúng tôi đã ghi nhận đơn hàng và sẽ liên hệ xác nhận trong thời gian sớm nhất.
                </p>
            </div>

            {{-- ── Order Overview Card ── --}}
            <div class="bg-white border border-[#E5E5E5] mb-8 shadow-sm">
                
                <div class="px-6 py-4 bg-[#F7F7F7] border-b border-[#E5E5E5] flex items-center justify-between">
                    <span class="text-[10px] font-semibold tracking-[0.2em] uppercase text-[#888888]">Chi Tiết Đơn Hàng</span>
                    <span class="text-xs font-semibold px-2.5 py-1 {{ $order->status_badge_classes }}">
                        {{ $order->status_label }}
                    </span>
                </div>

                <dl class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-y-4 gap-x-6 text-left border-b border-[#EBEBEB]">
                    <div>
                        <dt class="text-[10px] tracking-[0.15em] uppercase text-[#888888] mb-1">Mã đơn hàng</dt>
                        <dd class="text-sm font-semibold text-[#23232C]">{{ $order->order_number }}</dd>
                    </div>
                    <div>
                        <dt class="text-[10px] tracking-[0.15em] uppercase text-[#888888] mb-1">Trạng thái</dt>
                        <dd class="text-sm font-light text-[#23232C]">{{ $order->status_label }}</dd>
                    </div>
                    <div>
                        <dt class="text-[10px] tracking-[0.15em] uppercase text-[#888888] mb-1">Tổng tiền</dt>
                        <dd class="text-sm font-bold text-[#E84444]">{{ $order->formatted_total_amount }}</dd>
                    </div>
                    <div>
                        <dt class="text-[10px] tracking-[0.15em] uppercase text-[#888888] mb-1">Thanh toán</dt>
                        <dd class="text-sm font-light text-[#23232C]">
                            {{ $order->payment_method === 'cod' ? 'COD — Thanh toán khi nhận hàng' : strtoupper($order->payment_method) }}
                        </dd>
                    </div>
                    @if($order->address)
                        <div class="sm:col-span-2 pt-2 border-t border-[#F0F0F0]">
                            <dt class="text-[10px] tracking-[0.15em] uppercase text-[#888888] mb-1">Địa chỉ nhận hàng</dt>
                            <dd class="text-xs font-light text-[#23232C] leading-relaxed">
                                {{ $order->address }}
                                @if($order->ward), {{ $order->ward }}@endif
                                @if($order->district), {{ $order->district }}@endif
                                @if($order->city), {{ $order->city }}@endif
                                (SĐT: <strong>{{ $order->phone }}</strong>)
                            </dd>
                        </div>
                    @endif
                </dl>

                {{-- Purchased Items --}}
                <div class="p-6">
                    <h4 class="text-[10px] font-semibold tracking-[0.2em] uppercase text-[#888888] mb-4">Sản Phẩm Đã Mua</h4>
                    <ul class="divide-y divide-[#EBEBEB]">
                        @foreach($order->items as $item)
                            <li class="py-3.5 flex items-center justify-between gap-4">
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="w-12 h-12 bg-[#F7F7F7] border border-[#E5E5E5] flex items-center justify-center shrink-0 overflow-hidden">
                                        @if($item->thumbnail_url)
                                            <img src="{{ $item->thumbnail_url }}" alt="{{ $item->product_name }}" class="w-full h-full object-contain p-1">
                                        @else
                                            <span class="text-[9px] text-[#888888]">No img</span>
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-xs font-semibold text-[#23232C] truncate">{{ $item->product_name }}</p>
                                        @if($item->variant_name)
                                            <p class="text-[11px] text-[#888888]">{{ $item->variant_name }}</p>
                                        @endif
                                        <p class="text-[11px] text-[#888888]">Số lượng: {{ $item->quantity }} × {{ $item->formatted_price }}</p>
                                    </div>
                                </div>
                                <span class="text-xs font-semibold text-[#23232C] shrink-0">
                                    {{ $item->formatted_subtotal }}
                                </span>
                            </li>
                        @endforeach
                    </ul>

                    {{-- Financial Summary Breakdown --}}
                    <div class="mt-4 pt-4 border-t border-[#EBEBEB] space-y-1.5 text-xs">
                        <div class="flex justify-between text-[#888888]">
                            <span>Tạm tính</span>
                            <span>{{ $order->formatted_subtotal }}</span>
                        </div>
                        @if($order->discount_amount > 0)
                            <div class="flex justify-between text-[#E84444]">
                                <span>Giảm giá</span>
                                <span>-{{ $order->formatted_discount_amount }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between text-[#888888]">
                            <span>Phí vận chuyển</span>
                            <span>{{ $order->shipping_fee > 0 ? $order->formatted_shipping_fee : 'Miễn phí' }}</span>
                        </div>
                        <div class="flex justify-between text-sm font-bold text-[#23232C] pt-2 border-t border-[#EBEBEB]">
                            <span>Tổng thanh toán</span>
                            <span class="text-[#E84444]">{{ $order->formatted_total_amount }}</span>
                        </div>
                    </div>
                </div>

            </div>

            {{-- ── CTA Actions ── --}}
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="{{ route('track-order.index', ['order_number' => $order->order_number, 'contact_info' => $order->phone ?? $order->email]) }}"
                   class="w-full sm:w-auto px-6 py-3 border border-[#23232C] text-[#23232C] hover:bg-[#23232C] hover:text-white transition-all text-center text-xs font-semibold uppercase tracking-wider">
                    Tra Cứu Đơn Hàng
                </a>
                <a href="{{ route('products.index') }}"
                   class="w-full sm:w-auto px-6 py-3 bg-[#23232C] text-white hover:bg-black transition-all text-center text-xs font-semibold uppercase tracking-wider">
                    Tiếp Tục Mua Sắm
                </a>
            </div>

        </div>
    </div>
</div>

@endsection
