<?php

use App\Services\OrderService;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Validation\ValidationException;

require __DIR__ . '/../../vendor/autoload.php';

$app = require __DIR__ . '/../../bootstrap/app.php';

$app->make(Kernel::class)->bootstrap();

$productId = (int) $argv[1];
$email = $argv[2];

$startAt = isset($argv[3])
    ? (float) $argv[3]
    : microtime(true);

while (microtime(true) < $startAt) {
    usleep(1000);
}

$data = [
    'customer' => [
        'name' => 'Concurrent Customer',
        'email' => $email,
    ],
    'items' => [
        [
            'product_id' => $productId,
            'quantity' => 1,
        ],
    ],
];

try {
    $order = app(OrderService::class)->createOrder($data);

    echo json_encode([
        'status' => 'success',
        'order_id' => $order->id,
    ]);
} catch (ValidationException $e) {
    echo json_encode([
        'status' => 'failed',
        'errors' => $e->errors(),
    ]);
}