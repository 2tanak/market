<?php

require __DIR__ . '/../../../../vendor/autoload.php';

$app = require_once __DIR__ . '/../../../../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

$deadline = microtime(true) + 15.0;

while (microtime(true) < $deadline) {
    if (Redis::get('test7:done')) {
        break;
    }

    $msgId = Redis::lpop('test7:wakeup_job');
    if ($msgId) {
        $msgId = (int) $msgId;
        $waitUntil = microtime(true) + 3.0;

        // Ждём, пока отложенная задача CheckMessageDelivery лежит в jobs (не захвачена воркером) и сдвигаем available_at на сейчас
        while (microtime(true) < $waitUntil) {
            $job = DB::table('jobs')
                ->whereNull('reserved_at')
                ->where('payload', 'like', '%CheckMessageDelivery%')
                ->where('payload', 'like', '%i:' . $msgId . ';%')
                ->first();

            if ($job) {
                Redis::setex("test7:saw_job:{$msgId}", 60, '1');
                DB::table('jobs')
                    ->where('id', $job->id)
                    ->update(['available_at' => time() - 1]);
                break;
            }

            usleep(100_000);
        }
    }

    usleep(100_000);
}

Redis::del('test7:done');
Redis::del('test7:wakeup_job');
