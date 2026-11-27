<?php

namespace Modules\Chat\Tests\Feature;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Modules\Chat\Model\Messages\Model as Message;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PingSuccessAssertAndCleanupTest extends TestCase
{
    #[Test]
    public function verifies_ping_success_updated_status_to_two_without_queue_and_restores_db(): void
    {
        $rawSnapshot = Redis::get('test6:db_snapshot');
        $snapshot = $rawSnapshot ? json_decode($rawSnapshot, true) : null;

        $testMessages = Message::where('uuid', 'like', 'test-ok-ping-%')->get();
        $latestTestMessage = $testMessages->sortByDesc('id')->first();
        $msgId = $latestTestMessage?->id;

        try {
            $this->assertNotNull(
                $latestTestMessage,
                'Тестовое сообщение от PingSuccessNodeOk.test.js должно существовать перед проверкой БД'
            );

            // 1. /api/ping_success (updateStatus) обновил статус сообщения в MySQL на 2
            $this->assertSame(2, (int) $latestTestMessage->status);

            // 2. В Redis статус также равен 2
            $this->assertSame('2', (string) Redis::get("msg_status:{$msgId}"));

            // 3. По варианту ok в Node очередь CheckMessageDelivery НЕ должна запускаться
            $queuedJob = DB::table('jobs')->get()->first(function ($job) use ($msgId) {
                return str_contains($job->payload, 'CheckMessageDelivery')
                    && str_contains($job->payload, 'i:' . $msgId . ';');
            });

            $this->assertNull(
                $queuedJob,
                "По ветке ok (/api/ping_success) задача CheckMessageDelivery НЕ должна создаваться для message_id={$msgId}"
            );
        } finally {
            // Чистим Redis, возможные остатки в jobs и тестовое сообщение
            foreach ($testMessages as $msg) {
                Redis::del("msg_status:{$msg->id}");

                DB::table('jobs')
                    ->where('payload', 'like', '%CheckMessageDelivery%')
                    ->where('payload', 'like', '%i:' . $msg->id . ';%')
                    ->delete();
            }

            Message::where('uuid', 'like', 'test-ok-ping-%')->delete();

            // Возвращаем БД и AUTO_INCREMENT строго к состоянию ДО теста 6
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

            Redis::del('test6:db_snapshot');
        }

        // Контрольная проверка восстановления исходного состояния БД и Redis
        if ($msgId) {
            $this->assertNull(Redis::get("msg_status:{$msgId}"));
        }

        if (is_array($snapshot)) {
            $this->assertSame($snapshot['messages_count'], DB::table('messages')->count());
            $this->assertSame($snapshot['chats_count'], DB::table('chats')->count());
            $this->assertSame($snapshot['chats_updated_at'], DB::table('chats')->pluck('updated_at', 'id')->all());
        }
    }
}
