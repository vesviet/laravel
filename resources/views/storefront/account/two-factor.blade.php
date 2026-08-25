@extends("layouts.storefront")

@pushonce("page_title")Xác Thực Hai Yếu Tố (2FA) — @endpushonce
@pushonce("meta_description")Quản lý cài đặt xác thực hai yếu tố cho tài khoản của bạn.@endpushonce

@section("content")
<div class="py-10 md:py-14 bg-[#FAFAFA]">
    <div class="section-wrapper">
        <div class="max-w-2xl mx-auto">
            {{-- ── Header ── --}}
            <div class="mb-8">
                <a href="{{ route("account.profile") }}" class="text-xs uppercase tracking-wider text-[#888888] hover:text-[#23232C] font-medium link-underline inline-block mb-4">
                    ← Quay lại hồ sơ
                </a>
                <h1 class="text-2xl font-light text-[#23232C] tracking-wide uppercase">Xác Thực Hai Yếu Tố (2FA)</h1>
                <p class="text-sm text-[#888888] font-light mt-2">
                    Tăng cường bảo mật tài khoản bằng mã xác thực từ ứng dụng Authenticator.
                </p>
            </div>

            {{-- Flash Messages --}}
            @if(session("success"))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs mb-6">
                    {{ session("success") }}
                </div>
            @endif
            @if(session("error"))
                <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 text-xs mb-6">
                    {{ session("error") }}
                </div>
            @endif

            @if(!$customer->two_factor_enabled)
                {{-- ── Enable 2FA ── --}}
                <div class="bg-white border border-[#E5E5E5] p-6 shadow-sm space-y-6">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-12 h-12 bg-blue-50 border border-blue-200 rounded-lg flex items-center justify-center shrink-0">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-medium text-[#23232C]">Bật xác thực hai yếu tố</h3>
                            <p class="text-sm text-[#888888] font-light">Thêm lớp bảo mật thứ hai cho tài khoản của bạn</p>
                        </div>
                    </div>

                    <div class="space-y-4 text-sm text-[#888888] leading-relaxed">
                        <p>1. Tải ứng dụng Authenticator: <a href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2" target="_blank" class="text-[#23232C] hover:underline">Google Authenticator</a> (Android) hoặc <a href="https://apps.apple.com/app/google-authenticator/id388497605" target="_blank" class="text-[#23232C] hover:underline">iOS</a>, <a href="https://authy.com/download/" target="_blank" class="text-[#23232C] hover:underline">Authy</a>, hoặc <a href="https://www.microsoft.com/en-us/account/authenticator" target="_blank" class="text-[#23232C] hover:underline">Microsoft Authenticator</a>.</p>
                        <p>2. Quét mã QR bên dưới bằng ứng dụng.</p>
                        <p>3. Nhập mã 6 chữ số từ ứng dụng để xác thực.</p>
                    </div>

                    {{-- QR Code --}}
                    @if($customer->two_factor_secret)
                        <div class="text-center py-4 border border-[#E5E5E5] rounded-lg bg-white">
                            <p class="text-xs text-[#888888] font-light mb-2">Quét mã QR này:</p>
                            <div class="inline-block p-4 bg-white">
                                {!! $customer->two_factor_service->generateQrCodeSvg($customer) !!}
                            </div>
                            <p class="text-xs text-[#888888] font-light mt-2">Hoặc nhập thủ công: <code class="font-mono text-sm bg-[#F5F5F5] px-2 py-1 rounded">{{ $customer->two_factor_secret }}</code></p>
                        </div>
                    @endif

                    <form method="POST" action="{{ route("account.two-factor.enable") }}" class="space-y-4">
                        @csrf

                        <div>
                            <label for="code" class="block text-[10px] tracking-[0.15em] uppercase text-[#888888] mb-2">Mã xác thực 6 chữ số <span class="text-[#E84444]">*</span></label>
                            <input type="text" name="code" id="code" maxlength="6" pattern="[0-9]*" inputmode="numeric" required autocomplete="one-time-code" class="input-underline w-full max-w-xs text-center tracking-widest text-xl @error("code") border-[#E84444] @enderror">
                            @error("code")
                                <span class="text-[#E84444] text-xs mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <button type="submit" class="btn-dark w-full sm:w-auto">Bật 2FA</button>
                    </form>
                </div>

            @else
                {{-- ── 2FA Enabled ── --}}
                <div class="bg-white border border-[#E5E5E5] p-6 shadow-sm mb-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-emerald-50 border border-emerald-200 rounded-lg flex items-center justify-center shrink-0">
                                <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-medium text-[#23232C]">2FA đã được bật</h3>
                                <p class="text-sm text-[#888888] font-light">Tài khoản của bạn được bảo vệ bởi xác thực hai yếu tố</p>
                            </div>
                        </div>
                        <span class="text-[10px] uppercase font-bold tracking-wider px-2.5 py-1 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded">ĐANG BẬT</span>
                    </div>

                    <div class="grid sm:grid-cols-2 gap-4 mb-6">
                        <div class="bg-[#F9F9F9] border border-[#E5E5E5] p-4 rounded-lg">
                            <p class="text-[10px] uppercase tracking-wider text-[#888888] font-medium mb-2">Mã khôi phục còn lại</p>
                            <p class="text-2xl font-bold text-[#23232C]">{{ count($customer->two_factor_recovery_codes ?? []) }}</p>
                            <p class="text-xs text-[#888888] font-light">Mỗi mã chỉ dùng được 1 lần</p>
                        </div>
                        <div class="bg-[#F9F9F9] border border-[#E5E5E5] p-4 rounded-lg">
                            <p class="text-[10px] uppercase tracking-wider text-[#888888] font-medium mb-2">Đã xác thực lúc</p>
                            <p class="text-sm font-medium text-[#23232C]">{{ $customer->two_factor_confirmed_at?->format("d/m/Y H:i") ?? "Chưa xác thực" }}</p>
                        </div>
                    </div>

                    {{-- Recovery Codes --}}
                    @if($customer->two_factor_recovery_codes && count($customer->two_factor_recovery_codes) > 0)
                        <div class="mb-6 p-4 bg-amber-50 border border-amber-200 rounded-lg">
                            <h4 class="text-xs font-semibold tracking-[0.15em] uppercase text-amber-800 mb-3">Mã khôi phục (giữ bí mật, mỗi mã dùng 1 lần):</h4>
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                                @foreach($customer->two_factor_recovery_codes as $code)
                                    <code class="font-mono text-sm bg-white border border-amber-200 px-3 py-2 rounded text-center">{{ $code }}</code>
                                @endforeach
                            </div>
                            <p class="text-xs text-amber-700 mt-3">Lưu trữ các mã này an toàn. Chúng giúp bạn đăng nhập khi mất thiết bị Authenticator.</p>
                        </div>
                    @endif

                    {{-- Actions --}}
                    <div class="flex flex-col sm:flex-row gap-4 pt-4 border-t border-[#E5E5E5]">
                        <form method="POST" action="{{ route("account.two-factor.regenerate-recovery-codes") }}">
                            @csrf
                            <button type="submit" class="border border-[#23232C] text-[#23232C] hover:bg-[#23232C] hover:text-white text-xs font-semibold uppercase tracking-wider px-6 py-2.5 transition-colors w-full sm:w-auto"
                                    onclick="return confirm(\"Tạo mã khôi phục mới sẽ vô hiệu hóa các mã cũ. Tiếp tục?\")">
                                Tạo lại mã khôi phục
                            </button>
                        </form>

                        <form method="POST" action="{{ route("account.two-factor.disable") }}">
                            @csrf
                            <button type="submit" class="border border-rose-400 text-rose-700 hover:bg-rose-400 hover:text-white text-xs font-semibold uppercase tracking-wider px-6 py-2.5 transition-colors w-full sm:w-auto"
                                    onclick="return confirm(\"Tắt 2FA sẽ làm giảm bảo mật tài khoản. Tiếp tục?\")">
                                Tắt 2FA
                            </button>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
