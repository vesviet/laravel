<?php

use App\Enums\OrderStatus;
use App\Models\Category;
use App\Models\Customer;
use App\Models\CustomerAddress;
use App\Models\Order;
use App\Models\Product;
use App\Models\Province;
use App\Services\CartService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->category = Category::create([
        "name" => "Decor",
        "slug" => "decor",
    ]);

    $this->product = Product::create([
        "name" => "Ceramic Vase",
        "slug" => "ceramic-vase",
        "sku" => "VASE-001",
        "price" => 500000,
        "stock" => 10,
        "category_id" => $this->category->id,
        "status" => "published",
    ]);
    $this->province = Province::create([
        "name" => "Ha Noi",
        "code" => "HN",
    ]);
});

describe("Checkout Flow - Guest", function () {
    test("redirects to products when accessing checkout with empty cart", function () {
        $this->get("/checkout")
            ->assertRedirect("/products");
    });

    test("guest can complete checkout with valid data", function () {
        $cartService = app(CartService::class);
        $cartService->add($this->product->id, null, 2);

        $response = $this->post("/checkout", [
            "shippingData" => [
                "customer_name" => "Nguyen Van A",
                "phone" => "0912345678",
                "email" => "guest@example.com",
                "address" => "123 Nguyen Hue",
                "city" => "Ho Chi Minh",
                "district" => "District 1",
                "ward" => "Ben Nghe",
            ],
            "payment_method" => "cod",
        ]);

        $order = Order::where("email", "guest@example.com")->first();
        expect($order)->not->toBeNull()
            ->and($order->status)->toBe(\App\Enums\OrderStatus::Pending)
            ->and($order->subtotal)->toBe(1000000)
            ->and($order->customer_name)->toBe("Nguyen Van A");

        $response->assertRedirect();
    });

    test("guest checkout validates required fields", function () {
        $cartService = app(CartService::class);
        $cartService->add($this->product->id, null, 1);

        $this->post("/checkout", [])
            ->assertSessionHasErrors([
                "shippingData.customer_name",
                "shippingData.phone",
                "shippingData.address",
                "shippingData.city",
                "shippingData.district",
                "shippingData.ward",
                "payment_method",
            ]);
    });

    test("guest checkout rejects invalid phone format", function () {
        $cartService = app(CartService::class);
        $cartService->add($this->product->id, null, 1);

        $this->post("/checkout", [
            "shippingData" => [
                "customer_name" => "Test",
                "phone" => "123",
                "address" => "123 Test",
                "city" => "Hanoi",
                "district" => "District 1",
                "ward" => "Ward 1",
            ],
            "payment_method" => "cod",
        ])->assertSessionHasErrors("shippingData.phone");
    });
});

describe("Checkout Flow - Authenticated Customer", function () {
    beforeEach(function () {
        $this->customer = Customer::factory()->create([
            "email" => "customer@example.com",
            "password" => bcrypt("password123"),
        ]);
        $this->actingAs($this->customer, "customer");
    });

    test("pre-fills form with customer profile data", function () {
        $customer = Customer::factory()->create([
            "name" => "Profile Name",
            "phone" => "0987654321",
            "email" => "profile@example.com",
        ]);
        $this->actingAs($customer, "customer");

        $cartService = app(CartService::class);
        $cartService->add($this->product->id, null, 1);

        $response = $this->get("/checkout");
        expect($response->assertOk())->toBeTrue();
    });

    test("uses saved address when available", function () {
        $address = \App\Models\CustomerAddress::factory()->create([
            "customer_id" => $this->customer->id,
            "type" => "shipping",
            "is_default" => true,
            "recipient_name" => "Saved Name",
            "phone" => "0900111222",
            "address_line_1" => "456 Saved St",
            "city" => "Ha Noi",
            "district" => "Ba Dinh",
            "ward" => "Ward 1",
        ]);

        $cartService = app(CartService::class);
        $cartService->add($this->product->id, null, 1);

        $this->get("/checkout")
            ->assertOk();
    });

    test("completes order with COD payment", function () {
        $cartService = app(CartService::class);
        $cartService->add($this->product->id, null, 1);

        $response = $this->post("/checkout", [
            "shippingData" => [
                "customer_name" => "Test User",
                "phone" => "0912345678",
                "address" => "123 Test St",
                "city" => "Ha Noi",
                "district" => "Ba Dinh",
                "ward" => "Ward 1",
            ],
            "payment_method" => "cod",
        ]);

        $order = Order::where("customer_name", "Test User")->first();
        expect($order)->not->toBeNull()
            ->and($order->payment_method)->toBe("cod")
            ->and($order->status)->toBe(\App\Enums\OrderStatus::Pending);

        $response->assertRedirect(route("checkout.success", ["order_number" => $order->order_number]));
    });

    test("deducts inventory on successful order", function () {
        $cartService = app(CartService::class);
        $cartService->add($this->product->id, null, 3);

        $this->post("/checkout", [
            "shippingData" => [
                "customer_name" => "Test",
                "phone" => "0912345678",
                "address" => "123 Test",
                "city" => "Ha Noi",
                "district" => "Ba Dinh",
                "ward" => "Ward 1",
            ],
            "payment_method" => "cod",
        ]);

        $this->product->refresh();
        expect($this->product->stock)->toBe(7);
    });

    test("calculates 5% combo discount for 2+ items", function () {
        $cartService = app(CartService::class);
        $cartService->add($this->product->id, null, 2);

        $this->post("/checkout", [
            "shippingData" => [
                "customer_name" => "Test",
                "phone" => "0912345678",
                "address" => "123 Test",
                "city" => "Ha Noi",
                "district" => "Ba Dinh",
                "ward" => "Ward 1",
            ],
            "payment_method" => "cod",
        ]);

        $order = Order::where("customer_name", "Test")->first();
        expect($order)->not->toBeNull()
            ->and($order->discount_amount)->toBe(50000);
    });
});

