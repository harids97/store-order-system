<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StoreOrderRequest;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use App\Models\Customer;
use App\Models\Product;


class OrderController extends Controller
{
    public function __construct(
        private OrderService $orderService
    ) {
    }

    public function store(StoreOrderRequest $request): JsonResponse
    {
        $order = $this->orderService->createOrder(
            $request->validated()
        );

        return response()->json([
            'message' => 'Order created successfully.',
            'data' => $order,
        ], 201);
    }

    public function history(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $customer = Customer::where('email', $request->email)
            ->first();

        if (!$customer) {
            return response()->json([
                'message' => 'Customer not found.',
            ], 404);
        }

        $orders = $customer->orders()
            ->with('items.product')
            ->latest()
            ->get();

        return response()->json([
            'customer' => $customer,
            'orders' => $orders,
        ]);
    }

    public function lowStock(): JsonResponse
    {
        // $threshold = config('inventory.low_stock_threshold');
        $threshold = (int) config('inventory.low_stock_threshold');

        $products = Product::where('stock', '<', $threshold)
            ->orderBy('stock')
            ->get();

        return response()->json([
            'threshold' => $threshold,
            'products' => $products,
        ]);
    }

    public function products(): JsonResponse
    {
        $products = Product::orderBy('name')->get();

        return response()->json($products);
    }
}
