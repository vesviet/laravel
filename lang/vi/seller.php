<?php

return [
    'telegram' => [
        'order_notification_title' => '🛒 *CÓ ĐƠN HÀNG MỚI!*',
        'order_number' => '📦 *Mã ĐH:* :order_number',
        'customer' => '👤 *Khách hàng:* :name',
        'phone' => '📞 *SĐT:* :phone',
        'address' => '📍 *Địa chỉ:* :address',
        'total' => '💰 *Tổng tiền:* :total',
        'payment_method' => '💳 *Thanh toán:* :method',
        'notes' => '📝 *Ghi chú:* :notes',
        'notes_empty' => 'Không có',
        'items_header' => '👇 *Sản phẩm:*',
        'item_line' => '- :name x:qty',
        'payment_cod' => 'COD',
        'payment_vietqr' => 'Chuyển khoản VietQR',
        'bot_token_missing' => 'Telegram bot token is not configured.',
    ],
    'actions' => [
        'registration_failed' => 'Đăng ký tài khoản Seller thất bại: :error',
        'page_not_initialized' => 'Trang web chưa được khởi tạo.',
        'page_update_failed' => 'Không thể cập nhật trạng thái trang: :error',
        'order_failed' => 'Không thể xử lý đơn hàng: :error',
        'out_of_stock' => 'Sản phẩm đã hết hàng hoặc không đủ số lượng.',
    ],
    'product_status' => [
        'draft' => 'Bản nháp',
        'published' => 'Đã xuất bản',
        'archived' => 'Đã lưu trữ',
    ],
    'order_status' => [
        'pending' => 'Chờ xác nhận',
        'confirmed' => 'Đã xác nhận',
        'processing' => 'Đang xử lý',
        'shipped' => 'Đang giao',
        'delivered' => 'Đã giao',
        'cancelled' => 'Đã hủy',
    ],
];
