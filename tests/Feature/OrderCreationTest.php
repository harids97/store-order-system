<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Product;

class OrderCreationTest extends TestCase
{
    /**
     * A basic feature test example.
     */

    use RefreshDatabase;


    public function test_order_can_be_created_and_stock_is_deducted(): void
    {
        $product = Product::factory()->create([
            'price' => 1000,
            'tax_percentage' => 18,
            'stock' => 10,
        ]);

        $response = $this->postJson('/api/orders', [
            'customer' => [
                'name' => 'Test Customer',
                'email' => 'test@example.com',
            ],
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 2,
                ],
            ],
        ]);

        $response
            ->assertStatus(201)
            ->assertJsonPath('message', 'Order created successfully.')
            ->assertJsonPath('data.subtotal', '2000.00')
            ->assertJsonPath('data.tax', '360.00')
            ->assertJsonPath('data.grand_total', '2360.00');
            // ->assertJsonPath('data.subtotal', 2000)
            // ->assertJsonPath('data.tax', 360)
            // ->assertJsonPath('data.grand_total', 2360);

        $this->assertDatabaseHas('orders', [
            'subtotal' => 2000.00,
            'tax' => 360.00,
            'grand_total' => 2360.00,
        ]);

        $this->assertDatabaseHas('order_items', [
            'product_id' => $product->id,
            'quantity' => 2,
            'subtotal' => 2000.00,
            'tax' => 360.00,
            'total' => 2360.00,
        ]);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock' => 8,
        ]);
    }

    public function test_order_fails_when_stock_is_insufficient(): void
    {
        $product = Product::factory()->create([
            'price' => 1000,
            'tax_percentage' => 18,
            'stock' => 1,
        ]);

        $response = $this->postJson('/api/orders', [
            'customer' => [
                'name' => 'Test Customer',
                'email' => 'stocktest@example.com',
            ],
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 2,
                ],
            ],
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['items']);

        // $this->assertDatabaseMissing('orders', [
        //     'customer_id' => 1,
        // ]);

        $this->assertDatabaseCount('orders', 0);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock' => 1,
        ]);
    }
}
