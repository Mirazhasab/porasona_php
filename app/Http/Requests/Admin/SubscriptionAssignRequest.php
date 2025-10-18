<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class SubscriptionAssignRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('manage-subscriptions');
    }

    public function rules(): array
    {
        return [
            'user_id' => ['required', 'exists:users,id'],
            'subscription_id' => ['required', 'exists:subscriptions,id'],
            'starts_at' => ['required', 'date'],
            'expires_at' => ['required', 'date', 'after:starts_at'],
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }
}
