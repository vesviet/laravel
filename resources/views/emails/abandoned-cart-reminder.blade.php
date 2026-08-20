<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Giỏ hàng của bạn đang chờ</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #F7F7F7; margin: 0; padding: 20px; color: #23232C; }
        .container { max-width: 600px; margin: 0 auto; background: #ffffff; padding: 32px; border: 1px solid #E5E5E5; }
        .header { text-align: center; border-bottom: 2px solid #23232C; padding-bottom: 16px; margin-bottom: 24px; }
        .logo { font-size: 20px; font-weight: bold; letter-spacing: 0.15em; text-transform: uppercase; color: #23232C; }
        .title { font-size: 18px; font-weight: 600; margin-bottom: 12px; }
        .text { font-size: 14px; line-height: 1.6; color: #555555; margin-bottom: 20px; }
        .item-list { width: 100%; border-collapse: collapse; margin-bottom: 24px; }
        .item-list th, .item-list td { padding: 12px; border-bottom: 1px solid #EBEBEB; text-align: left; font-size: 13px; }
        .item-list th { background-color: #F9F9F9; font-weight: 600; text-transform: uppercase; font-size: 11px; letter-spacing: 0.05em; }
        .btn-cta { display: inline-block; background-color: #23232C; color: #ffffff !important; padding: 14px 28px; text-decoration: none; font-size: 13px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.1em; }
        .footer { margin-top: 32px; padding-top: 16px; border-top: 1px solid #E5E5E5; text-align: center; font-size: 11px; color: #888888; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">{{ config('app.name', 'SOBER') }}</div>
        </div>

        <div class="title">Bạn có sản phẩm đang chờ trong giỏ hàng!</div>
        <div class="text">
            Chào bạn,<br>
            Chúng tôi nhận thấy bạn đã để lại một số sản phẩm tuyệt vời trong giỏ hàng. Đừng bỏ lỡ — số lượng trong kho có thể thay đổi nhanh chóng:
        </div>

        <table class="item-list">
            <thead>
                <tr>
                    <th>Sản phẩm</th>
                    <th>Số lượng</th>
                    <th>Giá</th>
                </tr>
            </thead>
            <tbody>
                @foreach($abandonedCart->items_json as $item)
                    <tr>
                        <td><strong>{{ $item['name'] ?? 'Sản phẩm' }}</strong></td>
                        <td>{{ $item['quantity'] ?? 1 }}</td>
                        <td>{{ number_format($item['price'] ?? 0, 0, ',', '.') }}₫</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ route('checkout.index', ['token' => $abandonedCart->cart_token]) }}" class="btn-cta">
                Hoàn tất đơn hàng ngay →
            </a>
        </div>

        <div class="footer">
            Nếu bạn đã đặt hàng hoặc không muốn nhận thông báo này, xin vui lòng bỏ qua email.<br>
            © {{ date('Y') }} {{ config('app.name', 'Sober Furniture') }}. All rights reserved.
        </div>
    </div>
</body>
</html>
