@extends('layouts.app')
@section('title', $page->meta_title ?: $page->title . ' - Máy Điện Giải Sài Gòn')
@section('meta_description', $page->meta_description ?: '')
@section('content')
<div style="height: var(--header-height);"></div>
<section class="section">
    <div class="container" style="max-width:800px;">
        <div class="section-header">
            <div class="section-badge">Liên hệ</div>
            <h1 class="section-title">{{ $page->title }}</h1>
            @if($page->excerpt)
                <p class="section-desc">{{ $page->excerpt }}</p>
            @endif
        </div>

        @if(session('success'))
            <div style="padding:1rem 1.5rem;background:var(--color-teal-light,#e6f7f7);border-left:4px solid var(--color-teal);border-radius:var(--radius-md);margin-bottom:2rem;color:var(--color-teal-dark);">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div style="padding:1rem 1.5rem;background:#fef2f2;border-left:4px solid #ef4444;border-radius:var(--radius-md);margin-bottom:2rem;color:#dc2626;">
                {{ session('error') }}
            </div>
        @endif

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:3rem;">
            <div>
                @if($page->content)
                    <div class="page-content" style="line-height:1.8;color:var(--color-gray-700);">
                        {!! $page->content !!}
                    </div>
                @else
                    <h3 style="font-weight:700;margin-bottom:1.5rem;">Thông tin liên hệ</h3>
                    <div style="display:flex;flex-direction:column;gap:1.25rem;">
                        <div style="display:flex;gap:0.75rem;align-items:start;">
                            <span style="font-size:1.5rem;">📍</span>
                            <div>
                                <div style="font-weight:600;">Địa chỉ</div>
                                <div style="color:var(--color-gray-500);font-size:0.875rem;">123 Nguyễn Văn Linh, Quận 7, TP.HCM</div>
                            </div>
                        </div>
                        <div style="display:flex;gap:0.75rem;align-items:start;">
                            <span style="font-size:1.5rem;">📞</span>
                            <div>
                                <div style="font-weight:600;">Hotline</div>
                                <a href="tel:0901234567" style="color:var(--color-teal-dark);font-weight:600;">0901 234 567</a>
                            </div>
                        </div>
                        <div style="display:flex;gap:0.75rem;align-items:start;">
                            <span style="font-size:1.5rem;">✉️</span>
                            <div>
                                <div style="font-weight:600;">Email</div>
                                <a href="mailto:info@maydiengiaisaigon.vn" style="color:var(--color-teal-dark);">info@maydiengiaisaigon.vn</a>
                            </div>
                        </div>
                        <div style="display:flex;gap:0.75rem;align-items:start;">
                            <span style="font-size:1.5rem;">🕐</span>
                            <div>
                                <div style="font-weight:600;">Giờ làm việc</div>
                                <div style="color:var(--color-gray-500);font-size:0.875rem;">T2 - T7: 8:00 - 18:00</div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
            <div>
                <h3 style="font-weight:700;margin-bottom:1.5rem;">Gửi tin nhắn</h3>
                <form method="POST" action="{{ route('contact.submit') }}">
                    @csrf
                    <div class="form-group">
                        <label class="form-label">Họ tên <span style="color:#ef4444;">*</span></label>
                        <input type="text" name="name" class="form-input" placeholder="Nhập họ tên" value="{{ old('name') }}" required>
                        @error('name')
                            <div style="color:#ef4444;font-size:0.8rem;margin-top:0.25rem;">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Số điện thoại <span style="color:#ef4444;">*</span></label>
                        <input type="tel" name="phone" class="form-input" placeholder="09xx xxx xxx" value="{{ old('phone') }}" required>
                        @error('phone')
                            <div style="color:#ef4444;font-size:0.8rem;margin-top:0.25rem;">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-input" placeholder="email@example.com" value="{{ old('email') }}">
                        @error('email')
                            <div style="color:#ef4444;font-size:0.8rem;margin-top:0.25rem;">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Nội dung <span style="color:#ef4444;">*</span></label>
                        <textarea name="message" class="form-textarea" rows="4" placeholder="Bạn cần tư vấn gì?" required>{{ old('message') }}</textarea>
                        @error('message')
                            <div style="color:#ef4444;font-size:0.8rem;margin-top:0.25rem;">{{ $message }}</div>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;">Gửi tin nhắn</button>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection
