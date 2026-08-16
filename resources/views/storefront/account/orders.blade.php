@extends('layouts.storefront')

@pushonce('page_title')Đơn Hàng Của Tôi — @endpushonce
@pushonce('meta_description')Xem lịch sử đơn hàng và trạng thái giao hàng của bạn tại MYSHOP.@endpushonce

@section('content')
<div class="py-12">
    <div class="section-wrapper">

        {{-- Account header --}}
        <div class="flex items-baseline justify-between mb-10">
            <div>
                <h1 class="text-2xl font-medium tracking-wide">Tài Khoản</h1>
                <p class="text-sm text-[#888888] font-light mt-1">
                    Xin chào, <span class="text-[#1a1a1a]">{{ auth('customer')->user()->name }}</span>
                </p>
            </div>
            <form method="POST" action="{{ route('account.logout') }}">
                @csrf
                <button type="submit"
                        class="text-[10px] tracking-[0.15em] uppercase text-[#888888] hover:text-[#1a1a1a] transition-colors">
                    Đăng Xuất
                </button>
            </form>
        </div>

        {{-- Section title --}}
        <div class="mb-6 pb-4 border-b border-[#E5E5E5]">
            <h2 class="text-[11px] font-medium tracking-[0.2em] uppercase">Lịch Sử Đơn Hàng</h2>
        </div>

        @if($orders->count() > 0)
            <ul role="list" class="divide-y divide-[#E5E5E5]" aria-label="Danh sách đơn hàng">
                @php
                    $statusLabels = [
                        'pending'   => 'Đang xử lý',
                        'confirmed' => 'Đã xác nhận',
                        'shipping'  => 'Đang giao',
                        'delivered' => 'Đã giao',
                        'cancelled' => 'Đã huỷ',
                    ];
                @endphp
                @foreach($orders as $order)
                    <li>
                        <a href="{{ route('track-order.index', ['order_number' => $order->order_number]) }}"
                           class="block py-5 hover:opacity-70 transition-opacity group">
                            <div class="flex items-start sm:items-center justify-between gap-4">

                                {{-- Order info --}}
                                <div>
                                    <p class="text-sm font-medium mb-1">{{ $order->order_number }}</p>
                                    <p class="text-xs text-[#888888] font-light">
                                        {{ $order->items->count() }} sản phẩm &middot;
                                        <time datetime="{{ $order->created_at->format('Y-m-d') }}">
                                            {{ $order->created_at->format('d/m/Y') }}
                                        </time>
                                    </p>
                                </div>

                                {{-- Status + amount --}}
                                <div class="flex items-center gap-5 shrink-0">
                                    <span class="text-[9px] font-medium tracking-[0.15em] uppercase px-2.5 py-1 border
                                        @if($order->status == 'pending') border-yellow-400 text-yellow-700
                                        @elseif($order->status == 'confirmed') border-[#1a1a1a] text-[#1a1a1a]
                                        @elseif($order->status == 'shipping') border-[#1a1a1a] text-[#1a1a1a]
                                        @elseif($order->status == 'delivered') border-green-600 text-green-700
                                        @else border-[#E84444] text-[#E84444] @endif
                                    ">
                                        {{ $statusLabels[$order->status] ?? $order->status }}
                                    </span>
                                    <p class="text-sm font-medium">
                                        {{ number_format($order->total_amount, 0, ',', '.') }}₫
                                    </p>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-[#888888] group-hover:text-[#1a1a1a] transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                                    </svg>
                                </div>
                            </div>
                        </a>
                    </li>
                @endforeach
            </ul>

            {{-- Pagination --}}
            @if($orders->hasPages())
                <div class="mt-10">
                    {{ $orders->links() }}
                </div>
            @endif

        @else
            <div class="text-center py-20">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 text-[#d0c8c0] mx-auto mb-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
                <p class="text-sm text-[#888888] mb-6">Bạn chưa có đơn hàng nào.</p>
                <a href="{{ route('products.index') }}" class="link-underline text-[#1a1a1a]">
                    Mua sắm ngay
                </a>
            </div>
        @endif

    </div>
</div>
@endsection
