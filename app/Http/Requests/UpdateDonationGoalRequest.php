<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDonationGoalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isStreamer();
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'target_amount' => ['required', 'numeric', 'min:1000', 'max:100000000'],
            'current_amount' => ['required', 'numeric', 'min:0', 'max:100000000'],
            'bottom_text' => ['nullable', 'string', 'max:255'],
            'theme_id' => ['required', 'string', 'max:32'],
            'progress_style' => ['required', 'string', 'max:32'],
            'effects' => ['nullable', 'array'],
            'favorite_themes' => ['nullable', 'array'],
            'show_title' => ['sometimes', 'boolean'],
            'show_current' => ['sometimes', 'boolean'],
            'show_target' => ['sometimes', 'boolean'],
            'show_percentage' => ['sometimes', 'boolean'],
            'show_bottom_text' => ['sometimes', 'boolean'],
            'celebration_enabled' => ['sometimes', 'boolean'],
            'celebration_type' => ['sometimes', 'required', 'string', 'max:32'],
            'celebration_message' => ['nullable', 'string', 'max:255'],
            'primary_color' => ['required', 'string', 'max:32'],
            'secondary_color' => ['required', 'string', 'max:32'],
            'background_style' => ['required', 'string', 'max:255'],
            'border_style' => ['required', 'string', 'max:255'],
            'border_radius' => ['required', 'integer', 'min:0', 'max:99'],
            'shadow_style' => ['required', 'string', 'max:255'],
            'font_family' => ['required', 'string', 'max:255'],
            'font_weight' => ['required', 'string', 'max:8'],
            'font_color' => ['required', 'string', 'max:32'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'show_title' => $this->boolean('show_title'),
            'show_current' => $this->boolean('show_current'),
            'show_target' => $this->boolean('show_target'),
            'show_percentage' => $this->boolean('show_percentage'),
            'show_bottom_text' => $this->boolean('show_bottom_text'),
            'celebration_enabled' => $this->boolean('celebration_enabled'),
            'effects' => $this->input('effects', []),
            'favorite_themes' => $this->input('favorite_themes', []),
        ]);
    }
}
