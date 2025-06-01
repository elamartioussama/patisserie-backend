<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
{
    return [
        'name' => $this->faker->words(2, true),
        'description' => $this->faker->sentence(),
        'category' => $this->faker->randomElement(['Gâteau', 'Viennoiserie', 'Tarte']),
        'price' => $this->faker->randomFloat(2, 5, 50),
        'stock' => $this->faker->numberBetween(0, 100),
        'image' => 'img/default.png', // Une image par défaut
    ];
}
}
