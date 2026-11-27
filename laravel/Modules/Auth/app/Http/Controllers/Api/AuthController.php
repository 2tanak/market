<?php

namespace Modules\Auth\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Modules\Auth\Http\Requests\AuthRequest;
use Modules\Auth\Model\Users\Model as User;
use Modules\Auth\Services\TokenService;

class AuthController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(User $user)
    {
        try {
            return response()->json([
                'success' => true,
                'user' => $user,
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'error' => $e,
            ], 401);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function login(AuthRequest $request, TokenService $tokenService)
    {

        $data = $request->validated();
        // return response()->json(['data'=>'fffffffff']);
        if ($request->has('device_name')) {
            $extra = $request->validate([
                'device_name' => 'required|string|max:255',
            ]);

            // 3. Склеиваем два массива (основной + доп. поле)
            $data = array_merge($data, $extra);
        }

        try {
            if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
                $request->session()->regenerate();
                $user = $request->user();
                $token = $tokenService->verify_token($user, $request->device_name ?? 'web-access');
                session(['api_token' => $token]);

                return response()->json([
                    'user' => $user,
                    'token' => $token,
                ]);

            }

            return response()->json(['message' => 'Unauthorized'], 401);
        } catch (Exception $e) {
            return response()->json([
                'error' => $e,
            ], 401);
        }

    }

    public function logout(Request $request)
    {
        try {
            Auth::logout();

            return response()->json([
                'success' => true,
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'error' => $e,
            ], 422);
        }
    }

    public function register(AuthRequest $request) // RedirectResponse
    {
        try {
            $requestData = $request->all();
            User::create($requestData);

            return response()->json([
                'success' => true,
            ], 201);

        } catch (Exception $e) {
            return response()->json([
                'error' => $e,
            ], 422);
        }
    }
}
