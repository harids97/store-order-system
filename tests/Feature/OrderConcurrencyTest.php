<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class OrderConcurrencyTest extends TestCase
{
    use DatabaseMigrations;

    public function test_only_one_order_succeeds_when_two_requests_compete_for_last_item(): void
    {
        $product = Product::factory()->create([
            'price' => 1000,
            'tax_percentage' => 18,
            'stock' => 1,
        ]);

        // Give both PHP processes time to boot, then make them
        // attempt the order at approximately the same moment.
        $startAt = microtime(true) + 2;

        $script = base_path('tests/Support/attempt_order.php');

        $commands = [
            [
                PHP_BINARY,
                $script,
                (string) $product->id,
                'concurrent1@example.com',
                (string) $startAt,
            ],
            [
                PHP_BINARY,
                $script,
                (string) $product->id,
                'concurrent2@example.com',
                (string) $startAt,
            ],
        ];

        $environment = array_merge(
            getenv(),
            [
                'APP_ENV' => 'testing',
                'QUEUE_CONNECTION' => 'sync',
            ]
        );

        $processes = [];

        foreach ($commands as $command) {
            $descriptors = [
                0 => ['pipe', 'r'],
                1 => ['pipe', 'w'],
                2 => ['pipe', 'w'],
            ];

            $process = proc_open(
                $command,
                $descriptors,
                $pipes,
                base_path(),
                $environment
            );

            $this->assertIsResource($process);

            fclose($pipes[0]);

            $processes[] = [
                'process' => $process,
                'stdout' => $pipes[1],
                'stderr' => $pipes[2],
            ];
        }

        $results = [];

        foreach ($processes as $processData) {
            $stdout = stream_get_contents($processData['stdout']);
            $stderr = stream_get_contents($processData['stderr']);

            fclose($processData['stdout']);
            fclose($processData['stderr']);

            $exitCode = proc_close($processData['process']);

            $this->assertSame(
                0,
                $exitCode,
                "Child process failed: {$stderr}"
            );

            $result = json_decode(trim($stdout), true);

            $this->assertNotNull(
                $result,
                "Invalid child process output: {$stdout}"
            );

            $results[] = $result;
        }

        $statuses = array_column($results, 'status');

        sort($statuses);

        $this->assertSame([
            'failed',
            'success',
        ], $statuses);

        $this->assertDatabaseCount('orders', 1);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock' => 0,
        ]);
    }
}