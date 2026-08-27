<?php

namespace App\Exceptions;

use RuntimeException;
use Throwable;

/**
 * Base exception for all Seller Center domain errors.
 *
 * Used by RegisterSellerAction, PublishSellerPageAction, and
 * ProcessSellerQuickOrderAction so callers (Filament resources,
 * Livewire components) can render a single localized error block.
 */
class SellerActionException extends RuntimeException
{
    public function __construct(
        string $message = 'Seller action failed.',
        public readonly string $errorCode = 'seller_action_failed',
        ?Throwable $previous = null,
    ) {
        parent::__construct($message, 0, $previous);
    }

    public static function registrationFailed(Throwable $e): self
    {
        return new self(
            'Đăng ký tài khoản Seller thất bại: '.$e->getMessage(),
            'seller_registration_failed',
            $e,
        );
    }

    public static function pageNotInitialized(): self
    {
        return new self(
            'Trang web chưa được khởi tạo.',
            'seller_page_not_initialized',
        );
    }

    public static function pageUpdateFailed(Throwable $e): self
    {
        return new self(
            'Không thể cập nhật trạng thái trang: '.$e->getMessage(),
            'seller_page_update_failed',
            $e,
        );
    }

    public static function orderFailed(Throwable $e): self
    {
        return new self(
            'Không thể xử lý đơn hàng: '.$e->getMessage(),
            'seller_order_failed',
            $e,
        );
    }

    public static function outOfStock(): self
    {
        return new self(
            'Sản phẩm đã hết hàng hoặc không đủ số lượng.',
            'seller_out_of_stock',
        );
    }
}
