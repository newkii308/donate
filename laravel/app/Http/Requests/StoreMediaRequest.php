<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMediaRequest extends FormRequest
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
        $group = $this->input('type', 'image');
        $config = config("newlab.media.{$group}") ?? config('newlab.media.image');

        return [
            'type' => ['required', Rule::in(['image', 'animation', 'audio'])],
            'file' => [
                'required',
                'file',
                'mimes:'.implode(',', $config['mimes']),
                'max:'.$config['max_kb'],
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'file.mimes' => 'ປະເພດໄຟລ໌ນີ້ບໍ່ຮອງຮັບສຳລັບປະເພດທີ່ເລືອກ',
            'file.max' => 'ໄຟລ໌ມີຂະໜາດໃຫຍ່ເກີນໄປ',
            'file.required' => 'ກະລຸນາເລືອກໄຟລ໌',
        ];
    }
}
