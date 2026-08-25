@extends('layouts.storefront')

@pushonce('page_title')Tra Cứu Đơn Hàng — @endpushonce
@pushonce('meta_description')Tra cứu trạng thái đơn hàng tại {{ config('app.name', 'Sober Furniture') }} theo mã đơn hàng và số điện thoại/email.@endpushonce

@section('content')
<div class="py-12 md:py-16 bg-[#FAFAFA]">
    <div class="section-wrapper">
        <div class="max-w-3xl mx-auto">

            {{-- ── Heading ── --}}
            <div class="text-center mb-10">
                <h1 class="text-2xl md:text-3xl font-light text-primary-dark tracking-wide uppercase mb-2">Tra Cứu Đơn Hàng</h1>
                <p class="text-xs md:text-sm text-muted-text font-light">Nhập mã đơn hàng và thông tin liên hệ để kiểm tra trạng thái giao hàng.</p>
            </div>

            {{-- ── Search Form Card ── --}}
            <div class="bg-white border border-border-subtle p-6 md:p-8 mb-10 shadow-sm">
                <form action="{{ route('track-order.track') }}" method="POST">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label for="order_number" class="block text-[10px] uppercase tracking-wider font-semibold text-muted-text mb-1.5">Mã đơn hàng *</label>
                            <input type="text"
                                   id="order_number"
                                   name="order_number"
                                   value="{{ old('order_number', $order_number) }}"
                                   placeholder="VD: ORD-20260820-XXXXX"
                                   required
                                   class="w-full border border-border-subtle bg-[#F9F9F9] px-3.5 py-2.5 text-xs text-primary-dark outline-none focus:border-primary-dark transition-colors"
                                   aria-label="Mã đơn hàng">
                        </div>

                        <div>
                            <label for="contact_info" class="block text-[10px] uppercase tracking-wider font-semibold text-muted-text mb-1.5">Email hoặc Số điện thoại *</label>
                            <input type="text"
                                   id="contact_info"
                                   name="contact_info"
                                   value="{{ old('contact_info', request('contact_info')) }}"
                                   placeholder="Email hoặc SĐT đặt hàng"
                                   required
                                   class="w-full border border-border-subtle bg-[#F9F9F9] px-3.5 py-2.5 text-xs text-primary-dark outline-none focus:border-primary-dark transition-colors"
                                   aria-label="Email hoặc Số điện thoại">
                        </div>
                    </div>

                    <button type="submit"
                            class="w-full sm:w-auto px-8 py-3 bg-primary-dark text-white hover:bg-black text-xs font-semibold uppercase tracking-wider transition-colors cursor-pointer">
                        Tra Cứu
                    </button>
                </form>
            </div>

            {{-- ── Results Section ── --}}
            @if($order_number)
                @if($order)
                    <div class="bg-white border border-border-subtle shadow-sm">
                        
                        {{-- Order Header --}}
                        <div class="p-6 bg-surface-light border-b border-border-subtle flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                            <div>
                                <p class="text-[10px] tracking-[0.15em] uppercase text-muted-text mb-0.5">Mã đơn hàng</p>
                                <h2 class="text-base sm:text-lg font-bold text-primary-dark">{{ $order->order_number }}</h2>
                                <p class="text-xs text-muted-text font-light mt-0.5">
                                    Đặt lúc: {{ $order->created_at->format('d/m/Y H:i') }}
                                </p>
                            </div>
                            <div>
                                <span class="text-xs font-semibold px-3 py-1.5 {{ $order->status_badge_classes }}">
                                    {{ $order->status_label }}
                                </span>
                            </div>
                        </div>

                        {{-- Delivery Stepper Progress (5 Steps) --}}
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
                            <div class="p-6 border-b border-border-subtle">
                                <div class="grid grid-cols-5 gap-2 text-center relative">
                                    @foreach($statusSteps as $idx => $step)
                                        <div class="flex flex-col items-center">
                                            <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold mb-1.5 transition-colors {{ $idx <= $currentIndex ? 'bg-primary-dark text-white' : 'bg-border-subtle text-muted-text' }}">
                                                {{ $idx + 1 }}
                                            </div>
                                            <span class="text-[10px] font-medium {{ $idx <= $currentIndex ? 'text-primary-dark font-semibold' : 'text-muted-text' }}">
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

                        {{-- Items List --}}
                        <div class="p-6">
                            <h3 class="text-[10px] font-semibold tracking-[0.2em] uppercase text-muted-text mb-4">Sản Phẩm</h3>
                            <ul class="divide-y divide-[#EBEBEB] mb-6" aria-label="Danh sách sản phẩm">
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

                            {{-- Summary Totals --}}
                            <div class="pt-4 border-t border-[#EBEBEB] space-y-1.5 text-xs">
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
                                <div class="border-t border-border-subtle pt-3 flex justify-between items-baseline">
                                    <span class="text-sm font-bold text-primary-dark">Tổng cộng</span>
                                    <span class="text-base font-bold text-badge-hot">{{ $order->formatted_total_amount }}</span>
                                </div>
                            </div>
                        </div>

                    </div>
                @else
                    <div class="text-center py-12 bg-white border border-border-subtle p-8 shadow-sm">
                        <p class="text-sm text-badge-hot mb-2 font-medium" role="alert">
                            Không tìm thấy đơn hàng với mã <strong>{{ $order_number }}</strong>.
                        </p>
                        <p class="text-xs text-muted-text font-light">Vui lòng kiểm tra lại mã đơn hàng cùng số điện thoại hoặc email đã đăng ký.</p>
                    </div>
                @endif
            @endif

        </div>
    </div>
</div>
@endsection
