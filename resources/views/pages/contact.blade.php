@extends('layouts.app')
@section('title', 'Liên hệ - Máy Điện Giải Sài Gòn')
@section('content')
<div style="height: var(--header-height);"></div>
<section class="section">
    <div class="container" style="max-width:800px;">
        <div class="section-header">
            <div class="section-badge">Liên hệ</div>
            <h1 class="section-title">Liên Hệ Với Chúng Tôi</h1>
            <p class="section-desc">Chúng tôi luôn sẵn sàng hỗ trợ bạn</p>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:3rem;">
            <div>
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
            </div>
            <div>
                <h3 style="font-weight:700;margin-bottom:1.5rem;">Gửi tin nhắn</h3>
                <form>
                    <div class="form-group">
                        <label class="form-label">Họ tên</label>
                        <input type="text" class="form-input" placeholder="Nhập họ tên">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Số điện thoại</label>
                        <input type="tel" class="form-input" placeholder="09xx xxx xxx">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Nội dung</label>
                        <textarea class="form-textarea" rows="4" placeholder="Bạn cần tư vấn gì?"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;">Gửi tin nhắn</button>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection
