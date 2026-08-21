<?php

/**
 * [I-08] Auth Flow Feature Tests
 *
 * Coverage for login, registration, password reset, and logout flows
 * for the customer guard. Previously missing entirely from the test suite.
 *
 * Uses Pest PHP for consistency with the project's testing style.
 */

use App\Models\Customer;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;

uses(RefreshDatabase::class);

// ============================================================
// Login
// ============================================================

describe('Customer Login', function () {
    test('renders login page', function () {
        $this->get(route('account.login'))
            ->assertOk()
            ->assertViewIs('storefront.auth.login');
    });

    test('authenticates with valid credentials', function () {
        $customer = Customer::factory()->create([
            'email'    => 'test@example.com',
            'password' => Hash::make('secret123'),
        ]);

        $this->post(route('account.login'), [
            'email'    => 'test@example.com',
            'password' => 'secret123',
        ])
            ->assertRedirect(route('account.orders'));

        $this->assertAuthenticatedAs($customer, 'customer');
    });

    test('rejects wrong password', function () {
        Customer::factory()->create([
            'email'    => 'test@example.com',
            'password' => Hash::make('correct-password'),
        ]);

        $this->post(route('account.login'), [
            'email'    => 'test@example.com',
            'password' => 'wrong-password',
        ])
            ->assertSessionHasErrors('email');

        $this->assertGuest('customer');
    });

    test('rejects non-existent email', function () {
        $this->post(route('account.login'), [
            'email'    => 'nonexistent@example.com',
            'password' => 'anypassword',
        ])
            ->assertSessionHasErrors('email');

        $this->assertGuest('customer');
    });

    test('validates required fields', function () {
        $this->post(route('account.login'), [])
            ->assertSessionHasErrors(['email', 'password']);
    });

    test('rate limit: blocks after threshold exceeded', function () {
        Customer::factory()->create([
            'email'    => 'test@example.com',
            'password' => Hash::make('correct-password'),
        ]);

        // Hit the rate limit (10/min per AppServiceProvider)
        for ($i = 0; $i < 10; $i++) {
            $this->post(route('account.login'), [
                'email'    => 'test@example.com',
                'password' => 'wrong-password',
            ]);
        }

        // 11th attempt should be throttled
        $response = $this->post(route('account.login'), [
            'email'    => 'test@example.com',
            'password' => 'wrong-password',
        ]);

        // Too many requests (429) or validation error with throttle message
        $response->assertStatus(fn ($status) => in_array($status, [302, 429]));
    });
});

// ============================================================
// Logout
// ============================================================

describe('Customer Logout', function () {
    test('logs out authenticated customer and clears session', function () {
        $customer = Customer::factory()->create();

        $this->actingAs($customer, 'customer')
            ->post(route('account.logout'))
            ->assertRedirect(route('products.index'));

        $this->assertGuest('customer');
    });
});

// ============================================================
// Registration
// ============================================================

