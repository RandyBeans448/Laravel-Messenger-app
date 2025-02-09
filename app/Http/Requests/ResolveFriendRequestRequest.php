<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ResolveFriendRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Change this based on authorization logic
    }

    public function rules(): array
    {
        return [
            'friendRequestId' => ['required', 'string', 'exists:friend_requests,id'],
            'response' => ['required', 'boolean'],
        ];
    }
}
