<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Mã giảm giá độc quyền dành riêng cho bạn</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #F7F7F7; margin: 0; padding: 20px; color: #23232C; }
        .container { max-width: 600px; margin: 0 auto; background: #ffffff; padding: 32px; border: 1px solid #E5E5E5; }
        .header { text-align: center; border-bottom: 2px solid #23232C; padding-bottom: 16px; margin-bottom: 24px; }
        .logo { font-size: 20px; font-weight: bold; letter-spacing: 0.15em; text-transform: uppercase; color: #23232C; }
        .title { font-size: 18px; font-weight: 600; color: #E84444; margin-bottom: 12px; }
        .text { font-size: 14px; line-height: 1.6; color: #555555; margin-bottom: 20px; }
        .coupon-box { background-color: #FFF5F5; border: 2px dashed #E84444; padding: 20px; text-align: center; margin: 24px 0; }
        .coupon-code { font-size: 22px; font-weight: bold; letter-spacing: 0.15em; color: #E84444; margin-top: 8px; }
        .coupon-expiry { font-size: 11px; color: #888888; margin-top: 6px; }
        .btn-cta { display: inline-block; background-color: #E84444; color: #ffffff !important; padding: 14px 28px; text-decoration: none; font-size: 13px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.1em; }
        .footer { margin-top: 32px; padding-top: 16px; border-top: 1px solid #E5E5E5; text-align: center; font-size: 11px; color: #888888; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">{{ config('app.name', 'SOBER') }}</div>
        </div>

        <div class="title">🎁 Món quà đặc biệt dành cho giỏ hàng của bạn!</div>
        <div class="text">
            Chào bạn,<br>
            Để giúp bạn dễ dàng sở hữu những món đồ yêu thích, chúng tôi gửi tặng bạn mã ưu đãi giảm <strong>{{ $discountPercent }}%</strong> áp dụng ngay cho đơn hàng:
        </div>

        <div class="coupon-box">
            <div style="font-size: 12px; text-transform: uppercase; color: #666666;">Mã giảm giá độc quyền:</div>
            <div class="coupon-code">{{ $couponCode }}</div>
            <div class="coupon-expiry">Thời hạn sử dụng: 48 giờ kể từ khi nhận email</div>
        </div>

        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ route('checkout.index', ['token' => $abandonedCart->cart_token, 'coupon' => $couponCode]) }}" class="btn-cta">
                Sử dụng mã & Thanh toán ngay →
            </a>
        </div>

        <div class="footer">
            Ưu đãi có giá trị cho 1 lần đặt hàng.<br>
            © {{ date('Y') }} {{ config('app.name', 'Sober Furniture') }}. All rights reserved.
        </div>
    </div>
</body>
</html>
