<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            // Ganti validasi 'email' menjadi 'username'
            'username' => [
                'required', 
                'string', 
                'max:255', 
                'alpha_dash', // Hanya boleh huruf, angka, strip, underscore (opsional)
                \Illuminate\Validation\Rule::unique(User::class)->ignore($this->user()->id),
            ],
        ];
    }
}
