<?php

namespace Modules\Chat\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Auth\Model\Users\Model as Users;

class ChatController extends Controller
{
    public function chat(Request $request, ?Users $user = null)
    {
        // dd($user);
        return view('chat::chat', [
            'user' => $user,

        ]);
    }

    public function chat_test(Request $request, ?Users $user = null)
    {
        // dd($user);
        return response()->json(['ok' => $user]);
    }
}
