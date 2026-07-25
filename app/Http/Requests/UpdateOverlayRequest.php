<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateOverlayRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->streamer !== null;
    }

    protected function prepareForValidation(): void
    {
        foreach (['shadow', 'show_avatar', 'sound_enabled', 'tts_enabled'] as $bool) {
            $this->merge([$bool => filter_var($this->input($bool), FILTER_VALIDATE_BOOL)]);
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $streamerId = $this->user()->streamer->id;

        $mediaOwned = fn (string $type) => Rule::exists('media', 'id')
            ->where('streamer_id', $streamerId)
            ->where('type', $type)
            ->whereNull('deleted_at');

        return [
            // ธีมสำเร็จรูป
            'theme' => ['required', 'string', Rule::in(array_keys((array) config('newlab.overlay.themes')))],

            // โครงการ์ดแจ้งเตือน (layout)
            'alert_style' => ['required', 'string', Rule::in(array_keys((array) config('newlab.overlay.alert_styles')))],

            // Typography
            'font_family' => ['required', 'string', 'max:120'],
            'font_size' => ['required', 'integer', 'min:10', 'max:96'],
            'font_weight' => ['required', 'string', Rule::in(['300', '400', '500', '600', '700', '800', '900'])],
            'font_color' => ['required', 'string', 'max:32'],

            // Box
            'background_color' => ['required', 'string', 'max:64'],
            'border_radius' => ['required', 'integer', 'min:0', 'max:80'],
            'shadow' => ['boolean'],
            'position' => ['required', Rule::in([
                'top-left', 'top-center', 'top-right',
                'middle-center', 'bottom-left', 'bottom-center', 'bottom-right',
            ])],
            'width' => ['required', 'integer', 'min:200', 'max:1920'],
            'height' => ['nullable', 'integer', 'min:80', 'max:1080'],

            // Animation
            'animation' => ['required', Rule::in(array_keys((array) config('newlab.overlay.animations')))],
            'animation_duration' => ['required', 'integer', 'min:100', 'max:3000'],
            'display_duration' => ['required', 'integer', 'min:1000', 'max:30000'],

            // Default alert media
            'show_avatar' => ['boolean'],
            'image_media_id' => ['nullable', $mediaOwned('image')],

            // Sound
            'sound_enabled' => ['boolean'],
            'sound_preset' => ['nullable', 'string', Rule::in(array_keys((array) config('newlab.sound_presets')))],
            'sound_media_id' => ['nullable', $mediaOwned('audio')],
            'sound_volume' => ['required', 'integer', 'min:0', 'max:100'],
            'sound_delay' => ['required', 'integer', 'min:0', 'max:10000'],

            // TTS
            'tts_enabled' => ['boolean'],
            'tts_template' => ['nullable', 'string', 'max:500'],
            'tts_language' => ['required', Rule::in(['lo-LA'])],
            'tts_voice' => ['nullable', 'string', 'max:120'],
            'tts_rate' => ['required', 'numeric', 'min:0.5', 'max:2'],
            'tts_pitch' => ['required', 'numeric', 'min:0', 'max:2'],
            'tts_volume' => ['required', 'integer', 'min:0', 'max:100'],

            // Filtering
            'min_amount_to_show' => ['required', 'numeric', 'min:0'],
        ];
    }
}
