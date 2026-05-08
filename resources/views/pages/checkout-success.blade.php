@extends('layouts.app')
@section('title', 'Đặt hàng thành công - Máy Điện Giải Sài Gòn')
@section('content')
<div style="height: var(--header-height);"></div>
<section class="section">
    <div class="container">
        <div class="success-card">
            <div class="success-icon">✅</div>
            <h1 style="font-size:var(--font-size-2xl);font-weight:800;margin-bottom:0.5rem;">Đặt Hàng Thành Công!</h1>
            <p style="color:var(--color-gray-500);margin-bottom:2rem;">
                Cảm ơn bạn đã đặt hàng. Chúng tôi sẽ liên hệ xác nhận trong thời gian sớm nhất.
            </p>

            <div style="background:var(--color-gray-50);border-radius:var(--radius-md);padding:1.5rem;text-align:left;margin-bottom:1.5rem;">
                <div style="display:flex;justify-content:space-between;margin-bottom:0.75rem;">
                    <span style="color:var(--color-gray-500);">Mã đơn hàng:</span>
                    <span style="font-weight:700;color:var(--color-teal-dark);">{{ $order->order_number }}</span>
                </div>
                <div style="display:flex;justify-content:space-between;margin-bottom:0.75rem;">
                    <span style="color:var(--color-gray-500);">Khách hàng:</span>
                    <span style="font-weight:600;">{{ $order->customer_name }}</span>
                </div>
                <div style="display:flex;justify-content:space-between;margin-bottom:0.75rem;">
                    <span style="color:var(--color-gray-500);">Số điện thoại:</span>
                    <span>{{ $order->phone }}</span>
                </div>
                <div style="display:flex;justify-content:space-between;">
                    <span style="color:var(--color-gray-500);">Tổng tiền:</span>
                    <span style="font-size:var(--font-size-xl);font-weight:800;color:var(--color-teal-dark);">{{ $order->formatted_total }}</span>
                </div>
            </div>

            @if($order->payment_method === 'bank_transfer')
            <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:var(--radius-md);padding:1.5rem;text-align:left;margin-bottom:1.5rem;">
                <h3 style="font-weight:700;margin-bottom:0.75rem;">🏦 Thông tin chuyển khoản</h3>
                <p style="font-size:0.875rem;color:var(--color-gray-700);margin-bottom:0.75rem;">
                    Vui lòng chuyển khoản theo thông tin dưới đây:
                </p>
                <div style="font-size:0.875rem;line-height:1.8;">
                    <strong>Ngân hàng:</strong> Vietcombank<br>
                    <strong>Số TK:</strong> 1234567890<br>
                    <strong>Chủ TK:</strong> CONG TY MAY DIEN GIAI SAIGON<br>
                    <strong>Nội dung CK:</strong> {{ $order->order_number }}
                </div>
                <div style="text-align:center;margin-top:1rem;padding:1rem;background:white;border-radius:var(--radius-md);">
                    <p style="font-size:0.75rem;color:var(--color-gray-500);margin-bottom:0.5rem;">Quét mã QR để chuyển khoản nhanh</p>
                    @php
                        $bankId = 'VCB';
                        $accountNo = '1234567890';
                        $accountName = 'CONG TY MAY DIEN GIAI SAIGON';
                        $amount = (int) $order->total_amount;
                        $info = $order->order_number;
                        $qrUrl = "https://img.vietqr.io/image/{$bankId}-{$accountNo}-compact2.png?amount={$amount}&addInfo={$info}&accountName=" . urlencode($accountName);
                    @endphp
                    <img src="{{ $qrUrl }}" alt="VietQR" style="max-width:240px;height:auto;margin:0 auto;border:1px solid var(--color-gray-100);border-radius:var(--radius-md);">
                </div>
            </div>
            @endif

            <a href="{{ route('home') }}" class="btn btn-primary">← Về trang chủ</a>
        </div>
    </div>
</section>
@endsection
