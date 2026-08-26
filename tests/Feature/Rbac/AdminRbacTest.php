<?php

use App\Models\Customer;
use App\Models\Order;
use App\Models\User;
use App\Policies\RolePolicy;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Support\Facades\Gate;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

function makeOrder(): Order
{
    $order = Order::create([
        'customer_id' => Customer::factory()->create()->id,
        'order_number' => 'ORD-RBAC-001',
        'status' => 'confirmed',
        'payment_method' => 'cod',
        'customer_name' => 'Nguyen Van An',
        'phone' => '0901234567',
        'email' => 'rbac-order@example.com',
        'address' => '10 Street',
        'city' => 'HCMC',
        'subtotal' => 100000,
        'discount_amount' => 0,
        'shipping_fee' => 20000,
        'total_amount' => 120000,
    ]);

    $order->items()->create([
        'product_name' => 'Test Chair',
        'sku' => 'TST-001',
        'price_at_purchase' => 100000,
        'quantity' => 1,
        'is_flash_sale' => false,
    ]);

    return $order;
}

// ── R1: Role-based panel access ─────────────────────────────────────────────

test('user without any role cannot access admin panel', function () {
    expect(User::factory()->create(['email' => 'staff@example.com'])->canAccessPanel(filament()->getPanel('admin')))->toBeFalse();
});

test('user with panel_user role can access admin panel', function () {
    expect(User::factory()->panelUser()->create()->canAccessPanel(filament()->getPanel('admin')))->toBeTrue();
});

test('user with super_admin role can access admin panel', function () {
    expect(User::factory()->superAdmin()->create()->canAccessPanel(filament()->getPanel('admin')))->toBeTrue();
});

test('admin panel roles list is configurable', function () {
    config(['auth.admin_roles' => ['custom_admin']]);

    $role = Role::findOrCreate('custom_admin', 'web');

    expect(User::factory()->afterCreating(fn (User $u) => $u->assignRole($role))->create()->canAccessPanel(filament()->getPanel('admin')))->toBeTrue();
});

// ── R3: Gate::before hardening ──────────────────────────────────────────────

test('gate before bypass does not fatal for non user authenticatable and falls through to deny', function () {
    $customer = Customer::factory()->create();

    expect(Gate::forUser($customer)->allows('view_any_order'))->toBeFalse();
});

test('super_admin gate bypass grants all abilities without explicit permissions', function () {
    $superAdmin = User::factory()->superAdmin()->create();

    expect(Gate::forUser($superAdmin)->allows('view_any_order'))->toBeTrue();
});

test('regular user without permissions is denied by gates', function () {
    $user = User::factory()->create();

    expect(Gate::forUser($user)->allows('view_any_order'))->toBeFalse();
});

// ── R5: Order invoice PDF authorization ─────────────────────────────────────

test('guest cannot download order invoice pdf', function () {
    $order = makeOrder();

    $this->get(route('admin.orders.pdf', $order))->assertRedirect(route('login'));
});

test('authenticated user without view_order permission gets 403 on invoice pdf', function () {
    $order = makeOrder();

    $this->actingAs(User::factory()->panelUser()->create())
        ->get(route('admin.orders.pdf', $order))
        ->assertForbidden();
});

test('user with view_order permission can download invoice pdf', function () {
    Permission::findOrCreate('view_order', 'web');
    $order = makeOrder();

    $response = $this->actingAs(User::factory()->afterCreating(function (User $user) {
        $user->givePermissionTo('view_order');
    })->create())->get(route('admin.orders.pdf', $order));

    expect($response->status())->toBe(200);
    expect($response->headers->get('content-type'))->toContain('application/pdf');
});

test('super_admin can download invoice pdf without explicit permission', function () {
    $order = makeOrder();

    $response = $this->actingAs(User::factory()->superAdmin()->create())
        ->get(route('admin.orders.pdf', $order));

    expect($response->status())->toBe(200);
    expect($response->headers->get('content-type'))->toContain('application/pdf');
});

test('unknown order id returns 404 not 403 to avoid leaking existence', function () {
    $this->actingAs(User::factory()->superAdmin()->create())
        ->get('/admin/orders/999999/pdf')
        ->assertNotFound();
});

