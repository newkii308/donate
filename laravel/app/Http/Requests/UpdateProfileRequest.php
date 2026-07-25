<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
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
        $streamerId = $this->user()->streamer->id;
        $avatar = config('newlab.media.avatar');
        $qr = config('newlab.media.qr');

        return [
            'display_name' => ['required', 'string', 'max:60'],
            'username' => [
                'required', 'string', 'min:3', 'max:30', 'alpha_dash',
                Rule::unique('streamers', 'username')->ignore($streamerId),
            ],
            'description' => ['nullable', 'string', 'max:1000'],
            'currency' => ['required', 'string', Rule::in(config('newlab.currencies'))],
            'truewallet_phone' => ['nullable'],
            'payment_method' => ['nullable', 'string', 'max:60'],
            'account_name' => ['nullable', 'string', 'max:120'],
            'account_number' => ['nullable', 'string', 'max:60'],
            'bank_name' => ['nullable', 'string', 'max:120'],
            'avatar' => ['nullable', 'image', 'mimes:'.implode(',', $avatar['mimes']), 'max:'.$avatar['max_kb']],
            'qr_code' => ['nullable', 'image', 'mimes:'.implode(',', $qr['mimes']), 'max:'.$qr['max_kb']],
        ];
    }

}
