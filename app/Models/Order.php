<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'customer_name',
        'phone',
        'email',
        'address',
        'city',
        'district',
        'ward',
        'payment_method',
        'total_amount',
        'status',
        'payment_status',
        'notes',
    ];

    protected $casts = [
        'total_amount' => 'decimal:0',
        'status' => 'string',
        'payment_status' => 'string',
        'payment_method' => 'string',
    ];

    public const STATUS_PENDING = 'pending';
    public const STATUS_CONFIRMED = 'confirmed';
    public const STATUS_SHIPPING = 'shipping';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';

    public const PAYMENT_STATUS_UNPAID = 'unpaid';
    public const PAYMENT_STATUS_PAID = 'paid';
    public const PAYMENT_STATUS_REFUNDED = 'refunded';

    public const PAYMENT_METHOD_COD = 'cod';
    public const PAYMENT_METHOD_BANK = 'bank_transfer';

    public static function getStatuses(): array
    {
        return [
            self::STATUS_PENDING => 'Chờ xác nhận',
            self::STATUS_CONFIRMED => 'Đã xác nhận',
            self::STATUS_SHIPPING => 'Đang giao',
            self::STATUS_COMPLETED => 'Hoàn thành',
            self::STATUS_CANCELLED => 'Đã hủy',
        ];
    }

    public static function getPaymentStatuses(): array
    {
        return [
            self::PAYMENT_STATUS_UNPAID => 'Chưa thanh toán',
            self::PAYMENT_STATUS_PAID => 'Đã thanh toán',
            self::PAYMENT_STATUS_REFUNDED => 'Đã hoàn tiền',
        ];
    }

    public static function getPaymentMethods(): array
    {
        return [
            self::PAYMENT_METHOD_COD => 'Thanh toán khi nhận hàng (COD)',
            self::PAYMENT_METHOD_BANK => 'Chuyển khoản ngân hàng',
        ];
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public static function generateOrderNumber(): string
    {
        $prefix = 'MDG';
        $date = now()->format('ymd');
        $random = strtoupper(substr(uniqid(), -4));

        return "{$prefix}-{$date}-{$random}";
    }

    public function getFormattedTotalAttribute(): string
    {
        return number_format($this->total_amount, 0, ',', '.') . '₫';
    }

    public function getStatusLabelAttribute(): string
    {
        return self::getStatuses()[$this->status] ?? $this->status;
    }

    public function getPaymentStatusLabelAttribute(): string
    {
        return self::getPaymentStatuses()[$this->payment_status] ?? $this->payment_status;
    }
}
