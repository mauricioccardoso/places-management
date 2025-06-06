<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Symfony\Component\HttpKernel\Exception\HttpException;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $userId = Auth::id();
        if ($userId != $this->route('user')) {
            throw new HttpException(403, 'Não autorizado');
        }

        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $userId = Auth::id();

        return [
            'name' => ['min:3', 'string'],
            'email' => [
                'email',
                Rule::unique('users')->ignore($userId),
            ],
            'password' => [
                'nullable',
                'confirmed',
                Password::min(8)
            ],
            'password_confirmation' => [
                'nullable',
                'required_if:password,!null',
                Password::min(8)
            ],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'email.email' => 'O email deve ser um endereço de email válido.',
            'email.unique' => 'Este email já está registrado.',

            'password.min' => 'A senha deve ter pelo menos :min caracteres.',
            'password_confirmation.required_if' => 'O campo confirmação de senha é obrigatório.',
            'password.confirmed' => 'As senhas não corresponde.',
            'password_confirmation.min' => 'O campo confirmação da senha deve ter pelo menos :min caracteres.',

            '*.min' => 'O campo :attribute deve ter pelo menos :min caracteres.',
            '*.string' => 'O campo :attribute deve ser um conjunto de caracteres.',
        ];
    }
}
