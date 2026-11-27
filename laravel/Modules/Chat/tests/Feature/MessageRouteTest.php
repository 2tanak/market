<?php

namespace Modules\Chat\Tests\Feature;

use Illuminate\Http\Request;
use Laravel\Sanctum\Sanctum;
use Mockery;
use Mockery\MockInterface;
use Modules\Auth\Model\Users\Model as User;
use Modules\Chat\Http\Controllers\Api\MessageController;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class MessageRouteTest extends TestCase
{
    #[Test]
    public function post_api_message_hits_message_controller_action(): void
    {
        $user = new User();
        $user->id = 1;
        Sanctum::actingAs($user);

        $this->mock(MessageController::class, function (MockInterface $mock) {
            $mock->shouldReceive('message')
                ->once()
                ->with(Mockery::type(Request::class))
                ->andReturn(response()->json(['reached' => true]));
        });

        $response = $this->postJson('/api/message', [
            'uuid' => 'test-uuid-1',
            'text' => 'Тестовое сообщение',
        ]);

        $response->assertOk()
            ->assertExactJson(['reached' => true]);
    }

    #[Test]
    public function post_api_message_requires_sanctum_auth(): void
    {
        $response = $this->postJson('/api/message', [
            'uuid' => 'test-uuid-1',
            'text' => 'Тестовое сообщение',
        ]);

        $response->assertUnauthorized();
    }
}
