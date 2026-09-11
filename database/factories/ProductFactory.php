<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $products = [
        'Samsung Galaxy S24',
        'OnePlus Nord CE 4',
        'Redmi Note 13',
        'Realme Narzo 70',
        'boAt Rockerz Headphones',
        'Noise Smart Watch',
        'Ambrane Power Bank',
        'Portronics Wireless Mouse',
        'Zebronics Keyboard',
        'Syska LED Bulb',
        'Prestige Electric Kettle',
        'Bajaj Mixer Grinder',
        'Havells Table Fan',
        'Milton Water Bottle',
        'Cello Lunch Box',
        'Wildcraft Backpack',
        'Campus Running Shoes',
        'Allen Solly Shirt',
        'Classmate Notebook',
        'Reynolds Ball Pen',
    ];

        return [
            'name' => fake()->unique()->randomElement($products),
            'code' => fake()->unique()->bothify('PRD-####'),
            'price' => fake()->randomFloat(2, 100, 5000),
            'tax_percentage' => fake()->randomElement([5, 12, 18]),
            'stock' => fake()->numberBetween(0, 100),
        ];
    }
}
