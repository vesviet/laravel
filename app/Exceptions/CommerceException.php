<?php

namespace App\Exceptions;

use RuntimeException;

/**
 * Base exception for all Commerce domain errors.
 *
 * Catching this base class in controllers/Livewire components
 * allows unified handling of business rule violations that
 * should surface to the user as friendly messages.
 *
 * HTTP mapping: 422 Unprocessable Entity (business rule violation)
 */
class CommerceException extends RuntimeException {}
