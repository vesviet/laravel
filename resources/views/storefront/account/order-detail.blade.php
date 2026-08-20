@extends('layouts.storefront')

@pushonce('page_title')Chi Tiết Đơn Hàng #{{ $order->order_number }} — @endpushonce
@pushonce('meta_description')Xem chi tiết và tiến trình xử lý đơn hàng #{{ $order->order_number }} tại {{ config('app.name', 'Sober Furniture') }}.@endpushonce

@section('content')
<div class="py-10 md:py-14 bg-[#FAFAFA]">
    <div class="section-wrapper">
        <div class="max-w-3xl mx-auto">

            {{-- ── Breadcrumb & Back button ── --}}
            <div class="mb-6">
                <a href="{{ route('account.orders') }}" class="text-xs uppercase tracking-wider font-semibold text-[#23232C] hover:text-[#E84444] inline-flex items-center gap-1.5 link-underline">
                    <span>&larr;</span> Quay lại danh sách đơn hàng
                </a>
            </div>

            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs mb-6">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 text-xs mb-6">
                    {{ session('error') }}
                </div>
            @endif

            {{-- ── Main Order Card ── --}}
            <div class="bg-white border border-[#E5E5E5] shadow-sm">
                
                {{-- Order Header --}}
                <div class="p-6 bg-[#F7F7F7] border-b border-[#E5E5E5] flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <span class="text-[10px] uppercase font-bold tracking-widest text-[#888888]">Mã Đơn Hàng</span>
                        <h1 class="text-lg sm:text-xl font-bold text-[#23232C]">{{ $order->order_number }}</h1>
                        <p class="text-xs text-[#888888] font-light mt-0.5">
                            Đặt ngày {{ $order->created_at->format('d/m/Y') }} lúc {{ $order->created_at->format('H:i') }}
                        </p>
                    </div>

                    <div class="flex items-center gap-3">
                        <span class="text-xs font-semibold px-3 py-1.5 {{ $order->status_badge_classes }}">
                            {{ $order->status_label }}
                        </span>
                    </div>
                </div>

                {{-- Delivery Progress Stepper (5 Steps) --}}
                @php
                    $statusSteps = [
                        ['status' => \App\Enums\OrderStatus::Pending, 'label' => 'Chờ xác nhận'],
                        ['status' => \App\Enums\OrderStatus::Confirmed, 'label' => 'Đã xác nhận'],
                        ['status' => \App\Enums\OrderStatus::Processing, 'label' => 'Đang chuẩn bị'],
                        ['status' => \App\Enums\OrderStatus::Shipped, 'label' => 'Đang giao'],
                        ['status' => \App\Enums\OrderStatus::Delivered, 'label' => 'Đã giao'],
                    ];
                    $currentIndex = 0;
                    foreach ($statusSteps as $idx => $step) {
                        if ($order->status === $step['status']) {
                            $currentIndex = $idx;
                            break;
                        }
                    }
                @endphp

                @if($order->status !== \App\Enums\OrderStatus::Cancelled)
                    <div class="p-6 border-b border-[#E5E5E5]">
                        <div class="grid grid-cols-5 gap-2 text-center relative">
                            @foreach($statusSteps as $idx => $step)
                                <div class="flex flex-col items-center">
                                    <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold mb-1.5 transition-colors {{ $idx <= $currentIndex ? 'bg-[#23232C] text-white' : 'bg-[#E5E5E5] text-[#888888]' }}">
                                        {{ $idx + 1 }}
                                    </div>
                                    <span class="text-[10px] font-medium {{ $idx <= $currentIndex ? 'text-[#23232C] font-semibold' : 'text-[#888888]' }}">
                                        {{ $step['label'] }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="p-4 bg-rose-50 border-b border-rose-100 text-center text-xs text-rose-700 font-medium">
                        Đơn hàng này đã bị hủy.
                    </div>
                @endif

                {{-- Recipient & Shipping Information --}}
                <div class="p-6 border-b border-[#E5E5E5] grid grid-cols-1 sm:grid-cols-2 gap-6 text-xs">
                    <div>
                        <h3 class="text-[10px] font-bold tracking-[0.2em] uppercase text-[#888888] mb-2">Thông Tin Nhận Hàng</h3>
                        <p class="font-semibold text-[#23232C] text-sm">{{ $order->customer_name }}</p>
                        <p class="text-[#555555] mt-1">SĐT: <strong>{{ $order->phone }}</strong></p>
                        @if($order->email)
                            <p class="text-[#555555]">Email: {{ $order->email }}</p>
                        @endif
                        <p class="text-[#555555] mt-1.5 leading-relaxed">
                            {{ $order->address }}
                            @if($order->ward), {{ $order->ward }}@endif
                            @if($order->district), {{ $order->district }}@endif
                            @if($order->city), {{ $order->city }}@endif
                        </p>
                    </div>

                    <div>
                        <h3 class="text-[10px] font-bold tracking-[0.2em] uppercase text-[#888888] mb-2">Phương Thức Thanh Toán</h3>
                        <p class="font-semibold text-[#23232C]">
                            {{ $order->payment_method === 'cod' ? 'Thanh toán khi nhận hàng (COD)' : strtoupper($order->payment_method) }}
                        </p>
                        @if($order->notes)
                            <div class="mt-3 p-2.5 bg-[#F9F9F9] border border-[#E5E5E5] text-[11px] text-[#555555]">
                                <strong>Ghi chú:</strong> {{ $order->notes }}
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Items List --}}
                <div class="p-6">
                    <h3 class="text-[10px] font-bold tracking-[0.2em] uppercase text-[#888888] mb-4">Danh Sách Sản Phẩm</h3>
                    <ul class="divide-y divide-[#EBEBEB]">
                        @foreach($order->items as $item)
                            <li class="py-4 flex items-center justify-between gap-4">
                                <div class="flex items-center gap-3.5 min-w-0">
                                    <div class="w-14 h-14 bg-[#F7F7F7] border border-[#E5E5E5] shrink-0 overflow-hidden flex items-center justify-center">
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

                    {{-- Financial Breakdown --}}
                    <div class="mt-6 pt-4 border-t border-[#EBEBEB] space-y-2 text-xs">
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
                        <div class="flex justify-between text-sm font-bold text-[#23232C] pt-3 border-t border-[#EBEBEB]">
                            <span>Tổng thanh toán</span>
                            <span class="text-base text-[#E84444]">{{ $order->formatted_total_amount }}</span>
                        </div>
                    </div>
                </div>

                {{-- Footer Actions --}}
                <div class="p-6 bg-[#F9F9F9] border-t border-[#E5E5E5] flex flex-wrap items-center justify-between gap-4">
                    <form method="POST" action="{{ route('account.orders.reorder', $order->order_number) }}">
                        @csrf
                        <button type="submit" class="px-5 py-2.5 bg-[#23232C] text-white hover:bg-black text-xs font-semibold uppercase tracking-wider transition-colors cursor-pointer">
                            Mua Lại Đơn Hàng Này
                        </button>
                    </form>

                    @if($order->is_cancellable)
                        <form method="POST" action="{{ route('account.orders.cancel', $order->order_number) }}" onsubmit="return confirm('Bạn có chắc chắn muốn hủy đơn hàng này?')">
                            @csrf
                            <button type="submit" class="px-5 py-2.5 border border-[#E84444] text-[#E84444] hover:bg-[#E84444] hover:text-white text-xs font-semibold uppercase tracking-wider transition-colors cursor-pointer">
                                Hủy Đơn Hàng
                            </button>
                        </form>
                    @endif
                </div>

            </div>

        </div>
    </div>
</div>
@endsection
