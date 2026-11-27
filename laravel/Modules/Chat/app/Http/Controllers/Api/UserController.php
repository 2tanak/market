<?php

namespace Modules\Chat\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Auth\Model\Users\Model as Users;

class UserController extends Controller
{
    public function user(Request $request, ?Users $user = null)
    {
        // dd($user);
        return response()->json(['ok' => $user]);
    }
}
