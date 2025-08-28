<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StorePlaystationRequest extends FormRequest {
    public function authorize(): bool { return Auth::check(); }
    public function rules(): array {
        return [
            'code' => 'required|string|max:20|unique:playstations,code',
            'name' => 'nullable|string|max:100',
            'status' => 'in:available,in_use,maintenance',
            'price_per_hour' => 'required|numeric|min:0',
        ];
    }
}
