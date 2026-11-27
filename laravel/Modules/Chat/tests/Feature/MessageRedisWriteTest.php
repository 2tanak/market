<?php

namespace Modules\Chat\Tests\Feature;

use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Redis;
use Laravel\Sanctum\Sanctum;
use Modules\Auth\Model\Users\Model as User;
use Modules\Chat\Events\Privates;
use Modules\Chat\Jobs\CheckMessageDelivery;
use Modules\Chat\Model\Chats\Model as Chat;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class MessageRedisWriteTest extends TestCase
{
    #[Test]
    public function writes_and_updates_msg_status_key_in_real_redis_and_restores_state(): void
    {
        Event::fake([Privates::class]);
        Bus::fake([CheckMessageDelivery::class]);

        $messagesCountBefore = DB::table('messages')->count();
        $chatsCountBefore = DB::table('chats')->count();
        $maxMessageIdBefore = (int) DB::table('messages')->max('id');
        $maxChatIdBefore = (int) DB::table('chats')->max('id');

        $testUuid = 'test-redis-write-' . uniqid();
        $redisKey = null;
        $existingChat = null;
        $chatUpdatedBefore = null;

        DB::beginTransaction();

        try {
            $existingChat = Chat::first();

            if ($existingChat) {
                $sender = User::findOrFail($existingChat->buyer_id);
                $sellerId = $existingChat->seller_id;
                $chatUpdatedBefore = (string) $existingChat->updated_at;

                Sanctum::actingAs($sender);

                $payload = [
                    'uuid' => $testUuid,
                    'text' => 'Тест 8: проверка записи в Redis по ключу msg_status:{msgId}',
                    'chat_id' => $existingChat->id,
                    'seller_id' => $sellerId,
                ];
            } else {
                $users = User::limit(2)->get();
                $this->assertGreaterThanOrEqual(2, $users->count(), 'В БД должно быть минимум 2 пользователя');

                $listing = DB::table('listings')->first();
                $this->assertNotNull($listing, 'В БД должно быть минимум 1 объявление в listings');

                $sender = $users[0];
                $seller = $users[1];

                Sanctum::actingAs($sender);

                $payload = [
                    'uuid' => $testUuid,
                    'text' => 'Тест 8: проверка записи в Redis по ключу msg_status:{msgId}',
                    'listing_id' => $listing->id,
                    'seller_id' => $seller->id,
                ];
            }

            // Создаём сообщение в транзакции, чтобы получить реальный $msgId
            $messageResponse = $this->postJson('/api/message', $payload);
            $messageResponse->assertOk()->assertJsonPath('status', 'success');

            $msgId = $messageResponse->json('message.id');
            $this->assertNotEmpty($msgId);

            $redisKey = "msg_status:{$msgId}";
            Redis::del($redisKey);

            // До вызова /api/ping или /api/ping_success ключа в Redis нет
            $this->assertNull(Redis::get($redisKey));

            // 1. Вызов POST /api/ping (MessageController::forceQueue) должен записать "1" в msg_status:{$msgId}
            $pingResponse = $this->postJson('/api/ping', ['message_id' => $msgId]);
            $pingResponse->assertOk()->assertJsonPath('status', 'job_started');

            $this->assertSame(1, (int) Redis::exists($redisKey), "Ключ {$redisKey} должен появиться в Redis");
            $this->assertSame('1', (string) Redis::get($redisKey), "В ключ {$redisKey} должен записаться статус 1");

            $ttl = (int) Redis::ttl($redisKey);
            $this->assertGreaterThan(0, $ttl, "У ключа {$redisKey} должен быть установлен TTL");
            $this->assertLessThanOrEqual(86400, $ttl);

            // Повторный /api/ping видит уже записанный ключ в Redis и отвечает already_processed
            $duplicatePing = $this->postJson('/api/ping', ['message_id' => $msgId]);
            $duplicatePing->assertOk()->assertJsonPath('status', 'already_processed');
            $this->assertSame('1', (string) Redis::get($redisKey));

            // 2. Вызов POST /api/ping_success (MessageController::updateStatus) обновляет msg_status:{$msgId} на "2"
            $statusTwoResponse = $this->postJson('/api/ping_success', [
                'message_id' => $msgId,
                'status' => 2,
            ]);
            $statusTwoResponse->assertOk()->assertJsonPath('status', 2);
            $this->assertSame('2', (string) Redis::get($redisKey), "В ключ {$redisKey} должен записаться статус 2");

            // 3. Обновление статуса на "3" (прочитано) и проверка защиты от понижения обратно на "2"
            $statusThreeResponse = $this->postJson('/api/ping_success', [
                'message_id' => $msgId,
                'status' => 3,
            ]);
            $statusThreeResponse->assertOk()->assertJsonPath('status', 3);
            $this->assertSame('3', (string) Redis::get($redisKey), "В ключ {$redisKey} должен записаться статус 3");

            $downgradeResponse = $this->postJson('/api/ping_success', [
                'message_id' => $msgId,
                'status' => 2,
            ]);
            $downgradeResponse->assertOk();
            $this->assertSame('3', (string) Redis::get($redisKey), "Статус в {$redisKey} не должен понижаться с 3 на 2");
        } finally {
            if ($redisKey !== null) {
                Redis::del($redisKey);
            }

            DB::rollBack();

            DB::statement('ALTER TABLE messages AUTO_INCREMENT = ' . ($maxMessageIdBefore + 1));
            DB::statement('ALTER TABLE chats AUTO_INCREMENT = ' . ($maxChatIdBefore + 1));
        }

        // Контрольная проверка: ключ в Redis удалён, а БД полностью вернулась в исходное состояние
        if ($redisKey !== null) {
            $this->assertNull(Redis::get($redisKey));
            $this->assertSame(0, (int) Redis::exists($redisKey));
        }

        $this->assertSame($messagesCountBefore, DB::table('messages')->count());
        $this->assertSame($chatsCountBefore, DB::table('chats')->count());

        if ($existingChat && $chatUpdatedBefore !== null) {
            $this->assertSame($chatUpdatedBefore, (string) Chat::find($existingChat->id)->updated_at);
        }
    }
}
