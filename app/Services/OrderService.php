<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Jobs\SendOrderConfirmation;

class OrderService
{
    public function createOrder(array $data): Order
    {
        return DB::transaction(function () use ($data) {

            $customer = Customer::firstOrCreate(
                ['email' => $data['customer']['email']],
                ['name' => $data['customer']['name']]
            );

            $items = collect($data['items'])
                ->sortBy('product_id')
                ->values()
                ->all();

            $products = [];

            // foreach ($data['items'] as $item) {
            foreach ($items as $item) {
                $product = Product::where('id', $item['product_id'])
                    ->lockForUpdate()
                    ->first();

                if (!$product) {
                    throw ValidationException::withMessages([
                        'items' => ['One of the selected products does not exist.'],
                    ]);
                }

                if ($product->stock < $item['quantity']) {
                    throw ValidationException::withMessages([
                        'items' => [
                            "Insufficient stock for product: {$product->name}"
                        ],
                    ]);
                }

                $products[] = [
                    'product' => $product,
                    'quantity' => $item['quantity'],
                ];
            }

            $subtotal = 0;
            $tax = 0;

            foreach ($products as $item) {
                $product = $item['product'];
                $quantity = $item['quantity'];

                // $lineSubtotal = $product->price * $quantity;
                // $lineTax = ($lineSubtotal * $product->tax_percentage) / 100;

                $lineSubtotal = round($product->price * $quantity, 2);

                $lineTax = round(
                    ($lineSubtotal * $product->tax_percentage) / 100,
                    2
                );

                $subtotal += $lineSubtotal;
                $tax += $lineTax;
            }

            // $grandTotal = $subtotal + $tax;

            $subtotal = round($subtotal, 2);
            $tax = round($tax, 2);
            $grandTotal = round($subtotal + $tax, 2);

            $order = Order::create([
                'customer_id' => $customer->id,
                'subtotal' => $subtotal,
                'tax' => $tax,
                'grand_total' => $grandTotal,
            ]);

            foreach ($products as $item) {
                $product = $item['product'];
                $quantity = $item['quantity'];

                // $lineSubtotal = $product->price * $quantity;
                // $lineTax = ($lineSubtotal * $product->tax_percentage) / 100;
                // $lineTotal = $lineSubtotal + $lineTax;

                $lineSubtotal = round($product->price * $quantity, 2);

                $lineTax = round(
                    ($lineSubtotal * $product->tax_percentage) / 100,
                    2
                );

                $lineTotal = round($lineSubtotal + $lineTax, 2);

                $order->items()->create([
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_price' => $product->price,
                    'tax_percentage' => $product->tax_percentage,
                    'subtotal' => $lineSubtotal,
                    'tax' => $lineTax,
                    'total' => $lineTotal,
                ]);

                $product->decrement('stock', $quantity);
            }

            SendOrderConfirmation::dispatch($order)->afterCommit();
            // return $order->load('customer', 'items.product');
            return $order->fresh()->load('customer', 'items.product');

        });
    }
}