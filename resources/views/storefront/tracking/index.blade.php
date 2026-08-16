@extends('layouts.storefront')

@pushonce('page_title')Tra Cứu Đơn Hàng — @endpushonce
@pushonce('meta_description')Tra cứu trạng thái đơn hàng tại MYSHOP theo mã đơn hàng.@endpushonce

@section('content')
<div class="py-16 md:py-20">
    <div class="section-wrapper">
        <div class="max-w-2xl mx-auto">

            {{-- Heading --}}
            <div class="text-center mb-10">
                <h1 class="text-2xl font-medium tracking-wide mb-3">Tra Cứu Đơn Hàng</h1>
                <p class="text-sm text-[#888888] font-light">Nhập mã đơn hàng để kiểm tra trạng thái giao hàng.</p>
            </div>

            {{-- Search form --}}
            <form action="{{ route('track-order.track') }}" method="POST"
                  class="flex items-center border-b border-[#1a1a1a] mb-12">
                @csrf
                <input type="text"
                       name="order_number"
                       value="{{ old('order_number', $order_number) }}"
                       placeholder="Nhập mã đơn hàng (VD: ORD-XXXXXXXXXXXX)"
                       required
                       class="flex-1 bg-transparent py-3 text-sm outline-none placeholder:text-[#888888] font-light"
                       aria-label="Mã đơn hàng">
                <button type="submit"
                        class="shrink-0 text-[10px] font-medium tracking-[0.2em] uppercase py-3 pl-6 hover:opacity-60 transition-opacity focus-visible:outline focus-visible:outline-2 focus-visible:outline-[#1a1a1a]">
                    Tra Cứu
                </button>
            </form>

            {{-- Results --}}
            @if($order_number)
                @if($order)
                    <div>
                        {{-- Order header --}}
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 pb-6 border-b border-[#E5E5E5]">
                            <div>
                                <p class="text-[10px] tracking-[0.15em] uppercase text-[#888888] mb-1">Mã đơn hàng</p>
                                <h2 class="text-lg font-medium">{{ $order->order_number }}</h2>
                                <p class="text-xs text-[#888888] font-light mt-1">
                                    Đặt lúc: {{ $order->created_at->format('d/m/Y H:i') }}
                                </p>
                            </div>
                            <div class="mt-4 sm:mt-0">
                                <span class="text-[10px] font-medium tracking-[0.15em] uppercase px-3 py-1.5 border
                                    @if($order->status == 'pending') border-yellow-500 text-yellow-700
                                    @elseif($order->status == 'confirmed') border-[#1a1a1a] text-[#1a1a1a]
                                    @elseif($order->status == 'shipping') border-[#1a1a1a] text-[#1a1a1a]
                                    @elseif($order->status == 'delivered') border-green-600 text-green-700
                                    @else border-[#E84444] text-[#E84444] @endif
                                ">
                                    @php
                                        $statusLabels = [
                                            'pending'   => 'Đang xử lý',
                                            'confirmed' => 'Đã xác nhận',
                                            'shipping'  => 'Đang giao',
                                            'delivered' => 'Đã giao',
                                            'cancelled' => 'Đã huỷ',
                                        ];
                                    @endphp
                                    {{ $statusLabels[$order->status] ?? $order->status }}
                                </span>
                            </div>
                        </div>

                        {{-- Items list --}}
                        <h3 class="text-[11px] font-medium tracking-[0.2em] uppercase mb-5">Sản Phẩm</h3>
                        <ul class="divide-y divide-[#E5E5E5] mb-8" aria-label="Danh sách sản phẩm">
                            @foreach($order->items as $item)
                                <li class="py-4 flex justify-between items-start">
                                    <div>
                                        <p class="text-sm font-light">{{ $item->product_name }}</p>
                                        @if($item->variant_name)
                                            <p class="text-xs text-[#888888] mt-0.5">{{ $item->variant_name }}</p>
                                        @endif
                                        <p class="text-xs text-[#888888] mt-0.5">Số lượng: {{ $item->quantity }}</p>
                                    </div>
                                    <p class="text-sm font-medium ml-4 shrink-0">
                                        {{ number_format($item->price_at_purchase * $item->quantity, 0, ',', '.') }}₫
                                    </p>
                                </li>
                            @endforeach
                        </ul>

                        {{-- Total --}}
                        <div class="border-t border-[#E5E5E5] pt-5 flex justify-between items-baseline">
                            <span class="text-sm font-medium">Tổng cộng</span>
                            <span class="text-lg font-medium">{{ number_format($order->total_amount, 0, ',', '.') }}₫</span>
                        </div>
                    </div>
                @else
                    <div class="text-center py-12">
                        <p class="text-sm text-[#E84444] mb-4" role="alert">
                            Không tìm thấy đơn hàng với mã <strong>{{ $order_number }}</strong>.
                        </p>
                        <p class="text-xs text-[#888888] font-light">Vui lòng kiểm tra lại mã đơn hàng và thử lại.</p>
                    </div>
                @endif
            @endif

        </div>
    </div>
</div>
@endsection
