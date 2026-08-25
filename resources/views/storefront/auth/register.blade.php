@extends('layouts.storefront')

@pushonce('page_title')Đăng Ký — @endpushonce
@pushonce('meta_description')Tạo tài khoản MYSHOP để theo dõi đơn hàng và lưu sản phẩm yêu thích.@endpushonce

@section('content')
<div class="py-16 md:py-24">
    <div class="section-wrapper">
        <div class="max-w-sm mx-auto">

            <h1 class="text-2xl font-medium tracking-wide text-center mb-10">Tạo Tài Khoản</h1>

            <form action="{{ url('/account/register') }}" method="POST" class="space-y-6">
                @csrf

                {{-- Referral Code (hidden) --}}
                @if(request()->has('ref'))
                    <input type="hidden" name="ref" value="{{ request()->query('ref') }}">
                @endif

                {{-- Name --}}
                <div>
                    <label for="name" class="block text-[10px] tracking-[0.15em] uppercase text-muted-text mb-2">
                        Họ và tên <span class="text-badge-hot">*</span>
                    </label>
                    <input type="text"
                           name="name"
                           id="name"
                           value="{{ old('name') }}"
                           required
                           autofocus
                           autocomplete="name"
                           class="input-underline w-full @error('name') border-badge-hot @enderror">
                    @error('name')
                        <span class="text-badge-hot text-xs mt-1 block" role="alert">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Email --}}
                <div>
                    <label for="email" class="block text-[10px] tracking-[0.15em] uppercase text-muted-text mb-2">
                        Email <span class="text-badge-hot">*</span>
                    </label>
                    <input type="email"
                           name="email"
                           id="email"
                           value="{{ old('email') }}"
                           required
                           autocomplete="email"
                           class="input-underline w-full @error('email') border-badge-hot @enderror">
                    @error('email')
                        <span class="text-badge-hot text-xs mt-1 block" role="alert">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Phone --}}
                <div>
                    <label for="phone" class="block text-[10px] tracking-[0.15em] uppercase text-muted-text mb-2">
                        Số điện thoại <span class="text-muted-text text-[9px] normal-case tracking-normal">(tuỳ chọn)</span>
                    </label>
                    <input type="tel"
                           name="phone"
                           id="phone"
                           value="{{ old('phone') }}"
                           autocomplete="tel"
                           class="input-underline w-full @error('phone') border-badge-hot @enderror">
                    @error('phone')
                        <span class="text-badge-hot text-xs mt-1 block" role="alert">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Password --}}
                <div>
                    <label for="password" class="block text-[10px] tracking-[0.15em] uppercase text-muted-text mb-2">
                        Mật khẩu <span class="text-badge-hot">*</span>
                    </label>
                    <input type="password"
                           name="password"
                           id="password"
                           required
                           autocomplete="new-password"
                           class="input-underline w-full @error('password') border-badge-hot @enderror">
                    @error('password')
                        <span class="text-badge-hot text-xs mt-1 block" role="alert">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Confirm password --}}
                <div>
                    <label for="password_confirmation" class="block text-[10px] tracking-[0.15em] uppercase text-muted-text mb-2">
                        Xác nhận mật khẩu <span class="text-badge-hot">*</span>
                    </label>
                    <input type="password"
                           name="password_confirmation"
                           id="password_confirmation"
                           required
                           autocomplete="new-password"
                           class="input-underline w-full">
                </div>

                <button type="submit" class="btn-dark w-full">
                    Tạo Tài Khoản
                </button>
            </form>

            <div class="mt-8 text-center">
                <p class="text-sm text-muted-text font-light">
                    Đã có tài khoản?
                    <a href="{{ route('account.login') }}"
                       class="text-[#1a1a1a] hover:opacity-60 transition-opacity ml-1 underline underline-offset-2">
                        Đăng nhập
                    </a>
                </p>
            </div>

        </div>
    </div>
</div>
@endsection
