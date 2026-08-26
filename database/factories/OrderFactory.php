<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    protected $model = Order::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'customer_id' => Customer::factory(),
            'order_number' => 'ORD-'.strtoupper($this->faker->unique()->bothify('??????')),
            'status' => 'pending',
            'payment_method' => 'cod',
            'customer_name' => $this->faker->name(),
            'phone' => '0901234567',
            'email' => $this->faker->safeEmail(),
            'address' => $this->faker->streetAddress(),
            'city' => 'Ho Chi Minh',
            'district' => 'District 1',
            'ward' => 'Ben Nghe',
            'subtotal' => 100000,
            'discount_amount' => 0,
            'shipping_fee' => 20000,
            'total_amount' => 120000,
        ];
    }
}
