<?php

namespace Modules\Chat\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Chat\Events\Privates;
use Modules\Chat\Model\Messages\Model as Messages;
use Modules\Chat\Resources\MessagesResource;

class MessagesController extends Controller
{
    public function messages(Request $request)
    {
        $page = max(1, (int) ($request->page ?? 1));
        $result = Messages::filter($request)->paginate(2, ['*'], 'page', $page);

        return response()->json([
            'data' => MessagesResource::collection($result),
            'pagination' => [
                'current_page' => $result->currentPage(),
                'has_more' => $result->hasMorePages(),
            ],
            'request' => [
                'page' => $page,
                'created_at' => $request->created_at,
                'receiver_id' => $request->receiver_id,
                'newm' => $request->newm,
            ],
        ]);
    }

public function getUnreadCounters()
{
    $userId = auth()->id();
 $unreads = Messages::where('receiver_id', $userId)
        ->where('is_read', 0)
        ->selectRaw('sender_id, count(*) as total')
        ->groupBy('sender_id')
        ->pluck('total', 'sender_id');

    // 2. Достаем последние сообщения для каждого чата (срез)
    // Используем подзапрос, чтобы найти последние ID для пар (sender, receiver)
    $lastMessages = Messages::where('receiver_id', $userId)
        ->orWhere('sender_id', $userId)
        ->latest()
        ->get()
        ->unique(function ($item) use ($userId) {
            // Группируем по ID собеседника
            return $item->sender_id == $userId ? $item->receiver_id : $item->sender_id;
        })
        ->values();

    return response()->json([
        'unread_counts' => $unreads,
        'last_messages' => $lastMessages
    ]);
   
}


	

}
