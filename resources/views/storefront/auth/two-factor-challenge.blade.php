@extends("layouts.storefront")

@pushonce("page_title")Xác Thực Hai Yếu Tố — @endpushonce
@pushonce("meta_description")Nhập mã xác thực từ ứng dụng Authenticator để hoàn tất đăng nhập.@endpushonce

@section("content")
<div class="py-16 md:py-24">
    <div class="section-wrapper">
        <div class="max-w-sm mx-auto">
            <h1 class="text-2xl font-medium tracking-wide text-center mb-3">Xác Thực Hai Yếu Tố</h1>
            <p class="text-sm text-muted-text font-light text-center mb-10 leading-relaxed">
                Nhập mã 6 chữ số từ ứng dụng Authenticator (Google Authenticator, Authy, Microsoft Authenticator) để hoàn tất đăng nhập.
            </p>

            @if($errors->any())
                <div class="border border-badge-hot text-badge-hot px-4 py-3 text-sm mb-6" role="alert">
                    @foreach($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route("account.2fa.verify") }}" class="space-y-6">
                @csrf

                {{-- TOTP Code --}}
                <div>
                    <label for="code" class="block text-[10px] tracking-[0.15em] uppercase text-muted-text mb-2">
                        Mã xác thực 6 chữ số <span class="text-badge-hot">*</span>
                    </label>
                    <input type="text"
                           name="code"
                           id="code"
                           maxlength="6"
                           pattern="[0-9]*"
                           inputmode="numeric"
                           required
                           autocomplete="one-time-code"
                           class="input-underline w-full text-center tracking-widest text-xl @error("code") border-badge-hot @enderror">
                    @error("code")
                        <span class="text-badge-hot text-xs mt-1 block" role="alert">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Recovery Code Option --}}
                <div class="pt-2 border-t border-border-subtle">
                    <p class="text-xs text-muted-text font-light text-center mb-4">
                        Hoặc sử dụng mã khôi phục
                    </p>
                    <div>
                        <label for="recovery_code" class="block text-[10px] tracking-[0.15em] uppercase text-muted-text mb-2">
                            Mã khôi phục (8 ký tự)
                        </label>
                        <input type="text"
                               name="recovery_code"
                               id="recovery_code"
                               maxlength="8"
                               autocomplete="off"
                               class="input-underline w-full text-center tracking-widest text-xl">
                    </div>
                </div>

                <button type="submit" class="btn-dark w-full">
                    Xác Thực
                </button>
            </form>

            <div class="mt-8 text-center">
                <a href="{{ route("account.login") }}"
                   class="text-sm text-muted-text hover:text-[#1a1a1a] transition-colors font-light">
                    ← Quay lại đăng nhập
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