describe("Checkout Flow - Payment Methods", function () {
    beforeEach(function () {
        $this->customer = Customer::factory()->create();
        $this->actingAs($this->customer, "customer");
        $cartService = app(CartService::class);
        $cartService->add($this->product->id, null, 1);
    });

    test("accepts COD payment method", function () {
        $this->post("/checkout", [
            "shippingData" => [
                "customer_name" => "Test",
                "phone" => "0912345678",
                "address" => "123 Test",
                "city" => "Ha Noi",
                "district" => "Ba Dinh",
                "ward" => "Ward 1",
            ],
            "payment_method" => "cod",
        ])->assertRedirect();
    });

    test("accepts VNPAY payment method", function () {
        $this->post("/checkout", [
            "shippingData" => [
                "customer_name" => "Test",
                "phone" => "0912345678",
                "address" => "123 Test",
                "city" => "Ha Noi",
                "district" => "Ba Dinh",
                "ward" => "Ward 1",
            ],
            "payment_method" => "vnpay",
        ])->assertRedirect();
    });

    test("accepts MoMo payment method", function () {
        $this->post("/checkout", [
            "shippingData" => [
                "customer_name" => "Test",
                "phone" => "0912345678",
                "address" => "123 Test",
                "city" => "Ha Noi",
                "district" => "Ba Dinh",
                "ward" => "Ward 1",
            ],
            "payment_method" => "momo",
        ])->assertRedirect();
    });

    test("accepts banking payment method", function () {
        $this->post("/checkout", [
            "shippingData" => [
                "customer_name" => "Test",
                "phone" => "0912345678",
                "address" => "123 Test",
                "city" => "Ha Noi",
                "district" => "Ba Dinh",
                "ward" => "Ward 1",
            ],
            "payment_method" => "banking",
        ])->assertRedirect();
    });

    test("rejects invalid payment method", function () {
        $this->post("/checkout", [
            "shippingData" => [
                "customer_name" => "Test",
                "phone" => "0912345678",
                "address" => "123 Test",
                "city" => "Ha Noi",
                "district" => "Ba Dinh",
                "ward" => "Ward 1",
            ],
            "payment_method" => "invalid_method",
        ])->assertSessionHasErrors("payment_method");
    });
});

describe("Checkout Flow - Success Page", function () {
    beforeEach(function () {
        $this->customer = Customer::factory()->create();
    });

    test("shows success page for order owner", function () {
        $order = Order::factory()->create([
            "customer_id" => $this->customer->id,
            "order_number" => "ORD-TEST-001",
        ]);
        $this->actingAs($this->customer, "customer");

        $this->get("/checkout/success/{$order->order_number}")
            ->assertOk()
            ->assertViewIs("storefront.checkout.success");
    });

    test("shows success page with valid session", function () {
        $order = Order::factory()->create(["order_number" => "ORD-TEST-002"]);

        $this->withSession(["checkout_completed" => "ORD-TEST-002"])
            ->get("/checkout/success/{$order->order_number}")
            ->assertOk();
    });

    test("denies access to other customer order", function () {
        $customer2 = Customer::factory()->create();
        $order = Order::factory()->create([
            "customer_id" => $customer2->id,
            "order_number" => "ORD-TEST-003",
        ]);
        $this->actingAs($this->customer, "customer");

        $this->get("/checkout/success/{$order->order_number}")
            ->assertForbidden();
    });
});
