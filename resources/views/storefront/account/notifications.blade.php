@extends("layouts.storefront")

@pushonce("page_title")Cài Đặt Thông Báo — @endpushonce
@pushonce("meta_description")Quản lý tùy chọn nhận thông báo qua email và SMS.@endpushonce

@section("content")
<div class="py-10 md:py-14 bg-[#FAFAFA]">
    <div class="section-wrapper">
        <div class="max-w-2xl mx-auto">
            {{-- ── Header ── --}}
            <div class="mb-8">
                <a href="{{ route("account.profile") }}" class="text-xs uppercase tracking-wider text-muted-text hover:text-primary-dark font-medium link-underline inline-block mb-4">
                    ← Quay lại hồ sơ
                </a>
                <h1 class="text-2xl font-light text-primary-dark tracking-wide uppercase">Cài Đặt Thông Báo</h1>
                <p class="text-sm text-muted-text font-light mt-2">
                    Chọn loại thông báo bạn muốn nhận qua email và SMS.
                </p>
            </div>

            {{-- Flash Messages --}}
            @if(session("success"))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs mb-6">
                    {{ session("success") }}
                </div>
            @endif

            <form method="POST" action="{{ route("account.notifications.update") }}" class="space-y-6">
                @csrf
                @method("PUT")

                {{-- Email Notifications --}}
                <div class="bg-white border border-border-subtle p-6 shadow-sm">
                    <h3 class="text-xs font-semibold tracking-[0.2em] uppercase text-primary-dark mb-6 pb-3 border-b border-border-subtle flex items-center gap-2">
                        <svg class="w-5 h-5 text-primary-dark" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        Thông Báo Qua Email
                    </h3>

                    <div class="space-y-4 mt-4">
                        {{-- Order Updates --}}
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <input type="checkbox" name="order_updates" id="order_updates" value="1" {{ $preferences["order_updates"] ?? true ? "checked" : "" }} class="h-4 w-4 accent-[#1a1a1a] cursor-pointer">
                                <div>
                                    <label for="order_updates" class="text-sm font-medium text-primary-dark cursor-pointer">Cập nhật đơn hàng</label>
                                    <p class="text-xs text-muted-text font-light">Xác nhận đơn hàng, vận chuyển, giao hàng thành công</p>
                                </div>
                            </div>
                        </div>

                        {{-- Promotions --}}
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <input type="checkbox" name="promotions" id="promotions" value="1" {{ $preferences["promotions"] ?? true ? "checked" : "" }} class="h-4 w-4 accent-[#1a1a1a] cursor-pointer">
                                <div>
                                    <label for="promotions" class="text-sm font-medium text-primary-dark cursor-pointer">Khuyến mãi & Ưu đãi</label>
                                    <p class="text-xs text-muted-text font-light">Mã giảm giá, flash sale, sản phẩm mới</p>
                                </div>
                            </div>
                        </div>

                        {{-- Security Alerts --}}
                        <div class="flex items-center justify-between opacity-75">
                            <div class="flex items-center gap-3">
                                <input type="checkbox" name="security_alerts" id="security_alerts" value="1" checked disabled class="h-4 w-4 accent-[#1a1a1a] cursor-not-allowed">
                                <div>
                                    <label for="security_alerts" class="text-sm font-medium text-primary-dark cursor-pointer">Cảnh báo bảo mật</label>
                                    <p class="text-xs text-muted-text font-light">Đăng nhập mới, đổi mật khẩu, bật/tắt 2FA (không thể tắt)</p>
                                </div>
                            </div>
                            <span class="text-[10px] uppercase font-bold tracking-wider px-2 py-0.5 bg-amber-50 text-amber-700 border border-amber-200">Bắt buộc</span>
                        </div>

                        {{-- Newsletter --}}
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <input type="checkbox" name="newsletter" id="newsletter" value="1" {{ $preferences["newsletter"] ?? false ? "checked" : "" }} class="h-4 w-4 accent-[#1a1a1a] cursor-pointer">
                                <div>
                                    <label for="newsletter" class="text-sm font-medium text-primary-dark cursor-pointer">Bản tin (Newsletter)</label>
                                    <p class="text-xs text-muted-text font-light">Thông tin sản phẩm mới, bài viết blog, tips nội thất</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- SMS Notifications --}}
                <div class="bg-white border border-border-subtle p-6 shadow-sm">
                    <h3 class="text-xs font-semibold tracking-[0.2em] uppercase text-primary-dark mb-6 pb-3 border-b border-border-subtle flex items-center gap-2">
                        <svg class="w-5 h-5 text-primary-dark" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 21a1 1 0 01-1-1v-1a2 2 0 00-2-2H8a2 2 0 00-2 2v1a1 1 0 01-1 1m14-6V6a1 1 0 00-1-1H4a1 1 0 00-1 1v9m14-9h4m-4 0a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V8a2 2 0 012-2h4" />
                        </svg>
                        Thông Báo Qua SMS
                    </h3>

                    <div class="space-y-4 mt-4">
                        <p class="text-xs text-muted-text font-light mb-4">Cần số điện thoại đã xác thực. Phí SMS áp dụng theo nhà mạng.</p>

                        {{-- SMS Order Updates --}}
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <input type="checkbox" name="sms_order_updates" id="sms_order_updates" value="1" {{ $preferences["sms_order_updates"] ?? false ? "checked" : "" }} class="h-4 w-4 accent-[#1a1a1a] cursor-pointer">
                                <div>
                                    <label for="sms_order_updates" class="text-sm font-medium text-primary-dark cursor-pointer">Cập nhật đơn hàng qua SMS</label>
                                    <p class="text-xs text-muted-text font-light">Xác nhận đơn hàng, mã OTP giao hàng, giao thành công</p>
                                </div>
                            </div>
                        </div>

                        {{-- SMS Promotions --}}
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <input type="checkbox" name="sms_promotions" id="sms_promotions" value="1" {{ $preferences["sms_promotions"] ?? false ? "checked" : "" }} class="h-4 w-4 accent-[#1a1a1a] cursor-pointer">
                                <div>
                                    <label for="sms_promotions" class="text-sm font-medium text-primary-dark cursor-pointer">Khuyến mãi qua SMS</label>
                                    <p class="text-xs text-muted-text font-light">Mã giảm giá, flash sale thông qua tin nhắn</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn-dark w-full sm:w-auto">Lưu Cài Đặt</button>
            </form>
        </div>
    </div>
</div>
@endsection
