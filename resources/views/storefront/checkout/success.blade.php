@extends('layouts.storefront')

@pushonce('page_title')Đặt Hàng Thành Công — @endpushonce
@pushonce('meta_description')Đơn hàng của bạn đã được xác nhận. Cảm ơn bạn đã mua sắm tại {{ config('app.name', 'Sober Furniture') }}.@endpushonce

@section('content')

<div class="py-12 md:py-16 bg-[#FAFAFA]">
    <div class="section-wrapper">
        <div class="max-w-2xl mx-auto">

            {{-- ── Success Header ── --}}
            <div class="text-center mb-10">
                <div class="w-16 h-16 bg-primary-dark text-white flex items-center justify-center mx-auto mb-5 shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                    </svg>
                </div>
                <h1 class="text-2xl md:text-3xl font-light text-primary-dark tracking-wide uppercase mb-2">Đặt Hàng Thành Công</h1>
                <p class="text-xs md:text-sm text-muted-text font-light leading-relaxed max-w-md mx-auto">
                    Cảm ơn bạn đã mua sắm, <strong class="text-primary-dark font-semibold">{{ $order->customer_name }}</strong>.<br>
                    Chúng tôi đã ghi nhận đơn hàng và sẽ liên hệ xác nhận trong thời gian sớm nhất.
                </p>
            </div>

            {{-- ── Order Overview Card ── --}}
            <div class="bg-white border border-border-subtle mb-8 shadow-sm">
                
                <div class="px-6 py-4 bg-surface-light border-b border-border-subtle flex items-center justify-between">
                    <span class="text-[10px] font-semibold tracking-[0.2em] uppercase text-muted-text">Chi Tiết Đơn Hàng</span>
                    <span class="text-xs font-semibold px-2.5 py-1 {{ $order->status_badge_classes }}">
                        {{ $order->status_label }}
                    </span>
                </div>

                <dl class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-y-4 gap-x-6 text-left border-b border-[#EBEBEB]">
                    <div>
                        <dt class="text-[10px] tracking-[0.15em] uppercase text-muted-text mb-1">Mã đơn hàng</dt>
                        <dd class="text-sm font-semibold text-primary-dark">{{ $order->order_number }}</dd>
                    </div>
                    <div>
                        <dt class="text-[10px] tracking-[0.15em] uppercase text-muted-text mb-1">Trạng thái</dt>
                        <dd class="text-sm font-light text-primary-dark">{{ $order->status_label }}</dd>
                    </div>
                    <div>
                        <dt class="text-[10px] tracking-[0.15em] uppercase text-muted-text mb-1">Tổng tiền</dt>
                        <dd class="text-sm font-bold text-badge-hot">{{ $order->formatted_total_amount }}</dd>
                    </div>
                    <div>
                        <dt class="text-[10px] tracking-[0.15em] uppercase text-muted-text mb-1">Thanh toán</dt>
                        <dd class="text-sm font-light text-primary-dark flex items-center gap-2">
                            <span>{{ $order->payment_method === 'cod' ? 'COD — Thanh toán khi nhận hàng' : strtoupper($order->payment_method) }}</span>
                            <span class="text-[11px] font-medium px-2 py-0.5 {{ $order->payment_status_badge_classes }}">
                                {{ $order->payment_status_label }}
                            </span>
                        </dd>
                    </div>
                    @if($order->address)
                        <div class="sm:col-span-2 pt-2 border-t border-surface-bg">
                            <dt class="text-[10px] tracking-[0.15em] uppercase text-muted-text mb-1">Địa chỉ nhận hàng</dt>
                            <dd class="text-xs font-light text-primary-dark leading-relaxed">
                                {{ $order->address }}
                                @if($order->ward), {{ $order->ward }}@endif
                                @if($order->district), {{ $order->district }}@endif
                                @if($order->city), {{ $order->city }}@endif
                                (SĐT: <strong>{{ $order->phone }}</strong>)
                            </dd>
                        </div>
                    @endif
                </dl>

                {{-- VietQR Transfer Section if payment is online/banking --}}
                @if(($order->payment_method === 'vietqr' || $order->payment_method === 'banking') && !empty($order->payment_details['qr_url']))
                    <div class="p-6 bg-surface-light border-b border-border-subtle">
                        <div class="flex flex-col sm:flex-row items-center gap-6">
                            <div class="shrink-0 bg-white p-2 border border-border-subtle shadow-sm">
                                <img src="{{ $order->payment_details['qr_url'] }}" alt="VietQR Payment" class="w-44 h-44 object-contain">
                            </div>
                            <div class="text-left space-y-2 text-xs">
                                <h4 class="text-sm font-semibold text-primary-dark uppercase tracking-wide">Quét Mã VietQR Để Thanh Toán</h4>
                                <p class="text-muted-text">Mở ứng dụng ngân hàng hoặc ví điện tử để quét mã thanh toán tự động.</p>
                                <div class="bg-white p-3 border border-border-subtle space-y-1 font-mono text-[11px]">
                                    <div>Ngân hàng: <strong>{{ $order->payment_details['bank_code'] ?? 'MB' }}</strong></div>
                                    <div>Số tài khoản: <strong>{{ $order->payment_details['bank_account_no'] ?? '' }}</strong></div>
                                    <div>Chủ tài khoản: <strong>{{ $order->payment_details['account_name'] ?? '' }}</strong></div>
                                    <div>Số tiền: <strong class="text-badge-hot">{{ $order->formatted_total_amount }}</strong></div>
                                    <div>Nội dung CK: <strong class="text-primary-dark bg-amber-50 px-1.5 py-0.5 border border-amber-200">{{ $order->payment_details['transfer_syntax'] ?? "ORD {$order->order_number}" }}</strong></div>
                                </div>
                                <p class="text-[10px] text-muted-text italic">* Vui lòng giữ nguyên nội dung chuyển khoản để hệ thống tự động xác nhận.</p>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Purchased Items --}}
                <div class="p-6">
                    <h4 class="text-[10px] font-semibold tracking-[0.2em] uppercase text-muted-text mb-4">Sản Phẩm Đã Mua</h4>
                    <ul class="divide-y divide-[#EBEBEB]">
                        @foreach($order->items as $item)
                            <li class="py-3.5 flex items-center justify-between gap-4">
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="w-12 h-12 bg-surface-light border border-border-subtle flex items-center justify-center shrink-0 overflow-hidden">
                                        @if($item->thumbnail_url)
                                            <img src="{{ $item->thumbnail_url }}" alt="{{ $item->product_name }}" class="w-full h-full object-contain p-1">
                                        @else
                                            <span class="text-[9px] text-muted-text">No img</span>
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-xs font-semibold text-primary-dark truncate">{{ $item->product_name }}</p>
                                        @if($item->variant_name)
                                            <p class="text-[11px] text-muted-text">{{ $item->variant_name }}</p>
                                        @endif
                                        <p class="text-[11px] text-muted-text">Số lượng: {{ $item->quantity }} × {{ $item->formatted_price }}</p>
                                    </div>
                                </div>
                                <span class="text-xs font-semibold text-primary-dark shrink-0">
                                    {{ $item->formatted_subtotal }}
                                </span>
                            </li>
                        @endforeach
                    </ul>

                    {{-- Financial Summary Breakdown --}}
                    <div class="mt-4 pt-4 border-t border-[#EBEBEB] space-y-1.5 text-xs">
                        <div class="flex justify-between text-muted-text">
                            <span>Tạm tính</span>
                            <span>{{ $order->formatted_subtotal }}</span>
                        </div>
                        @if($order->discount_amount > 0)
                            <div class="flex justify-between text-badge-hot">
                                <span>Giảm giá</span>
                                <span>-{{ $order->formatted_discount_amount }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between text-muted-text">
                            <span>Phí vận chuyển</span>
                            <span>{{ $order->shipping_fee > 0 ? $order->formatted_shipping_fee : 'Miễn phí' }}</span>
                        </div>
                        <div class="flex justify-between text-sm font-bold text-primary-dark pt-2 border-t border-[#EBEBEB]">
                            <span>Tổng thanh toán</span>
                            <span class="text-badge-hot">{{ $order->formatted_total_amount }}</span>
                        </div>
                    </div>
                </div>

            </div>

            {{-- ── CTA Actions ── --}}
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="{{ route('track-order.index', ['order_number' => $order->order_number, 'contact_info' => $order->phone ?? $order->email]) }}"
                   class="w-full sm:w-auto px-6 py-3 border border-primary-dark text-primary-dark hover:bg-primary-dark hover:text-white transition-all text-center text-xs font-semibold uppercase tracking-wider">
                    Tra Cứu Đơn Hàng
                </a>
                <a href="{{ route('products.index') }}"
                   class="w-full sm:w-auto px-6 py-3 bg-primary-dark text-white hover:bg-black transition-all text-center text-xs font-semibold uppercase tracking-wider">
                    Tiếp Tục Mua Sắm
                </a>
            </div>

        </div>
    </div>
</div>

@endsection
