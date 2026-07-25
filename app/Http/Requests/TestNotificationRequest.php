<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TestNotificationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->streamer !== null;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'donor_name' => ['required', 'string', 'max:60'],
            'amount' => ['required', 'numeric', 'min:1000', 'max:'.config('newlab.donation.max_amount', 100_000_000)],
            'message' => ['nullable', 'string', 'max:255'],
        ];
    }
}
