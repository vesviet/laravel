<?php

use App\Models\User;
use App\Models\SellerProfile;
use App\Models\Order;
use App\Policies\SellerOrderPolicy;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('DEBUG2: trace Filament panel in test', function () {
    $user = User::factory()->create();
    $seller = SellerProfile::factory()->create(['user_id' => $user->id, 'status' => 'published']);
    $order = Order::factory()->create(['seller_id' => $seller->id]);
    $user->refresh();

    $panel = Filament::getCurrentPanel();
    dump([
        'panel' => $panel,
        'panel_id' => $panel?->getId(),
        'isSellerPanel_result' => ($panel === null || $panel->getId() === 'seller'),
    ]);

    $profile = $user->sellerProfile()->withoutGlobalScopes()->first();
    dump([
        'profile_id' => $profile?->id,
        'isActive' => $profile?->isActive(),
        'order_seller_id' => $order->seller_id,
        'id_match' => (int)$order->seller_id === (int)$profile?->id,
    ]);

    $policy = new SellerOrderPolicy();
    dump(['view_result' => $policy->view($user, $order)]);

    expect(true)->toBeTrue(); // always pass, just need the dumps
});
