<?php

namespace App\Services\Promotions\Strategies;

use App\Models\PromotionRule;
use App\Services\Promotions\Contracts\PromotionStrategyInterface;
use App\Services\Promotions\DTOs\AppliedRuleDiscount;

class PercentageWithCapStrategy implements PromotionStrategyInterface
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

        $percent = min(100.0, max(0.0, (float) $rule->discount_value));
        if ($percent <= 0) {
            return null;
        }

        $rawDiscount = $eligibleSubtotal * ($percent / 100.0);

        $discountAmount = $rawDiscount;
        if ($rule->max_discount_amount !== null && $rule->max_discount_amount > 0) {
            $discountAmount = min($discountAmount, (float) $rule->max_discount_amount);
        }

        $discountAmount = min($discountAmount, $eligibleSubtotal);
        $discountAmount = round($discountAmount, 2);

        if ($discountAmount <= 0) {
            return null;
        }

        $description = ($rule->max_discount_amount !== null && $rule->max_discount_amount > 0)
            ? sprintf('Giảm %s%% (tối đa %s₫)', $percent, number_format($rule->max_discount_amount, 0, ',', '.'))
            : sprintf('Giảm %s%%', $percent);

        return new AppliedRuleDiscount(
            ruleId: (int) $rule->id,
            ruleName: (string) ($rule->name ?? ''),
            ruleCode: $rule->code,
            actionType: PromotionRule::ACTION_PERCENTAGE,
            discountAmount: $discountAmount,
            target: 'item',
            description: $description,
            isCoupon: $rule->isCoupon(),
            rule: $rule
        );
    }
}
