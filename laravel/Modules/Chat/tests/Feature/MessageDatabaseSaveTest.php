<?php

namespace Modules\Chat\Tests\Feature;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Laravel\Sanctum\Sanctum;
use Modules\Auth\Model\Users\Model as User;
use Modules\Chat\Events\Privates;
use Modules\Chat\Model\Chats\Model as Chat;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class MessageDatabaseSaveTest extends TestCase
{
    #[Test]
    public function saves_message_in_real_database_via_message_route_and_restores_db_state(): void
    {
        Event::fake([Privates::class]);

        // Проверяем, что маршрут message зарегистрирован и ведёт на /api/message
        $routeUrl = route('api.message', [], false);
        $this->assertSame('/api/message', $routeUrl);

        // Фиксируем точное состояние таблиц ДО теста
        $messagesCountBefore = DB::table('messages')->count();
        $chatsCountBefore = DB::table('chats')->count();
        $maxMessageIdBefore = (int) DB::table('messages')->max('id');
        $maxChatIdBefore = (int) DB::table('chats')->max('id');

        $testUuid = 'test-db-save-' . uniqid();
        $testText = 'Тестовая запись сообщения через маршрут /api/message';

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
                    'text' => $testText,
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
                $chatUpdatedBefore = null;

                Sanctum::actingAs($sender);

                $payload = [
                    'uuid' => $testUuid,
                    'text' => $testText,
                    'listing_id' => $listing->id,
                    'seller_id' => $seller->id,
                ];
            }

            // Отправляем запрос строго по маршруту /api/message
            $response = $this->postJson($routeUrl, $payload);

            $response->assertOk()
                ->assertJsonPath('status', 'success')
                ->assertJsonPath('message.uuid', $testUuid)
                ->assertJsonPath('message.text', $testText)
                ->assertJsonPath('message.sender_id', $sender->id)
                ->assertJsonPath('message.status', 1);

            // Убеждаемся, что запись РЕАЛЬНО появилась в таблице messages в MySQL
            $this->assertSame($messagesCountBefore + 1, DB::table('messages')->count());
            $this->assertDatabaseHas('messages', [
                'uuid' => $testUuid,
                'sender_id' => $sender->id,
                'text' => $testText,
                'status' => 1,
            ]);
        } finally {
            // Откатываем транзакцию, чтобы не оставить никаких изменений в БД
            DB::rollBack();

            // Возвращаем счётчики AUTO_INCREMENT на прежнее значение
            DB::statement('ALTER TABLE messages AUTO_INCREMENT = ' . ($maxMessageIdBefore + 1));
            DB::statement('ALTER TABLE chats AUTO_INCREMENT = ' . ($maxChatIdBefore + 1));
        }

        // Проверяем, что после теста база осталась один в один такой же, как была до теста
        $this->assertSame($messagesCountBefore, DB::table('messages')->count());
        $this->assertSame($chatsCountBefore, DB::table('chats')->count());
        $this->assertDatabaseMissing('messages', ['uuid' => $testUuid]);

        if (isset($existingChat) && $existingChat && $chatUpdatedBefore !== null) {
            $this->assertSame($chatUpdatedBefore, (string) Chat::find($existingChat->id)->updated_at);
        }
    }
}
