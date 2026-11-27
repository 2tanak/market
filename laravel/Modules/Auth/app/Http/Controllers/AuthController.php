<?php

namespace Modules\Auth\Http\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Auth\Http\Requests\AuthRequest;
use Modules\Auth\Services\TokenService;

class AuthController extends Controller
{
    public function index()
    {
        return view('auth::login');
    }

    public function check_login(AuthRequest $request, TokenService $tokenService)
    {
        try {
            if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
                $request->session()->regenerate();
                $user = $request->user();

                $token = $tokenService->verify_token($user);
                session(['api_token' => $token]);

                if ($request->ajax()) {
                    return response()->json([
                        'success' => true,
                        'user' => $user,
                        'token' => $token,
                    ], 201);
                }

                return redirect()->route('admin_index')->with('success', trans('messages.auth.successMessage'));
            }

            return redirect()->route('login')->with('error', 'Неверные данные');
        } catch (Exception) {
            return redirect()->route('login')->with('error', trans('messages.auth.errorException'));
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->forget('api_token');

        if ($request->lang) {
            return redirect('/'.$request->lang.'/');
        }

        return redirect('/');
    }
}
