<?php

namespace Modules\Admin\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class WebAuth
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Пример кастомной логики проверки (например, проверка кастомной сессии или флага blocked)
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Доступ запрещен.');
        }

        // Пример проверки поля 'blocked' из вашей модели Users
        if (Auth::user()->blocked) {
            Auth::logout();
            return redirect()->route('login')->with('error', 'Ваш аккаунт заблокирован.');
        }

        return $next($request);
    }
}
