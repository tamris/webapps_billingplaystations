<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class UpdatePlaystationRequest extends FormRequest {
    public function authorize(): bool { return Auth::check(); }
    public function rules(): array {
        return [
            'code' => [
                'required','string','max:20',
                Rule::unique('playstations','code')->ignore($this->route('playstation')->id)
            ],
            'name' => 'nullable|string|max:100',
            'status' => 'in:available,in_use,maintenance',
            'price_per_hour' => 'required|numeric|min:0',
        ];
    }
}
