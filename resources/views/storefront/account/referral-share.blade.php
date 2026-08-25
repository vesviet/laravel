@extends("layouts.storefront")

@pushonce("page_title")Chia Sẻ Mã Giới Thiệu — @endpushonce
@pushonce("meta_description")Chia sẻ mã giới thiệu với bạn bè qua Facebook, Zalo, Messenger, Email.@endpushonce

@section("content")
<div class="py-10 md:py-14 bg-[#FAFAFA]">
    <div class="section-wrapper">
        <div class="max-w-md mx-auto text-center">
            {{-- ── Header ── --}}
            <div class="mb-8">
                <a href="{{ route("account.referrals") }}" class="text-xs uppercase tracking-wider text-muted-text hover:text-primary-dark font-medium link-underline inline-block mb-4">
                    ← Quay lại chương trình giới thiệu
                </a>
                <h1 class="text-2xl font-light text-primary-dark tracking-wide uppercase">Chia Sẻ Mã Giới Thiệu</h1>
                <p class="text-sm text-muted-text font-light mt-2">
                    Mời bạn bè mua sắm, bạn nhận 50.000đ điểm, bạn bè được ưu đãi.
                </p>
            </div>

            {{-- Referral Code Card --}}
            <div class="bg-white border border-border-subtle p-8 shadow-sm rounded-2xl mb-8">
                <div class="w-16 h-16 bg-amber-50 border border-amber-200 rounded-xl flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
                    </svg>
                </div>

                <p class="text-xs text-muted-text font-light mb-2">Mã giới thiệu của bạn</p>
                <p class="font-mono text-3xl font-bold text-primary-dark tracking-wider mb-4">{{ $customer->referral_code }}</p>

                <div class="bg-[#F9F9F9] border border-border-subtle p-4 rounded-lg mb-4">
                    <p class="text-xs text-muted-text font-light mb-2">Link giới thiệu</p>
                    <div class="flex gap-2">
                        <input type="text" id="referralLink" value="{{ $customer->referral_service->getReferralStats($customer)["referral_url"] }}" readonly class="input-underline flex-1 font-mono text-sm bg-white" onclick="this.select()">
                        <button type="button" onclick="navigator.clipboard.writeText(document.getElementById(\"referralLink\").value); this.textContent = \"Đã sao chép!\"; setTimeout(() => this.textContent = \"Sao chép\", 2000)" class="btn-dark whitespace-nowrap">Sao chép</button>
                    </div>
                </div>

                <p class="text-sm text-muted-text leading-relaxed">
                    Bạn bè nhập mã <span class="font-bold text-primary-dark">{{ $customer->referral_code }}</span> khi đăng ký hoặc đặt hàng đầu tiên.
                </p>
            </div>

            {{-- Share Buttons --}}
            <div class="bg-white border border-border-subtle p-6 shadow-sm rounded-2xl mb-8">
                <h3 class="text-xs font-semibold tracking-[0.2em] uppercase text-primary-dark mb-4 text-center">Chia Sẻ Nhanh</h3>
                <div class="grid grid-cols-2 gap-3">
                    @php
                        $shareUrl = urlencode($customer->referral_service->getReferralStats($customer)["referral_url"]);
                        $shareText = urlencode("Mời bạn mua sắm tại " . config("app.name", "Sober Furniture") . " với mã giảm giá " . $customer->referral_code . "! Bạn được ưu đãi, mình nhận điểm thưởng.");
                    @endphp

                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ $shareUrl }}&quote={{ $shareText }}" target="_blank" rel="noopener noreferrer" class="flex items-center justify-center gap-2 px-4 py-3 bg-blue-600 text-white hover:bg-blue-700 rounded-lg transition-colors text-sm font-medium">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.046V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                        </svg>
                        Facebook
                    </a>

                    <a href="https://zalo.me/share?url={{ $shareUrl }}&title={{ $shareText }}" target="_blank" rel="noopener noreferrer" class="flex items-center justify-center gap-2 px-4 py-3 bg-[#0068FF] text-white hover:bg-[#0056CC] rounded-lg transition-colors text-sm font-medium">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M20.5 3.5H3.5A2 2 0 001.5 5.5v13A2 2 0 003.5 20.5h17a2 2 0 002-2v-13a2 2 0 00-2-2zm-10 10.5v-5h-2v5H7v-7h3V6.5c0-1.5.8-2.5 2.5-2.5h2.5v3H13c-.8 0-1 .5-1 1v2.5h3.5l-.5 4h-3z"/>
                        </svg>
                        Zalo
                    </a>

                    <a href="fb-messenger://share?link={{ $shareUrl }}&app_id={{ config("services.facebook.app_id") }}" class="flex items-center justify-center gap-2 px-4 py-3 bg-[#0084FF] text-white hover:bg-[#0073E6] rounded-lg transition-colors text-sm font-medium">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M12 2C6.477 2 2 6.477 2 12c0 4.42 3.58 8 8 8s8-3.58 8-8c0-5.523-4.477-10-10-10zm0 1.5c3.86 0 7 3.14 7 7s-3.14 7-7 7-7-3.14-7-7 3.14-7 7-7zm0 12c-2.761 0-5-2.239-5-5s2.239-5 5-5 5 2.239 5 5-2.239 5-5 5zm-4-8.5c-.827 0-1.5-.673-1.5-1.5s.673-1.5 1.5-1.5 1.5.673 1.5 1.5-.673 1.5-1.5 1.5zm8 0c-.827 0-1.5-.673-1.5-1.5s.673-1.5 1.5-1.5 1.5.673 1.5 1.5-.673 1.5-1.5 1.5z"/>
                        </svg>
                        Messenger
                    </a>

                    <a href="mailto:?subject={{ $shareText }}&body={{ $shareUrl }}" class="flex items-center justify-center gap-2 px-4 py-3 bg-[#EA4335] text-white hover:bg-[#D93025] rounded-lg transition-colors text-sm font-medium">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        Email
                    </a>
                </div>
            </div>

            {{-- QR Code --}}
            <div class="bg-white border border-border-subtle p-6 shadow-sm rounded-2xl">
                <h3 class="text-xs font-semibold tracking-[0.2em] uppercase text-primary-dark mb-4 text-center">Quét Mã QR Để Chia Sẻ</h3>
                <div class="inline-block p-4 bg-white mx-auto">
                    {!! $customer->two_factor_service->generateQrCodeSvg($customer) !!}
                </div>
                <p class="text-xs text-muted-text font-light text-center mt-3">Người khác quét mã này để lấy link giới thiệu</p>
            </div>
        </div>
    </div>
</div>
@endsection
