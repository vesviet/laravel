<?php

namespace App\Listeners;

use App\Events\SellerOrderPlaced;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendSellerTelegramNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(SellerOrderPlaced $event): void
    {
        $order = $event->order;
        $seller = $event->seller;

        if (empty($seller->telegram_chat_id)) {
            return;
        }

        $botToken = config('services.telegram.bot_token');
        if (empty($botToken)) {
            Log::warning('Telegram bot token is not configured.');
            return;
        }

        $message = "🛒 *CÓ ĐƠN HÀNG MỚI!*\n\n";
        $message .= "📦 *Mã ĐH:* {$order->order_number}\n";
        $message .= "👤 *Khách hàng:* {$order->customer_name}\n";
        $message .= "📞 *SĐT:* {$order->phone}\n";
        $message .= "📍 *Địa chỉ:* {$order->address}\n";
        $message .= "💰 *Tổng tiền:* {$order->formatted_total_amount}\n";
        $message .= "💳 *Thanh toán:* " . ($order->payment_method === 'vietqr' ? 'Chuyển khoản VietQR' : 'COD') . "\n";
        $message .= "📝 *Ghi chú:* " . ($order->notes ?? 'Không có') . "\n\n";
        
        $message .= "👇 *Sản phẩm:*\n";
        foreach ($order->items as $item) {
            $message .= "- {$item->product_name} x{$item->quantity}\n";
        }

        try {
            $response = Http::timeout(5)->post("https://api.telegram.org/bot{$botToken}/sendMessage", [
                'chat_id' => $seller->telegram_chat_id,
                'text' => $message,
                'parse_mode' => 'Markdown',
            ]);

            if (!$response->successful()) {
                Log::error('Failed to send Telegram notification', ['response' => $response->body()]);
            }
        } catch (\Exception $e) {
            Log::error('Exception sending Telegram notification: ' . $e->getMessage());
        }
    }
}
