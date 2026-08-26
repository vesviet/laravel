<?php

namespace App\Filament\Seller\Resources\SellerPageResource\Pages;

use App\Filament\Seller\Resources\SellerPageResource;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;

class ListSellerPages extends ListRecords
{
    protected static string $resource = SellerPageResource::class;

    public function mount(): void
    {
        parent::mount();

        $page = auth()->user()->sellerProfile->pages()->first();
        if ($page) {
            redirect()->to(SellerPageResource::getUrl('edit', ['record' => $page->id]));
            return;
        }
    }
}