// ── R2: RolePolicy placeholder fixes ────────────────────────────────────────

test('role policy maps every ability to a real permission name', function () {
    $permissions = collect([
        'view_any_role', 'view_role', 'create_role', 'update_role',
        'delete_role', 'delete_any_role', 'force_delete_role',
        'force_delete_any_role', 'restore_role', 'restore_any_role',
        'replicate_role', 'reorder_role',
    ])->each(fn ($name) => Permission::findOrCreate($name, 'web'));

    $granted = User::factory()->afterCreating(fn (User $u) => $u->givePermissionTo(
        $permissions->all(),
    ))->create();

    foreach (['forceDelete', 'forceDeleteAny', 'restore', 'restoreAny', 'replicate', 'reorder'] as $ability) {
        expect(app(RolePolicy::class)->{$ability}($granted, new Role))->toBeTrue();
    }
});

test('role policy denies restore and replicate for users holding only view permission', function () {
    Permission::findOrCreate('view_role', 'web');
    $viewer = User::factory()->afterCreating(fn (User $u) => $u->givePermissionTo('view_role'))->create();

    expect(app(RolePolicy::class)->restore($viewer, new Role))->toBeFalse();
    expect(app(RolePolicy::class)->replicate($viewer, new Role))->toBeFalse();
});

// ── R4: RolesAndPermissionsSeeder ───────────────────────────────────────────

test('roles and permissions seeder creates both admin roles idempotently', function () {
    $this->seed(RolesAndPermissionsSeeder::class);
    $this->seed(RolesAndPermissionsSeeder::class);

    expect(Role::where('name', config('filament-shield.super_admin.name'))->count())->toBe(1);
    expect(Role::where('name', config('filament-shield.panel_user.name'))->count())->toBe(1);
});

test('seeder syncs generated permissions to super_admin role', function () {
    // Simulate shield-generated resource permissions existing in DB.
    Permission::findOrCreate('view_any_banner', 'web');
    Permission::findOrCreate('update_order', 'web');

    $this->seed(RolesAndPermissionsSeeder::class);

    $superAdmin = Role::findByName(config('filament-shield.super_admin.name'), 'web');

    expect($superAdmin->hasPermissionTo('view_any_banner'))->toBeTrue();
    expect($superAdmin->hasPermissionTo('update_order'))->toBeTrue();
});

test('seeder keeps super_admin powers when shield generation fails', function () {
    Permission::findOrCreate('view_any_post', 'web');

    // Force the Artisan call inside the seeder to fail.
    Artisan::shouldReceive('call')
        ->withArgs(fn ($command) => $command === 'shield:generate')
        ->andThrow(new RuntimeException('boom'));

    app(RolesAndPermissionsSeeder::class)->run();

    expect(Role::findByName(config('filament-shield.super_admin.name'), 'web')->hasPermissionTo('view_any_post'))->toBeTrue();
});

// ── Deploy cutover: admin:grant backfill command ────────────────────────────

test('admin grant command assigns role and grants panel access idempotently', function () {
    $user = User::factory()->create(['email' => 'legacy-admin@example.com']);

    $this->artisan('admin:grant', ['email' => 'legacy-admin@example.com'])->assertSuccessful();
    $this->artisan('admin:grant', ['email' => 'legacy-admin@example.com'])->assertSuccessful();

    expect($user->fresh()->hasRole(config('filament-shield.super_admin.name')))->toBeTrue();
    expect($user->fresh()->canAccessPanel(filament()->getPanel('admin')))->toBeTrue();
});

test('admin grant command rejects roles outside configured admin roles', function () {
    User::factory()->create(['email' => 'victim@example.com']);

    $this->artisan('admin:grant', [
        'email' => 'victim@example.com',
        '--role' => 'customer',
    ])->assertFailed();

    expect(User::where('email', 'victim@example.com')->first()->hasAnyRole(['customer']))->toBeFalse();
});

test('admin grant command fails cleanly for unknown email', function () {
    $this->artisan('admin:grant', ['email' => 'ghost@example.com'])->assertFailed();
});
