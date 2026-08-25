@extends("layouts.storefront")

@pushonce("page_title")Chương Trình Giới Thiệu — @endpushonce
@pushonce("meta_description")Giới thiệu bạn bè nhận thưởng, tích điểm thưởng từ đơn hàng.@endpushonce

@section("content")
<div class="py-10 md:py-14 bg-[#FAFAFA]">
    <div class="section-wrapper">
        <div class="max-w-2xl mx-auto">
            {{-- ── Header ── --}}
            <div class="mb-8">
                <a href="{{ route("account.profile") }}" class="text-xs uppercase tracking-wider text-[#888888] hover:text-[#23232C] font-medium link-underline inline-block mb-4">
                    ← Quay lại hồ sơ
                </a>
                <h1 class="text-2xl font-light text-[#23232C] tracking-wide uppercase">Chương Trình Giới Thiệu & Điểm Thưởng</h1>
                <p class="text-sm text-[#888888] font-light mt-2">
                    Chia sẻ mã giới thiệu để nhận điểm thưởng, đổi điểm lấy voucher giảm giá.
                </p>
            </div>

            {{-- Flash Messages --}}
            @if(session("success"))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs mb-6">
                    {{ session("success") }}
                </div>
            @endif

            {{-- Stats Cards --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">
                <div class="bg-white border border-[#E5E5E5] p-4 text-center">
                    <p class="text-[10px] uppercase tracking-wider text-[#888888] font-medium mb-1">Đã Giới Thiệu</p>
                    <p class="text-2xl font-bold text-[#23232C]">{{ $stats["total_referrals"] }}</p>
                </div>
                <div class="bg-white border border-[#E5E5E5] p-4 text-center">
                    <p class="text-[10px] uppercase tracking-wider text-[#888888] font-medium mb-1">Hoàn Tất Đơn</p>
                    <p class="text-2xl font-bold text-emerald-700">{{ $stats["completed_referrals"] }}</p>
                </div>
                <div class="bg-white border border-[#E5E5E5] p-4 text-center">
                    <p class="text-[10px] uppercase tracking-wider text-[#888888] font-medium mb-1">Điểm Thưởng</p>
                    <p class="text-2xl font-bold text-amber-600">{{ number_format($stats["loyalty_points"]) }}đ</p>
                </div>
                <div class="bg-white border border-[#E5E5E5] p-4 text-center">
                    <p class="text-[10px] uppercase tracking-wider text-[#888888] font-medium mb-1">Mã Của Bạn</p>
                    <p class="text-xl font-bold font-mono text-[#23232C]">{{ $stats["referral_code"] }}</p>
                </div>
            </div>

            {{-- Referral Code & Share --}}
            <div class="bg-white border border-[#E5E5E5] p-6 shadow-sm mb-8">
                <h3 class="text-xs font-semibold tracking-[0.2em] uppercase text-[#23232C] mb-4 pb-3 border-b border-[#E5E5E5]">Mã Giới Thiệu Của Bạn</h3>

                <div class="flex flex-col sm:flex-row items-center justify-between gap-4 mb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-16 h-16 bg-amber-50 border border-amber-200 rounded-lg flex items-center justify-center shrink-0">
                            <svg class="w-8 h-8 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-[#888888] font-light">Mã giới thiệu</p>
                            <p class="font-mono text-xl font-bold text-[#23232C]">{{ $stats["referral_code"] }}</p>
                        </div>
                    </div>
                    <a href="{{ route("account.referrals.share") }}" class="btn-dark text-xs uppercase tracking-wider whitespace-nowrap">Chia Sẻ</a>
                </div>

                <div class="bg-[#F9F9F9] border border-[#E5E5E5] p-4 rounded-lg">
                    <p class="text-xs text-[#888888] font-light mb-2">Link giới thiệu của bạn:</p>
                    <div class="flex gap-2">
                        <input type="text" value="{{ $stats["referral_url"] }}" readonly class="input-underline flex-1 font-mono text-sm bg-white" onclick="this.select()">
                        <button type="button" onclick="navigator.clipboard.writeText(this.previousElementSibling.value); this.textContent = \"Đã sao chép!\"; setTimeout(() => this.textContent = \"Sao chép\", 2000)" class="btn-dark whitespace-nowrap">Sao chép</button>
                    </div>
                </div>
            </div>

            {{-- How It Works --}}
            <div class="bg-white border border-[#E5E5E5] p-6 shadow-sm mb-8">
                <h3 class="text-xs font-semibold tracking-[0.2em] uppercase text-[#23232C] mb-4">Cách Thức Hoạt Động</h3>
                <div class="grid sm:grid-cols-3 gap-6 text-center">
                    <div class="p-4">
                        <div class="w-12 h-12 bg-blue-50 border border-blue-200 rounded-lg flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
                            </svg>
                        </div>
                        <h4 class="font-medium text-[#23232C] mb-1">Chia Sẻ Mã</h4>
                        <p class="text-xs text-[#888888]">Gửi mã hoặc link cho bạn bè</p>
                    </div>
                    <div class="p-4">
                        <div class="w-12 h-12 bg-emerald-50 border border-emerald-200 rounded-lg flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                        </div>
                        <h4 class="font-medium text-[#23232C] mb-1">Bạn Bè Mua Hàng</h4>
                        <p class="text-xs text-[#888888">Nhập mã khi đăng ký/đặt hàng</p>
                    </div>
                    <div class="p-4">
                        <div class="w-12 h-12 bg-amber-50 border border-amber-200 rounded-lg flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h4 class="font-medium text-[#23232C] mb-1">Nhận Thưởng</h4>
                        <p class="text-xs text-[#888888">50.000đ điểm cho mỗi giới thiệu thành công</p>
                    </div>
                </div>
            </div>

            {{-- Loyalty Points --}}
            <div class="bg-white border border-[#E5E5E5] p-6 shadow-sm mb-8">
                <h3 class="text-xs font-semibold tracking-[0.2em] uppercase text-[#23232C] mb-4">Điểm Thưởng & Đổi Voucher</h3>
                <div class="grid sm:grid-cols-2 gap-6">
                    <div>
                        <h4 class="font-medium text-[#23232C] mb-2">Tích Điểm</h4>
                        <ul class="text-sm text-[#888888] space-y-1">
                            <li class="flex items-center gap-2">✓ 1 điểm / 1.000đ chi tiêu</li>
                            <li class="flex items-center gap-2">✓ 50.000 điểm / giới thiệu thành công</li>
                            <li class="flex items-center gap-2">✓ Điểm không hết hạn</li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="font-medium text-[#23232C] mb-2">Đổi Voucher</h4>
                        <ul class="text-sm text-[#888888] space-y-1">
                            <li class="flex items-center gap-2">10.000 điểm = 10.000đ voucher</li>
                            <li class="flex items-center gap-2">50.000 điểm = 55.000đ voucher (tiết kiệm 5.000đ)</li>
                            <li class="flex items-center gap-2">100.000 điểm = 115.000đ voucher (tiết kiệm 15.000đ)</li>
                        </ul>
                    </div>
                </div>
            </div>

            {{-- Referral History --}}
            <div class="bg-white border border-[#E5E5E5] p-6 shadow-sm">
                <h3 class="text-xs font-semibold tracking-[0.2em] uppercase text-[#23232C] mb-4 pb-3 border-b border-[#E5E5E5]">Lịch Sử Giới Thiệu</h3>

                @if($referrals->count() > 0)
                    <div class="space-y-4">
                        @foreach($referrals as $referral)
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-4 bg-[#F9F9F9] border border-[#E5E5E5] rounded-lg">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-[#F0F0F0] border border-[#E5E5E5] rounded-full flex items-center justify-center shrink-0">
                                        <span class="text-sm font-medium text-[#888888]">{{ Str::upper($referral->name[0]) }}</span>
                                    </div>
                                    <div>
                                        <p class="font-medium text-[#23232C]">{{ $referral->name }}</p>
                                        <p class="text-xs text-[#888888]">{{ $referral->email }}</p>
                                    </div>
                                </div>

                                <div class="flex items-center gap-4 text-sm">
                                    <span class="{{ $referral->orders_count > 0 ? "text-emerald-700" : "text-[#888888]" }} font-medium">
                                        {{ $referral->orders_count > 0 ? "Đã đặt hàng (" . $referral->orders_count . " đơn)" : "Chưa đặt hàng" }}
                                    </span>
                                    <span class="text-xs text-[#888888]">{{ $referral->created_at->format("d/m/Y") }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Pagination --}}
                    @if($referrals->hasPages())
                        <div class="mt-6 flex justify-center">
                            {{ $referrals->links() }}
                        </div>
                    @endif
                @else
                    <div class="text-center py-12">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 text-[#888888] mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM1 10h22v2H1v-2z" />
                        </svg>
                        <p class="text-sm font-medium text-[#23232C] mb-2">Chưa có giới thiệu nào</p>
                        <p class="text-xs text-[#888888] font-light mb-6">Hãy chia sẻ mã giới thiệu để bắt đầu kiếm thưởng.</p>
                        <a href="{{ route("account.referrals.share") }}" class="inline-block px-6 py-2.5 bg-[#23232C] text-white hover:bg-black text-xs font-semibold uppercase tracking-wider transition-colors">
                            Chia Sẻ Ngay
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
