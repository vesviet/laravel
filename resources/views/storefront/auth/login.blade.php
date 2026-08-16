@extends('layouts.storefront')

@pushonce('page_title')Đăng Nhập — @endpushonce
@pushonce('meta_description')Đăng nhập vào tài khoản MYSHOP của bạn.@endpushonce

@section('content')
<div class="py-16 md:py-24">
    <div class="section-wrapper">
        <div class="max-w-sm mx-auto">

            <h1 class="text-2xl font-medium tracking-wide text-center mb-10">Đăng Nhập</h1>

            @if(session('status'))
                <div class="border border-green-600 text-green-700 px-4 py-3 text-sm mb-6" role="alert">
                    {{ session('status') }}
                </div>
            @endif

            <form action="{{ route('account.login') }}" method="POST" class="space-y-6">
                @csrf

                {{-- Email --}}
                <div>
                    <label for="email" class="block text-[10px] tracking-[0.15em] uppercase text-[#888888] mb-2">
                        Địa chỉ email
                    </label>
                    <input type="email"
                           name="email"
                           id="email"
                           value="{{ old('email') }}"
                           required
                           autofocus
                           autocomplete="email"
                           class="input-underline w-full @error('email') border-[#E84444] @enderror">
                    @error('email')
                        <span class="text-[#E84444] text-xs mt-1 block" role="alert">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Password --}}
                <div>
                    <div class="flex items-baseline justify-between mb-2">
                        <label for="password" class="block text-[10px] tracking-[0.15em] uppercase text-[#888888]">
                            Mật khẩu
                        </label>
                        <a href="{{ route('account.password.request') }}"
                           class="text-[10px] tracking-[0.1em] text-[#888888] hover:text-[#1a1a1a] transition-colors">
                            Quên mật khẩu?
                        </a>
                    </div>
                    <input type="password"
                           name="password"
                           id="password"
                           required
                           autocomplete="current-password"
                           class="input-underline w-full">
                </div>

                {{-- Remember me --}}
                <div class="flex items-center gap-3">
                    <input id="remember"
                           name="remember"
                           type="checkbox"
                           class="h-4 w-4 accent-[#1a1a1a] cursor-pointer">
                    <label for="remember" class="text-sm text-[#888888] cursor-pointer font-light">
                        Ghi nhớ đăng nhập
                    </label>
                </div>

                <button type="submit" class="btn-dark w-full">
                    Đăng Nhập
                </button>
            </form>

            <div class="mt-8 text-center">
                <p class="text-sm text-[#888888] font-light">
                    Chưa có tài khoản?
                    <a href="{{ route('register') }}"
                       class="text-[#1a1a1a] hover:opacity-60 transition-opacity ml-1 underline underline-offset-2">
                        Đăng ký ngay
                    </a>
                </p>
            </div>

        </div>
    </div>
</div>
@endsection