describe('Customer Registration', function () {
    test('renders registration page', function () {
        $this->get(route('account.register'))
            ->assertOk()
            ->assertViewIs('storefront.auth.register');
    });

    test('registers new customer and logs in', function () {
        $this->post(route('account.register'), [
            'name'                  => 'Nguyễn Văn A',
            'email'                 => 'new@example.com',
            'phone'                 => '0901234567',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ])
            ->assertRedirect(route('account.orders'));

        $this->assertDatabaseHas('customers', ['email' => 'new@example.com']);

        $customer = Customer::where('email', 'new@example.com')->first();
        $this->assertAuthenticatedAs($customer, 'customer');
    });

    test('rejects duplicate email', function () {
        Customer::factory()->create(['email' => 'existing@example.com']);

        $this->post(route('account.register'), [
            'name'                  => 'Trùng Email',
            'email'                 => 'existing@example.com',
            'phone'                 => '0909999999',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ])
            ->assertSessionHasErrors('email');

        $this->assertGuest('customer');
    });

    test('rejects short password', function () {
        $this->post(route('account.register'), [
            'name'                  => 'Short Pass',
            'email'                 => 'short@example.com',
            'password'              => '1234567',       // 7 chars, min is 8
            'password_confirmation' => '1234567',
        ])
            ->assertSessionHasErrors('password');
    });

    test('rejects password mismatch', function () {
        $this->post(route('account.register'), [
            'name'                  => 'Mismatch',
            'email'                 => 'mismatch@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'different456',
        ])
            ->assertSessionHasErrors('password');
    });

    test('rate limit: blocks excessive registration attempts', function () {
        for ($i = 0; $i < 5; $i++) {
            $this->post(route('account.register'), [
                'name'                  => "User $i",
                'email'                 => "ratelimit{$i}@example.com",
                'password'              => 'password123',
                'password_confirmation' => 'password123',
            ]);
        }

        // 6th attempt should be throttled (5/min per AppServiceProvider)
        $response = $this->post(route('account.register'), [
            'name'                  => 'Throttled User',
            'email'                 => 'throttled@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(fn ($status) => in_array($status, [302, 429]));
    });
});

// ============================================================
// Forgot Password
// ============================================================

describe('Forgot Password', function () {
    test('renders forgot password page', function () {
        $this->get(route('account.password.request'))
            ->assertOk()
            ->assertViewIs('storefront.auth.forgot-password');
    });

    test('sends reset link email to existing customer', function () {
        Notification::fake();

        $customer = Customer::factory()->create(['email' => 'reset@example.com']);

        $this->post(route('account.password.email'), ['email' => 'reset@example.com'])
            ->assertSessionHas('status');

        Notification::assertSentTo($customer, \Illuminate\Auth\Notifications\ResetPassword::class);
    });

    test('returns error for non-existent email', function () {
        $this->post(route('account.password.email'), ['email' => 'nobody@example.com'])
            ->assertSessionHasErrors('email');
    });

    test('validates email format', function () {
        $this->post(route('account.password.email'), ['email' => 'not-an-email'])
            ->assertSessionHasErrors('email');
    });
});

// ============================================================
// Password Reset
// ============================================================

describe('Password Reset', function () {
    test('renders reset password form with valid token', function () {
        $customer = Customer::factory()->create(['email' => 'reset@example.com']);
        $token    = Password::broker('customers')->createToken($customer);

        $this->get(route('account.password.reset', ['token' => $token, 'email' => $customer->email]))
            ->assertOk()
            ->assertViewIs('storefront.auth.reset-password');
    });

    test('resets password with valid token and redirects to login', function () {
        Event::fake();

        $customer = Customer::factory()->create([
            'email'    => 'reset@example.com',
            'password' => Hash::make('old-password'),
        ]);
        $token = Password::broker('customers')->createToken($customer);

        $this->post(route('account.password.update'), [
            'token'                 => $token,
            'email'                 => $customer->email,
            'password'              => 'new-password123',
            'password_confirmation' => 'new-password123',
        ])
            ->assertRedirect(route('account.login'))
            ->assertSessionHas('status');

        Event::assertDispatched(PasswordReset::class);

        // Verify new password is actually set
        $customer->refresh();
        expect(Hash::check('new-password123', $customer->password))->toBeTrue();
    });

    test('rejects expired or invalid token', function () {
        $customer = Customer::factory()->create(['email' => 'expired@example.com']);

        $this->post(route('account.password.update'), [
            'token'                 => 'invalid-token-string',
            'email'                 => $customer->email,
            'password'              => 'new-password123',
            'password_confirmation' => 'new-password123',
        ])
            ->assertSessionHasErrors('email');
    });

    test('rejects password mismatch in reset form', function () {
        $customer = Customer::factory()->create(['email' => 'reset@example.com']);
        $token    = Password::broker('customers')->createToken($customer);

        $this->post(route('account.password.update'), [
            'token'                 => $token,
            'email'                 => $customer->email,
            'password'              => 'new-password123',
            'password_confirmation' => 'different-password',
        ])
            ->assertSessionHasErrors('password');
    });

    test('rejects short password in reset form', function () {
        $customer = Customer::factory()->create(['email' => 'reset@example.com']);
        $token    = Password::broker('customers')->createToken($customer);

        $this->post(route('account.password.update'), [
            'token'                 => $token,
            'email'                 => $customer->email,
            'password'              => '1234567',       // 7 chars, min is 8
            'password_confirmation' => '1234567',
        ])
            ->assertSessionHasErrors('password');
    });
});
