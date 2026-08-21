<?php

namespace App\Filament\Resources\BannerResource\Pages;

use App\Filament\Resources\BannerResource;
use App\Models\Banner;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListBanners extends ListRecords
{
    protected static string $resource = BannerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Tất cả')
                ->badge(fn () => Banner::count()),

            'hero_slider' => Tab::make('🌟 Slide Trang Chủ')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('position', Banner::POSITION_HERO_SLIDER))
                ->badge(fn () => Banner::where('position', Banner::POSITION_HERO_SLIDER)->count())
                ->badgeColor('primary'),

            'home_promo_2col' => Tab::make('🏷️ Khuyến Mãi 2 Cột')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('position', Banner::POSITION_HOME_PROMO_2COL))
                ->badge(fn () => Banner::where('position', Banner::POSITION_HOME_PROMO_2COL)->count())
                ->badgeColor('warning'),

            'home_collection_3col' => Tab::make('🛋️ Bộ Sưu Tập 3 Cột')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('position', Banner::POSITION_HOME_COLLECTION_3COL))
                ->badge(fn () => Banner::where('position', Banner::POSITION_HOME_COLLECTION_3COL)->count())
                ->badgeColor('info'),

            'catalog_header' => Tab::make('📦 Header Catalog')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('position', Banner::POSITION_CATALOG_HEADER))
                ->badge(fn () => Banner::where('position', Banner::POSITION_CATALOG_HEADER)->count())
                ->badgeColor('success'),

            'blog_sidebar' => Tab::make('📰 Blog Sidebar')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('position', Banner::POSITION_BLOG_SIDEBAR))
                ->badge(fn () => Banner::where('position', Banner::POSITION_BLOG_SIDEBAR)->count())
                ->badgeColor('gray'),

            'top_announcement' => Tab::make('📢 Thông Báo Header')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('position', Banner::POSITION_TOP_ANNOUNCEMENT))
                ->badge(fn () => Banner::where('position', Banner::POSITION_TOP_ANNOUNCEMENT)->count())
                ->badgeColor('danger'),
        ];
    }
}
