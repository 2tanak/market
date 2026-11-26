<?php

namespace Modules\Chat\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Chat\Model\Chats\Model as Chat;
use Modules\Chat\Model\Messages\Model as Message;
use Modules\Chat\Resources\UserResource;

class UsersController extends Controller
{
    public function index(Request $request)
    {
        $myId = (int) auth()->id();
        $page = $request->integer('page', 1);

        $chats = Chat::with([
            'buyer',
            'seller',
            'lastMessage',
        ])
            ->where(function ($query) use ($myId) {
                $query->where('buyer_id', $myId)
                    ->orWhere('seller_id', $myId);
            })
            ->selectRaw(
                'chats.*,
                (
                    SELECT COUNT(*) FROM messages m
                    WHERE m.chat_id = chats.id
                      AND m.sender_id != ?
                      AND m.status = 2
                ) as unread_count,
                (
                    SELECT MAX(created_at) FROM messages m
                    WHERE m.chat_id = chats.id
                ) as last_msg_at',
                [$myId]
            )
            ->orderByRaw('CASE WHEN unread_count > 0 THEN 1 ELSE 0 END DESC')
            ->orderByRaw('CASE WHEN last_msg_at IS NULL THEN 1 ELSE 0 END')
            ->orderBy('last_msg_at', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(20);

        $usersCollection = collect($chats->items())->map(function ($chat) use ($myId) {
            $rawPartner = (int) $chat->buyer_id === $myId ? $chat->seller : $chat->buyer;

            if (!$rawPartner) {
                return null;
            }

            $partner = clone $rawPartner;
            $lastMessage = $chat->lastMessage;

            $partner->listing_id = $chat->listing_id;
            $partner->unread_count_custom = (int) $chat->unread_count;
            $partner->current_chat_id = $chat->id;
            $partner->last_message_id = $lastMessage?->id;
            $partner->last_message_text = $lastMessage?->text;
            $partner->last_message_status = $lastMessage?->status;
            $partner->last_message_text_at = $lastMessage?->created_at?->toDateTimeString();

            return $partner;
        })->filter()->values();

        return response()->json([
            'data' => UserResource::collection($usersCollection),
            'pagination' => [
                'current_page' => $chats->currentPage(),
                'has_more' => $chats->hasMorePages(),
                'page' => $page,
            ],
        ]);
    }

    public function totalUnread()
    {
        $myId = (int) auth()->id();

        $count = Message::whereHas('chat', function ($query) use ($myId) {
            $query->where('buyer_id', $myId)
                ->orWhere('seller_id', $myId);
        })
            ->where('sender_id', '!=', $myId)
            ->where('status', 2)
            ->count();

        return response()->json([
            'total_unread' => $count,
        ]);
    }
}
