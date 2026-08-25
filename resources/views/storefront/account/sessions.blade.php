@extends("layouts.storefront")

@pushonce("page_title")Phiên Đăng Nhập — @endpushonce
@pushonce("meta_description")Quản lý các phiên đăng nhập đang hoạt động của tài khoản.@endpushonce

@section("content")
<div class="py-10 md:py-14 bg-[#FAFAFA]">
    <div class="section-wrapper">
        <div class="max-w-2xl mx-auto">
            {{-- ── Header ── --}}
            <div class="mb-8">
                <a href="{{ route("account.profile") }}" class="text-xs uppercase tracking-wider text-muted-text hover:text-primary-dark font-medium link-underline inline-block mb-4">
                    ← Quay lại hồ sơ
                </a>
                <h1 class="text-2xl font-light text-primary-dark tracking-wide uppercase">Phiên Đăng Nhập</h1>
                <p class="text-sm text-muted-text font-light mt-2">
                    Quản lý các thiết bị đang đăng nhập vào tài khoản của bạn.
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

            {{-- Sessions List --}}
            <div class="bg-white border border-border-subtle shadow-sm">
                @if($sessions->count() > 0)
                    <div class="divide-y divide-border-subtle">
                        @foreach($sessions as $session)
                            <div class="p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 bg-surface-bg border border-border-subtle rounded-lg flex items-center justify-center shrink-0">
                                        <svg class="w-6 h-6 text-muted-text" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-medium text-primary-dark">{{ $session["device"] }}</p>
                                        <p class="text-xs text-muted-text font-light">{{ $session["ip"] }} &middot; Hoạt động: {{ \Carbon\Carbon::parse($session["last_active"])->diffForHumans() }}</p>
                                        @if($session["is_current"])
                                            <span class="text-[10px] uppercase font-bold tracking-wider px-2 py-0.5 bg-blue-50 text-blue-700 border border-blue-200 inline-block mt-1">Phiên hiện tại</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="flex items-center gap-2 shrink-0">
                                    @if(!$session["is_current"])
                                        <form method="POST" action="{{ route("account.sessions.destroy", $session["id"]) }}">
                                            @csrf
                                            @method("DELETE")
                                            <button type="submit" class="text-[10px] uppercase tracking-wider text-rose-600 hover:text-rose-800 font-medium link-underline px-2 py-1" title="Thu hồi phiên">
                                                Thu hồi
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-[10px] text-muted-text font-light">Đang sử dụng</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Revoke All Other Sessions --}}
                    @if($sessions->where("is_current", false)->count() > 0)
                        <div class="p-5 border-t border-border-subtle">
                            <form method="POST" action="{{ route("account.sessions.destroy-all") }}" onsubmit="return confirm(\"Bạn có chắc chắn muốn thu hồi tất cả phiên đăng nhập khác? Bạn sẽ bị đăng xuất trên các thiết bị khác.\")">
                                @csrf
                                @method("DELETE")
                                <button type="submit" class="text-xs uppercase tracking-wider text-rose-600 hover:text-rose-800 font-medium link-underline">
                                    Thu hồi tất cả phiên khác
                                </button>
                            </form>
                        </div>
                    @endif
                @else
                    <div class="text-center py-12">
                        <p class="text-sm text-muted-text font-light">Không có phiên đăng nhập nào.</p>
                    </div>
                @endif
            </div>

            {{-- Security Tips --}}
            <div class="mt-8 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <h4 class="text-xs font-semibold tracking-[0.15em] uppercase text-blue-800 mb-2">Mẹo bảo mật</h4>
                <ul class="text-xs text-blue-700 space-y-1">
                    <li class="flex items-start gap-2">• Thu hồi phiên nếu bạn nhận thấy thiết bị không quen thuộc</li>
                    <li class="flex items-start gap-2">• Luôn đăng xuất trên thiết bị công cộng/chung</li>
                    <li class="flex items-start gap-2">• Bật xác thực hai yếu tố (2FA) để bảo vệ tốt hơn</li>
                    <li class="flex items-start gap-2">• Thay đổi mật khẩu nếu nghi ngờ tài khoản bị xâm phạm</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
