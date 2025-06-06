<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePlaceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:62'],
            'city' => ['nullable', 'string', 'max:62'],
            'state' => ['nullable', 'string', 'max:62'],
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
            'name.required' => 'O nome é obrigatório.',
            'name.string' => 'O nome deve ser uma sequência de caracteres.',
            'name.max' => 'O nome não pode ter mais que 62 caracteres.',

            'city.string' => 'A cidade deve ser uma sequência de caracteres.',
            'city.max' => 'A cidade não pode ter mais que 62 caracteres.',

            'state.string' => 'O estado deve ser uma sequência de caracteres.',
            'state.max' => 'O estado não pode ter mais que 62 caracteres.',
        ];
    }
}
