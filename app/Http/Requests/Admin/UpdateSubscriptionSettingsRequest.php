<?php

namespace App\Http\Requests\Admin;

use App\Models\UserAccess;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class UpdateSubscriptionSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('manage-subscription-settings');
    }

    public function rules(): array
    {
        $features = implode(',', UserAccess::FEATURE_FIELDS);

        return [
            'free_mcq_limit_per_set' => ['required', 'integer', 'min:1', 'max:500'],
            'free_features' => ['nullable', 'array'],
            'free_features.*' => ['in:' . $features],
            'subscribed_features' => ['nullable', 'array'],
            'subscribed_features.*' => ['in:' . $features],
        ];
    }
}
