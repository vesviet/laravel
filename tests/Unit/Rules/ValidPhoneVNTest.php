<?php

use App\Rules\ValidPhoneVN;

it('validates correct Vietnamese phone numbers', function ($phone) {
    $rule = new ValidPhoneVN();
    $failed = false;
    $rule->validate('phone', $phone, function ($message) use (&$failed) {
        $failed = true;
    });

    expect($failed)->toBeFalse();
})->with([
    '0901234567',
    '+84901234567',
    '0391234567',
    '0881234567',
]);

it('rejects incorrect Vietnamese phone numbers', function ($phone) {
    $rule = new ValidPhoneVN();
    $failed = false;
    $rule->validate('phone', $phone, function ($message) use (&$failed) {
        $failed = true;
    });

    expect($failed)->toBeTrue();
})->with([
    '1901234567', // Not starting with 0 or +84
    '090123456',  // Too short
    '09012345678', // Too long
    '0123456789', // Invalid prefix
    'abc1234567',
]);
