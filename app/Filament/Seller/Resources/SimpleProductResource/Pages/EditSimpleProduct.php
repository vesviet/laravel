<?php

namespace App\Filament\Seller\Resources\SimpleProductResource\Pages;

use App\Filament\Seller\Resources\SimpleProductResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

/**
 * EditSimpleProduct — Seller Panel product edit page.
 *
 * Authorization is handled by SellerProductPolicy::update() which is evaluated
 * by Filament before rendering this page. The inline beforeSave() ownership
 * check has been removed (SF-02) — the Policy is the single authority on
 * who can edit which product.
 *
 * ADR-S2: All mutations go through parent EditRecord → Eloquent directly.
 * No transaction boundary needed here (single UPDATE, no invariants to maintain).
 */
class EditSimpleProduct extends EditRecord
{
    protected static string $resource = SimpleProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
