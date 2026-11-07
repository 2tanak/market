<?php

namespace Modules\Auth\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Auth\Http\Requests\AuthRequest;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{

    public function index()
    {
        return view('auth::login');
    }

    public function check_login(AuthRequest $request)
    {
       
        try {


            if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
				
                $user = $request->user();

                if ($request->ajax()) {

                    $user = $request->user();
                    return response()->json([
                        'success' => true,
                        'user'    => $user
                    ], 201);
                }

                return redirect()->route('admin_index')->with('success', trans('messages.auth.successMessage'));
            }else{
				 return redirect()->route('login')->with('error', 'Неверные данные');
			}
        } catch (Exception) {


            return redirect()->route('login')->with('error', trans('messages.auth.errorException'));
        }
    }
    function logout(Request $request)
    {


        Auth::logout();
        if ($request->lang) {
            return redirect('/' . $request->lang . '/');
        } else {
            return redirect('/');
        }
    }
}
