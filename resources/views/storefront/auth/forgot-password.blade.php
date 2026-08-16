@extends('layouts.storefront')

@pushonce('page_title')Quên Mật Khẩu — @endpushonce
@pushonce('meta_description')Đặt lại mật khẩu tài khoản MYSHOP của bạn.@endpushonce

@section('content')
<div class="py-16 md:py-24">
    <div class="section-wrapper">
        <div class="max-w-sm mx-auto">

            <h1 class="text-2xl font-medium tracking-wide text-center mb-3">Quên Mật Khẩu</h1>
            <p class="text-sm text-[#888888] font-light text-center mb-10 leading-relaxed">
                Nhập email của bạn để nhận liên kết đặt lại mật khẩu.
            </p>

            @if(session('status'))
                <div class="border border-green-600 text-green-700 px-4 py-3 text-sm mb-6" role="alert">
                    {{ session('status') }}
                </div>
            @endif

            @if($errors->any())
                <div class="border border-[#E84444] text-[#E84444] px-4 py-3 text-sm mb-6" role="alert">
                    @foreach($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('account.password.email') }}" class="space-y-6">
                @csrf

                <div>
                    <label for="email" class="block text-[10px] tracking-[0.15em] uppercase text-[#888888] mb-2">
                        Địa chỉ email
                    </label>
                    <input id="email"
                           name="email"
                           type="email"
                           autocomplete="email"
                           required
                           value="{{ old('email') }}"
                           class="input-underline w-full @error('email') border-[#E84444] @enderror">
                </div>

                <button type="submit" class="btn-dark w-full">
                    Gửi Liên Kết Đặt Lại
                </button>
            </form>

            <div class="mt-8 text-center">
                <a href="{{ route('account.login') }}"
                   class="text-sm text-[#888888] hover:text-[#1a1a1a] transition-colors font-light">
                    ← Quay lại đăng nhập
                </a>
            </div>

        </div>
    </div>
</div>
@endsection
