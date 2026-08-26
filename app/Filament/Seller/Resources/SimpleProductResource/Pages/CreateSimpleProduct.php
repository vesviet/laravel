<?php

namespace App\Filament\Seller\Resources\SimpleProductResource\Pages;

use App\Filament\Seller\Resources\SimpleProductResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateSimpleProduct extends CreateRecord
{
    protected static string $resource = SimpleProductResource::class;
    
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['seller_id'] = auth()->user()->sellerProfile->id;
        $data['is_visible'] = true;
        $data['status'] = 'published';
        
        return $data;
    }
}
