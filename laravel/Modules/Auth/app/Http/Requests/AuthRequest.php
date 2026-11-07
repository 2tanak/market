<?php
namespace Modules\Auth\Http\Requests;


use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Route;

/**
 * User Request
 *
 * @property-read string $email
 * @property-read string $password
 */
class AuthRequest extends FormRequest
{
    /**
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
		
        $rules = [
            'email'    => ['required', 'email'],
            'password' => ['required', 'string', 'min:8'],
			
        ];

        if (Route::currentRouteName() === 'registration_save') {
            $rules['email'][]    = 'unique:users';
            $rules['password'][] = 'confirmed';
            $rules['name']       = [
                'required',
                'string',
                'max:255',
                'min:3',
            ];
        }

        return $rules;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'email.required'     => 'поле email обязательно к заполнению',
            'email.unique'       => 'такой email уже есть',
            'email.email'        => 'Не правильный email',
            'password.min'       => 'пароль не менее :min символов',
            'password.confirmed' => 'Пароли не совподают',
        ];
    }
}
