<?php

namespace Tests\Feature;

use App\Jobs\ServiceHealthProbeJob;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redis;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ServiceHealthTest extends TestCase
{
    #[Test]
    public function queue_worker_is_running_and_processing_jobs(): void
    {
        // Переключаем очередь с sync (из phpunit.xml) на реальную database,
        // чтобы задачу подхватил именно контейнер laravel-worker
        config(['queue.default' => 'database']);

        $maxJobIdBefore = (int) DB::table('jobs')->max('id');
        $probeKey = 'healthcheck:worker:' . uniqid();

        try {
            ServiceHealthProbeJob::dispatch($probeKey);

            $result = null;
            $deadline = microtime(true) + 5.0;

            while (microtime(true) < $deadline) {
                $result = Redis::get($probeKey);
                if ($result === 'alive') {
                    break;
                }
                usleep(200_000);
            }

            $this->assertSame(
                'alive',
                $result,
                'Сервис queue-worker (laravel-worker) НЕ работает: тестовая задача в очереди не была обработана за 5 секунд'
            );
        } finally {
            Redis::del($probeKey);
            DB::table('jobs')->where('payload', 'like', '%' . $probeKey . '%')->delete();
            $maxAfter = (int) DB::table('jobs')->max('id');
            DB::statement('ALTER TABLE jobs AUTO_INCREMENT = ' . (max($maxJobIdBefore, $maxAfter) + 1));
        }
    }

    #[Test]
    public function node_server_is_running_and_responding(): void
    {
        $httpResponse = Http::retry(5, 500, throw: false)
            ->timeout(3)
            ->get('http://node:3000/test');

        $this->assertTrue(
            $httpResponse->successful() && trim($httpResponse->body()) === '11111111',
            'Сервис Node server.js (:3000) НЕ отвечает на /test'
        );

        $socketResponse = Http::retry(3, 300, throw: false)
            ->timeout(3)
            ->get('http://node:3000/socket.io/?EIO=4&transport=polling');

        $this->assertTrue(
            $socketResponse->successful() && str_contains($socketResponse->body(), '"sid"'),
            'Socket.IO на сервере Node (:3000) НЕ отвечает на handshake'
        );
    }

    #[Test]
    public function vite_dev_server_is_running_and_responding(): void
    {
        $this->assertFileExists(
            public_path('hot'),
            'Файл public/hot отсутствует — Vite dev-сервер (npm run dev) не запущен'
        );

        $viteResponse = Http::retry(5, 500, throw: false)
            ->timeout(3)
            ->withHeaders([
                'Host' => '127.0.0.1:5173',
                'Origin' => config('app.url', 'http://127.0.0.1'),
            ])
            ->get('http://node:5173/@vite/client');

        $this->assertTrue(
            $viteResponse->successful(),
            'Сервис Vite (:5173) НЕ запущен или не отдаёт /@vite/client'
        );
    }
}
