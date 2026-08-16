<?php

namespace App\Exceptions;

/**
 * Thrown when cart is empty and checkout is attempted.
 *
 * User-facing message: "Giỏ hàng trống."
 * HTTP: 422
 */
class EmptyCartException extends CommerceException {}
