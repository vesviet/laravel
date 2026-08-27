<?php

return [
    'telegram' => [
        'order_notification_title' => '🛒 *NEW ORDER!*',
        'order_number' => '📦 *Order ID:* :order_number',
        'customer' => '👤 *Customer:* :name',
        'phone' => '📞 *Phone:* :phone',
        'address' => '📍 *Address:* :address',
        'total' => '💰 *Total:* :total',
        'payment_method' => '💳 *Payment:* :method',
        'notes' => '📝 *Notes:* :notes',
        'notes_empty' => 'None',
        'items_header' => '👇 *Items:*',
        'item_line' => '- :name x:qty',
        'payment_cod' => 'COD',
        'payment_vietqr' => 'VietQR bank transfer',
        'bot_token_missing' => 'Telegram bot token is not configured.',
    ],
    'actions' => [
        'registration_failed' => 'Seller registration failed: :error',
        'page_not_initialized' => 'Store page has not been initialized.',
        'page_update_failed' => 'Failed to update page status: :error',
        'order_failed' => 'Could not process order: :error',
        'out_of_stock' => 'Product is out of stock or insufficient quantity.',
    ],
    'product_status' => [
        'draft' => 'Draft',
        'published' => 'Published',
        'archived' => 'Archived',
    ],
    'order_status' => [
        'pending' => 'Pending',
        'confirmed' => 'Confirmed',
        'processing' => 'Processing',
        'shipped' => 'Shipped',
        'delivered' => 'Delivered',
        'cancelled' => 'Cancelled',
    ],
];
