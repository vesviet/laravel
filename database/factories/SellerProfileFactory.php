<?php

namespace Database\Factories;

use App\Models\SellerProfile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class SellerProfileFactory extends Factory
{
    protected $model = SellerProfile::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'shop_name' => $this->faker->company(),
            'subdomain' => $this->faker->unique()->slug(),
            'phone' => $this->faker->phoneNumber(),
            'email' => $this->faker->companyEmail(),
            'status' => 'active',
            'telegram_chat_id' => $this->faker->randomNumber(8, true),
        ];
    }
}
