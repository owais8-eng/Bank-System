<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class Accountrequest extends FormRequest
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
            'user_id' => 'required|exists:users,id',
            'type' => 'required|in:savings,checking,loan,investment',
            'initial_balance' => 'numeric|min:0',
            'parent_id' => 'nullable|exists:accounts,id',
            'nickname' => 'nullable|string',
            'daily_limit' => 'nullable|numeric|min:0',
        ];
    }
}
