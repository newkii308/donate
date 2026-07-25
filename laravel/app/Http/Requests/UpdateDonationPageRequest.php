<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDonationPageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->streamer !== null;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'allow_anonymous' => filter_var($this->input('allow_anonymous'), FILTER_VALIDATE_BOOL),
            'show_recent_donations' => filter_var($this->input('show_recent_donations'), FILTER_VALIDATE_BOOL),
        ]);

        if (is_string($this->input('quick_amounts'))) {
            $amounts = collect(explode(',', $this->input('quick_amounts')))
                ->map(fn ($v) => (int) trim($v))
                ->filter(fn ($v) => $v > 0)
                ->values()
                ->all();
            $this->merge(['quick_amounts' => $amounts]);
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'title' => ['nullable', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:1000'],
            'min_amount' => ['required', 'numeric', 'min:1'],
            'max_amount' => ['nullable', 'numeric', 'gte:min_amount'],
            'quick_amounts' => ['nullable', 'array', 'max:8'],
            'quick_amounts.*' => ['integer', 'min:1'],
            'allow_anonymous' => ['boolean'],
            'show_recent_donations' => ['boolean'],
            'thank_you_message' => ['nullable', 'string', 'max:255'],
            'theme' => ['required', Rule::in(['dark', 'light'])],
            'accent_color' => ['required', 'string', 'regex:/^#[0-9a-fA-F]{6}$/'],
        ];
    }
}
