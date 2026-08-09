<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class ValidPhoneVN implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  Closure(string): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! preg_match('/^(0|\+84)(3|5|7|8|9)[0-9]{8}$/', $value)) {
            $fail('The :attribute must be a valid Vietnamese phone number.');
        }
    }
}
