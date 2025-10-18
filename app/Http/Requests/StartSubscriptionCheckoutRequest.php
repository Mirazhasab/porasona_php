<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StartSubscriptionCheckoutRequest extends FormRequest
{
    public const PAYMENT_TYPE_BKASH = 'bkash_online';
    public const PAYMENT_TYPE_MANUAL = 'manual';

    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'subscription_id' => ['required', 'exists:subscriptions,id'],
            'payment_type' => ['required', 'in:' . implode(',', [self::PAYMENT_TYPE_BKASH, self::PAYMENT_TYPE_MANUAL])],
            'manual_transaction_reference' => ['required_if:payment_type,' . self::PAYMENT_TYPE_MANUAL, 'string', 'max:255'],
            'manual_notes' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
