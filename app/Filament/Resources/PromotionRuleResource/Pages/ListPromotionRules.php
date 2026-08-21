<?php

namespace App\Filament\Resources\PromotionRuleResource\Pages;

use App\Filament\Resources\PromotionRuleResource;
use App\Models\PromotionRule;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListPromotionRules extends ListRecords
{
    protected static string $resource = PromotionRuleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tạo Khuyến Mãi Mới')
                ->icon('heroicon-o-plus'),
        ];
    }

    /**
     * Define the 5 interactive navigation tabs with real-time badge counts.
     */
    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Tất cả')
                ->icon('heroicon-m-clipboard-document-list')
                ->badge(fn () => PromotionRule::query()->count())
                ->badgeColor('gray'),

            'cart_rules' => Tab::make('Khuyến Mãi Giỏ Hàng (Cart Rules)')
                ->icon('heroicon-m-shopping-bag')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('rule_type', PromotionRule::RULE_TYPE_CART))
                ->badge(fn () => PromotionRule::query()->where('rule_type', PromotionRule::RULE_TYPE_CART)->count())
                ->badgeColor('success'),

            'catalog_rules' => Tab::make('Khuyến Mãi Danh Mục / Giá (Catalog Rules)')
                ->icon('heroicon-m-tag')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('rule_type', PromotionRule::RULE_TYPE_CATALOG))
                ->badge(fn () => PromotionRule::query()->where('rule_type', PromotionRule::RULE_TYPE_CATALOG)->count())
                ->badgeColor('info'),

            'coupons' => Tab::make('Mã Coupon')
                ->icon('heroicon-m-bolt')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereNotNull('code')->where('code', '!=', ''))
                ->badge(fn () => PromotionRule::query()->whereNotNull('code')->where('code', '!=', '')->count())
                ->badgeColor('warning'),

            'bxgy' => Tab::make('Mua X Tặng Y (BXGY)')
                ->icon('heroicon-m-gift')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('action_type', PromotionRule::ACTION_BUY_X_GET_Y))
                ->badge(fn () => PromotionRule::query()->where('action_type', PromotionRule::ACTION_BUY_X_GET_Y)->count())
                ->badgeColor('danger'),
        ];
    }
}
