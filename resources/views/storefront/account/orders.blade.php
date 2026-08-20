@extends('layouts.storefront')

@pushonce('page_title')Đơn Hàng Của Tôi — @endpushonce
@pushonce('meta_description')Xem lịch sử đơn hàng và trạng thái giao hàng của bạn tại {{ config('app.name', 'Sober Furniture') }}.@endpushonce

@section('content')
<div class="py-10 md:py-14 bg-[#FAFAFA]">
    <div class="section-wrapper">

        {{-- ── Account Header ── --}}
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-8 pb-6 border-b border-[#E5E5E5]">
            <div>
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl font-light text-[#23232C] tracking-wide uppercase">Tài Khoản</h1>
                    <span class="text-[10px] uppercase font-bold tracking-wider px-2.5 py-1 {{ $membershipTierBadge }}">
                        {{ $membershipTier }}
                    </span>
                </div>
                <p class="text-xs text-[#888888] font-light mt-1">
                    Xin chào, <span class="text-[#23232C] font-semibold">{{ $customer->name }}</span> ({{ $customer->email }})
                </p>
            </div>
            
            <div class="flex items-center gap-4">
                <a href="{{ route('account.wishlist') }}" class="text-xs uppercase tracking-wider text-[#23232C] hover:text-[#E84444] font-medium link-underline">
                    Danh Sách Yêu Thích
                </a>
                <form method="POST" action="{{ route('account.logout') }}">
                    @csrf
                    <button type="submit"
                            class="text-xs tracking-wider uppercase text-[#888888] hover:text-[#E84444] transition-colors cursor-pointer">
                        Đăng Xuất
                    </button>
                </form>
            </div>
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

        {{-- ── Statistics Cards ── --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-10">
            <div class="bg-white border border-[#E5E5E5] p-4 text-center">
                <p class="text-[10px] uppercase tracking-wider text-[#888888] font-medium mb-1">Tổng Đơn Hàng</p>
                <p class="text-xl font-bold text-[#23232C]">{{ $totalOrdersCount }}</p>
            </div>
            <div class="bg-white border border-[#E5E5E5] p-4 text-center">
                <p class="text-[10px] uppercase tracking-wider text-[#888888] font-medium mb-1">Đang Giao</p>
                <p class="text-xl font-bold text-purple-700">{{ $deliveringCount }}</p>
            </div>
            <div class="bg-white border border-[#E5E5E5] p-4 text-center">
                <p class="text-[10px] uppercase tracking-wider text-[#888888] font-medium mb-1">Hoàn Tất</p>
                <p class="text-xl font-bold text-emerald-700">{{ $completedCount }}</p>
            </div>
            <div class="bg-white border border-[#E5E5E5] p-4 text-center">
                <p class="text-[10px] uppercase tracking-wider text-[#888888] font-medium mb-1">Tổng Chi Tiêu</p>
                <p class="text-xl font-bold text-[#E84444]">{{ $totalSpentFormatted }}</p>
            </div>
        </div>

        {{-- ── Section Header + Filter Tabs ── --}}
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6 pb-4 border-b border-[#E5E5E5]">
            <h2 class="text-xs font-semibold tracking-[0.2em] uppercase text-[#23232C]">Lịch Sử Đơn Hàng</h2>
            
            <div class="flex flex-wrap gap-2 text-xs">
                <a href="{{ route('account.orders') }}"
                   class="px-3 py-1.5 border transition-colors {{ $statusTab === 'all' ? 'bg-[#23232C] text-white border-[#23232C]' : 'bg-white text-[#888888] border-[#E5E5E5] hover:border-[#23232C]' }}">
                    Tất cả
                </a>
                <a href="{{ route('account.orders', ['status' => 'processing']) }}"
                   class="px-3 py-1.5 border transition-colors {{ $statusTab === 'processing' ? 'bg-[#23232C] text-white border-[#23232C]' : 'bg-white text-[#888888] border-[#E5E5E5] hover:border-[#23232C]' }}">
                    Đang xử lý
                </a>
                <a href="{{ route('account.orders', ['status' => 'shipped']) }}"
                   class="px-3 py-1.5 border transition-colors {{ $statusTab === 'shipped' ? 'bg-[#23232C] text-white border-[#23232C]' : 'bg-white text-[#888888] border-[#E5E5E5] hover:border-[#23232C]' }}">
                    Đang giao
                </a>
                <a href="{{ route('account.orders', ['status' => 'delivered']) }}"
                   class="px-3 py-1.5 border transition-colors {{ $statusTab === 'delivered' ? 'bg-[#23232C] text-white border-[#23232C]' : 'bg-white text-[#888888] border-[#E5E5E5] hover:border-[#23232C]' }}">
                    Hoàn tất
                </a>
                <a href="{{ route('account.orders', ['status' => 'cancelled']) }}"
                   class="px-3 py-1.5 border transition-colors {{ $statusTab === 'cancelled' ? 'bg-[#23232C] text-white border-[#23232C]' : 'bg-white text-[#888888] border-[#E5E5E5] hover:border-[#23232C]' }}">
                    Đã hủy
                </a>
            </div>
        </div>

        {{-- ── Orders List ── --}}
        @if($orders->count() > 0)
            <div class="space-y-4" role="list" aria-label="Danh sách đơn hàng">
                @foreach($orders as $order)
                    <div class="bg-white border border-[#E5E5E5] p-5 shadow-sm hover:border-[#23232C] transition-colors">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-[#F0F0F0]">
                            <div>
                                <div class="flex items-center gap-3">
                                    <a href="{{ route('account.orders.show', $order->order_number) }}" class="text-sm font-bold text-[#23232C] hover:text-[#E84444]">
                                        {{ $order->order_number }}
                                    </a>
                                    <span class="text-[10px] font-semibold px-2 py-0.5 {{ $order->status_badge_classes }}">
                                        {{ $order->status_label }}
                                    </span>
                                </div>
                                <p class="text-xs text-[#888888] font-light mt-1">
                                    {{ $order->items->count() }} sản phẩm &middot;
                                    <time datetime="{{ $order->created_at->format('Y-m-d') }}">
                                        {{ $order->created_at->format('d/m/Y H:i') }}
                                    </time>
                                </p>
                            </div>

                            <div class="text-left sm:text-right">
                                <p class="text-xs text-[#888888]">Tổng tiền</p>
                                <p class="text-sm font-bold text-[#E84444]">{{ $order->formatted_total_amount }}</p>
                            </div>
                        </div>

                        {{-- Order Line Items Preview --}}
                        <div class="py-3 divide-y divide-[#F7F7F7]">
                            @foreach($order->items->take(2) as $item)
                                <div class="py-2 flex items-center justify-between gap-4 text-xs">
                                    <div class="flex items-center gap-3 truncate">
                                        <div class="w-10 h-10 bg-[#F9F9F9] border border-[#EBEBEB] shrink-0 overflow-hidden flex items-center justify-center">
                                            @if($item->thumbnail_url)
                                                <img src="{{ $item->thumbnail_url }}" alt="{{ $item->product_name }}" class="w-full h-full object-contain p-0.5">
                                            @else
                                                <span class="text-[8px] text-[#888888]">No img</span>
                                            @endif
                                        </div>
                                        <div class="truncate">
                                            <p class="font-medium text-[#23232C] truncate">{{ $item->product_name }}</p>
                                            <p class="text-[11px] text-[#888888]">SL: {{ $item->quantity }} × {{ $item->formatted_price }}</p>
                                        </div>
                                    </div>
                                    <span class="font-semibold text-[#23232C] shrink-0">{{ $item->formatted_subtotal }}</span>
                                </div>
                            @endforeach
                            @if($order->items->count() > 2)
                                <p class="text-[11px] text-[#888888] italic pt-1">+ và {{ $order->items->count() - 2 }} sản phẩm khác</p>
                            @endif
                        </div>

                        {{-- Actions Bar --}}
                        <div class="pt-3 border-t border-[#F0F0F0] flex flex-wrap items-center justify-between gap-3 text-xs">
                            <a href="{{ route('account.orders.show', $order->order_number) }}" class="font-semibold text-[#23232C] hover:text-[#E84444] link-underline">
                                Xem chi tiết đơn hàng &rarr;
                            </a>

                            <div class="flex items-center gap-2">
                                <form method="POST" action="{{ route('account.orders.reorder', $order->order_number) }}">
                                    @csrf
                                    <button type="submit" class="px-3 py-1.5 bg-[#23232C] text-white hover:bg-black text-[11px] font-semibold uppercase tracking-wider transition-colors cursor-pointer">
                                        Mua Lại
                                    </button>
                                </form>

                                @if($order->is_cancellable)
                                    <form method="POST" action="{{ route('account.orders.cancel', $order->order_number) }}" onsubmit="return confirm('Bạn có chắc chắn muốn hủy đơn hàng này?')">
                                        @csrf
                                        <button type="submit" class="px-3 py-1.5 border border-[#E84444] text-[#E84444] hover:bg-[#E84444] hover:text-white text-[11px] font-semibold uppercase tracking-wider transition-colors cursor-pointer">
                                            Hủy Đơn
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            @if($orders->hasPages())
                <div class="mt-10 flex justify-center">
                    {{ $orders->links() }}
                </div>
            @endif

        @else
            <div class="text-center py-20 bg-white border border-[#E5E5E5] p-8 shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 text-[#888888] mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
                <p class="text-sm font-medium text-[#23232C] mb-2">Bạn chưa có đơn hàng nào.</p>
                <p class="text-xs text-[#888888] font-light mb-6">Hãy khám phá bộ sưu tập sản phẩm tuyệt vời của chúng tôi.</p>
                <a href="{{ route('products.index') }}" class="inline-block px-6 py-2.5 bg-[#23232C] text-white hover:bg-black text-xs font-semibold uppercase tracking-wider transition-colors">
                    Mua sắm ngay
                </a>
            </div>
        @endif

    </div>
</div>
@endsection
