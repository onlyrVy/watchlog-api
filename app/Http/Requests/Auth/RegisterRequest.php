<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Anyone can attempt to register — no auth check needed here.
        return true;
    }

    public function rules(): array
    {
        return [
            'username' => ['required', 'string', 'min:3', 'max:30', 'unique:users,username'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }
}