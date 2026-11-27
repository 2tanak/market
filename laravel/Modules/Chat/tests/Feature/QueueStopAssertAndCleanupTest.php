<?php

namespace Modules\Chat\Tests\Feature;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Modules\Chat\Model\Messages\Model as Message;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class QueueStopAssertAndCleanupTest extends TestCase
{
    #[Test]
    public function verifies_worker_stopped_queue_and_wiped_jobs_and_redis_then_restores_db(): void
    {
        $rawSnapshot = Redis::get('test7:db_snapshot');
        $snapshot = $rawSnapshot ? json_decode($rawSnapshot, true) : null;

        $testMessages = Message::where('uuid', 'like', 'test-stop-queue-%')->get();
        $latestTestMessage = $testMessages->sortByDesc('id')->first();
        $msgId = $latestTestMessage?->id;

        try {
            $this->assertNotNull(
                $latestTestMessage,
                'Тестовое сообщение от QueueStopOnStatusTwo.test.js должно существовать для проверки'
            );

            // 1. Подтверждаем, что задача реально была в таблице jobs до остановки
            $this->assertSame(
                '1',
                (string) Redis::get("test7:saw_job:{$msgId}"),
                'Задача CheckMessageDelivery должна была реально существовать в таблице jobs'
            );

            // 2. Статус сообщения в БД обновлён на 2
            $this->assertSame(2, (int) $latestTestMessage->status);

            // 3. Проверяем ДО любой ручной очистки: сама джоба CheckMessageDelivery стёрла ключ msg_status в Redis
            $this->assertNull(
                Redis::get("msg_status:{$msgId}"),
                'Ключ msg_status в Redis должен быть уже удалён самой джобой CheckMessageDelivery'
            );

            // 4. Проверяем ДО любой ручной очистки: сама очередь остановилась и стёрла запись из таблицы jobs
            $remainingJob = DB::table('jobs')->get()->first(function ($job) use ($msgId) {
                return str_contains($job->payload, 'CheckMessageDelivery')
                    && str_contains($job->payload, 'i:' . $msgId . ';');
            });

            $this->assertNull(
                $remainingJob,
                "Таблица jobs уже должна быть пуста от задачи message_id={$msgId} (воркер сам остановил очередь)"
            );
        } finally {
            // Страховочная зачистка и полный возврат состояния БД к слепку ДО Теста 7
            foreach ($testMessages as $msg) {
                Redis::del("msg_status:{$msg->id}");
                Redis::del("test7:saw_job:{$msg->id}");

                DB::table('jobs')
                    ->where('payload', 'like', '%CheckMessageDelivery%')
                    ->where('payload', 'like', '%i:' . $msg->id . ';%')
                    ->delete();
            }

            Redis::del('test7:done');
            Redis::del('test7:wakeup_job');
            Message::where('uuid', 'like', 'test-stop-queue-%')->delete();

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

            Redis::del('test7:db_snapshot');
        }

        // Финальная контрольная проверка, что база вернулась один в один в исходное состояние
        if (is_array($snapshot)) {
            $this->assertSame($snapshot['messages_count'], DB::table('messages')->count());
            $this->assertSame($snapshot['chats_count'], DB::table('chats')->count());
            $this->assertSame($snapshot['jobs_count'], DB::table('jobs')->count());
            $this->assertSame($snapshot['chats_updated_at'], DB::table('chats')->pluck('updated_at', 'id')->all());
        }
    }
}
