<?php

namespace Modules\Chat\Tests\Feature;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Modules\Chat\Model\Messages\Model as Message;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ForceQueueJobAssertAndCleanupTest extends TestCase
{
    #[Test]
    public function verifies_force_queue_job_stops_queue_and_restores_database_state(): void
    {
        $rawSnapshot = Redis::get('test5:db_snapshot');
        $snapshot = $rawSnapshot ? json_decode($rawSnapshot, true) : null;

        $testMessages = Message::where('uuid', 'like', 'test-err-queue-%')->get();
        $latestTestMessage = $testMessages->sortByDesc('id')->first();
        $msgId = $latestTestMessage?->id;

        try {
            $this->assertNotNull(
                $latestTestMessage,
                'Тестовое сообщение от ForceQueueNodeErr.test.js должно существовать перед проверкой очереди'
            );

            // 1. Статус сообщения в БД специально 1
            $this->assertSame(1, (int) $latestTestMessage->status);

            // 2. forceQueue записал статус 1 в Redis
            $this->assertSame('1', (string) Redis::get("msg_status:{$msgId}"));

            // 3. В таблице jobs создана отложенная задача CheckMessageDelivery (воркер уже запущен в laravel-worker)
            $matchedJob = DB::table('jobs')->get()->first(function ($job) use ($msgId) {
                return str_contains($job->payload, 'CheckMessageDelivery')
                    && str_contains($job->payload, 'i:' . $msgId . ';');
            });

            $this->assertNotNull(
                $matchedJob,
                "В таблице jobs должна находиться задача CheckMessageDelivery для message_id={$msgId}"
            );
        } finally {
            // 1. Останавливаем очередь для всех тестовых сообщений (удаляем задачу из jobs и ключ статуса из Redis)
            foreach ($testMessages as $msg) {
                // Сначала ставим статус 3 на случай, если воркер уже успел захватить джобу, затем удаляем запись и ключ
                Redis::setex("msg_status:{$msg->id}", 5, 3);

                DB::table('jobs')
                    ->where('payload', 'like', '%CheckMessageDelivery%')
                    ->where('payload', 'like', '%i:' . $msg->id . ';%')
                    ->delete();

                Redis::del("msg_status:{$msg->id}");
            }

            // 2. Удаляем тестовые сообщения
            Message::where('uuid', 'like', 'test-err-queue-%')->delete();

            // 3. Возвращаем таблицу chats, updated_at и AUTO_INCREMENT строго к состоянию ДО теста 5
            if (is_array($snapshot)) {
                if (isset($snapshot['max_chat_id'])) {
                    DB::table('chats')->where('id', '>', (int) $snapshot['max_chat_id'])->delete();
                }

                if (! empty($snapshot['chats_updated_at']) && is_array($snapshot['chats_updated_at'])) {
                    foreach ($snapshot['chats_updated_at'] as $chatId => $updatedAt) {
                        DB::table('chats')->where('id', (int) $chatId)->update(['updated_at' => $updatedAt]);
                    }
                }

                DB::statement('ALTER TABLE messages AUTO_INCREMENT = ' . (((int) $snapshot['max_message_id']) + 1));
                DB::statement('ALTER TABLE chats AUTO_INCREMENT = ' . (((int) $snapshot['max_chat_id']) + 1));
                DB::statement('ALTER TABLE jobs AUTO_INCREMENT = ' . (((int) $snapshot['max_job_id']) + 1));
            } else {
                $maxMsgId = (int) DB::table('messages')->max('id');
                $maxJobId = (int) DB::table('jobs')->max('id');
                DB::statement('ALTER TABLE messages AUTO_INCREMENT = ' . ($maxMsgId + 1));
                DB::statement('ALTER TABLE jobs AUTO_INCREMENT = ' . ($maxJobId + 1));
            }

            Redis::del('test5:db_snapshot');
        }

        // Финальная проверка: очередь для тестового сообщения полностью остановлена, а БД один в один как до теста
        if ($msgId) {
            $remainingJob = DB::table('jobs')->get()->first(function ($job) use ($msgId) {
                return str_contains($job->payload, 'CheckMessageDelivery')
                    && str_contains($job->payload, 'i:' . $msgId . ';');
            });
            $this->assertNull($remainingJob, 'Очередь CheckMessageDelivery для тестового сообщения должна быть остановлена');
            $this->assertNull(Redis::get("msg_status:{$msgId}"), 'Ключ msg_status в Redis должен быть очищен');
        }

        if (is_array($snapshot)) {
            $this->assertSame($snapshot['messages_count'], DB::table('messages')->count());
            $this->assertSame($snapshot['chats_count'], DB::table('chats')->count());
            $this->assertSame($snapshot['chats_updated_at'], DB::table('chats')->pluck('updated_at', 'id')->all());
        }
    }
}
