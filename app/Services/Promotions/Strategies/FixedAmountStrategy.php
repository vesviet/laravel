<?php

namespace App\Services\Promotions\Strategies;

use App\Models\PromotionRule;
use App\Services\Promotions\Contracts\PromotionStrategyInterface;
use App\Services\Promotions\DTOs\AppliedRuleDiscount;

class FixedAmountStrategy implements PromotionStrategyInterface
{
    public function calculate(
        PromotionRule $rule,
        float $eligibleSubtotal,
        array $cartItems,
        float $shippingFee = 0.0
    ): ?AppliedRuleDiscount {
        if ($eligibleSubtotal <= 0 || (float) $rule->discount_value <= 0) {
            return null;
        }

        $discountAmount = min((float) $rule->discount_value, $eligibleSubtotal);
        $discountAmount = round($discountAmount, 2);

        if ($discountAmount <= 0) {
            return null;
        }

        $description = sprintf('Giảm %s₫ trực tiếp vào đơn hàng', number_format($discountAmount, 0, ',', '.'));

        return new AppliedRuleDiscount(
            ruleId: (int) $rule->id,
            ruleName: (string) ($rule->name ?? ''),
            ruleCode: $rule->code,
            actionType: PromotionRule::ACTION_FIXED_AMOUNT,
            discountAmount: $discountAmount,
            target: 'item',
            description: $description,
            isCoupon: $rule->isCoupon(),
            rule: $rule
        );
    }
}
