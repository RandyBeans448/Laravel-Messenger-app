<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Ensure authorization logic is handled here if needed
    }

    public function rules(): array
    {
        return [
            'message' => ['required', 'string', 'max:5000'],
            'conversation_id' => ['required', 'integer', 'exists:conversations,id'],
        ];
    }
}
