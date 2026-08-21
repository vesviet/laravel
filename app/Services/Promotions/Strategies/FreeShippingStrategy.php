<?php

namespace App\Services\Promotions\Strategies;

use App\Models\PromotionRule;
use App\Services\Promotions\Contracts\PromotionStrategyInterface;
use App\Services\Promotions\DTOs\AppliedRuleDiscount;

class FreeShippingStrategy implements PromotionStrategyInterface
{
    public function calculate(
        PromotionRule $rule,
        float $eligibleSubtotal,
        array $cartItems,
        float $shippingFee = 0.0
    ): ?AppliedRuleDiscount {
        if ($shippingFee <= 0.0) {
            return null;
        }

        $discountAmount = ($rule->discount_value !== null && (float) $rule->discount_value > 0)
            ? min($shippingFee, (float) $rule->discount_value)
            : $shippingFee;

        $discountAmount = round($discountAmount, 2);

        if ($discountAmount <= 0.0) {
            return null;
        }

        $description = ($rule->discount_value !== null && (float) $rule->discount_value > 0 && (float) $rule->discount_value < $shippingFee)
            ? sprintf('Giảm %s₫ phí vận chuyển', number_format($discountAmount, 0, ',', '.'))
            : 'Miễn phí vận chuyển (Freeship 100%)';

        return new AppliedRuleDiscount(
            ruleId: (int) $rule->id,
            ruleName: (string) ($rule->name ?? ''),
            ruleCode: $rule->code,
            actionType: PromotionRule::ACTION_FREE_SHIPPING,
            discountAmount: $discountAmount,
            target: 'shipping',
            description: $description,
            isCoupon: $rule->isCoupon(),
            rule: $rule
        );
    }
}
