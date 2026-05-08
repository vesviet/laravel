<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $name = $this->faker->unique()->sentence(3);
        return [
            'category_id' => Category::factory(),
            'sku' => strtoupper(Str::random(8)),
            'name' => $name,
            'slug' => Str::slug($name),
            'price' => $this->faker->numberBetween(1000000, 50000000),
            'stock' => $this->faker->numberBetween(0, 50),
            'is_active' => true,
        ];
    }
}
