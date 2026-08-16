<?php

namespace App\Exceptions;

/**
 * Thrown when a coupon is invalid, expired, or usage limit reached.
 *
 * User-facing message: "Mã giảm giá không hợp lệ hoặc đã hết lượt sử dụng."
 * HTTP: 422
 */
class InvalidCouponException extends CommerceException {}
