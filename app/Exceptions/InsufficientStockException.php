<?php

namespace App\Exceptions;

/**
 * Thrown when stock is insufficient to fulfill an order.
 *
 * User-facing message: "Không đủ tồn kho cho sản phẩm: {name}"
 * HTTP: 422
 */
class InsufficientStockException extends CommerceException {}
