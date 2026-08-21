<?php

namespace App\Services\Promotions\Strategies;

use App\Models\PromotionRule;
use App\Services\Promotions\Contracts\PromotionStrategyInterface;
use App\Services\Promotions\DTOs\AppliedRuleDiscount;

class BuyXGetYStrategy implements PromotionStrategyInterface
{
    public function calculate(
        PromotionRule $rule,
        float $eligibleSubtotal,
        array $cartItems,
        float $shippingFee = 0.0
    ): ?AppliedRuleDiscount {
        if (empty($cartItems)) {
            return null;
        }

        $conditions = $rule->conditions ?? [];
        $config = $conditions['bxgy_config'] ?? $conditions;

        if (empty($config)) {
            return null;
        }

        $buyProductId = (int) ($config['buy_product_id'] ?? $config['buy_x_product_id'] ?? 0);
        $buyQty = max(1, (int) ($config['buy_quantity'] ?? $config['buy_x_quantity'] ?? 1));
        $getProductId = (int) ($config['get_product_id'] ?? $config['get_y_product_id'] ?? $buyProductId);
        $getQty = max(1, (int) ($config['get_quantity'] ?? $config['get_y_quantity'] ?? 1));
        $isFree = array_key_exists('is_free', $config) ? (bool) $config['is_free'] : true;
        $configuredDiscount = $config['discount_value'] ?? $config['discount_percentage'] ?? $config['discount_percent'] ?? null;
        if ($isFree) {
            $discountValue = 100.0;
        } elseif ($configuredDiscount !== null) {
            $discountValue = (float) $configuredDiscount;
        } elseif (! empty($rule->discount_value) && (float) $rule->discount_value > 0) {
            $discountValue = (float) $rule->discount_value;
        } else {
            $discountValue = 100.0;
        }
        $maxRewards = ! empty($config['max_rewards']) ? (int) $config['max_rewards'] : null;

        if ($buyProductId <= 0 && $getProductId <= 0) {
            return null;
        }

        // Count non-flash-sale items in cart
        $buyCartQty = 0;
        $getCartQty = 0;
        $getUnitPrice = 0.0;
        $getProductName = 'sản phẩm tặng';

        foreach ($cartItems as $item) {
            if (! empty($item['is_flash_sale'])) {
                continue;
            }

            $pid = (int) ($item['product_id'] ?? $item['id'] ?? 0);
            $qty = (int) ($item['quantity'] ?? 1);
            $price = (float) ($item['price'] ?? 0.0);

            if ($buyProductId === 0 || $pid === $buyProductId) {
                $buyCartQty += $qty;
            }

            if ($pid === $getProductId) {
                $getCartQty += $qty;
                if ($getUnitPrice <= 0.0) {
                    $getUnitPrice = $price;
                    $getProductName = $item['product_name'] ?? $item['name'] ?? $getProductName;
                }
            }
        }

        $qualifiedRewards = 0;

        if ($buyProductId === $getProductId) {
            // Same product: Buy X Get Y from the same item pool
            $setCost = $buyQty + $getQty;
            $sets = intdiv($buyCartQty, $setCost);
            $qualifiedRewards = $sets * $getQty;
        } else {
            // Different products: Buy X triggers reward Y in cart
            $sets = intdiv($buyCartQty, $buyQty);
            $entitledRewards = $sets * $getQty;
            $qualifiedRewards = min($getCartQty, $entitledRewards);
        }

        if ($maxRewards !== null && $maxRewards > 0) {
            $qualifiedRewards = min($qualifiedRewards, $maxRewards);
        }

        if ($qualifiedRewards <= 0 || $getUnitPrice <= 0.0) {
            return null;
        }

        $discountPercent = min(100.0, max(0.0, $discountValue));
        if ($discountPercent <= 0.0) {
            return null;
        }

        $rawDiscount = $qualifiedRewards * $getUnitPrice * ($discountPercent / 100.0);

        $discountAmount = $rawDiscount;
        if ($rule->max_discount_amount !== null && $rule->max_discount_amount > 0) {
            $discountAmount = min($discountAmount, (float) $rule->max_discount_amount);
        }

        $discountAmount = min($discountAmount, $eligibleSubtotal);
        $discountAmount = round($discountAmount, 2);

        if ($discountAmount <= 0) {
            return null;
        }

        $description = ($isFree || $discountPercent >= 100)
            ? sprintf('Tặng %d %s miễn phí (Mua %d Tặng %d)', $qualifiedRewards, $getProductName, $buyQty, $getQty)
            : sprintf('Giảm %s%% cho %d %s (Mua %d Tặng %d)', $discountPercent, $qualifiedRewards, $getProductName, $buyQty, $getQty);

        return new AppliedRuleDiscount(
            ruleId: (int) $rule->id,
            ruleName: (string) ($rule->name ?? ''),
            ruleCode: $rule->code,
            actionType: PromotionRule::ACTION_BUY_X_GET_Y,
            discountAmount: $discountAmount,
            target: 'item',
            description: $description,
            isCoupon: $rule->isCoupon(),
            freeGift: null,
            rule: $rule
        );
    }
}
