<?php

namespace App\Http\Requests;

use App\Models\Streamer;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDonationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;       // public, no registration required
    }

    protected function prepareForValidation(): void
    {
        $anonymous = filter_var($this->input('is_anonymous'), FILTER_VALIDATE_BOOL);

        $this->merge([
            'is_anonymous' => $anonymous,
            'donor_name' => $anonymous
                ? 'ບໍ່ປະສົງອອກນາມ'
                : trim((string) $this->input('donor_name')),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        /** @var Streamer $streamer */
        $streamer = $this->route('streamer');
        $page = $streamer->donationPage;

        $rules = [
            'donor_name' => ['required', 'string', 'max:'.config('newlab.donation.max_name_length', 60)],
            'amount' => [
                'required',
                'numeric',
                'min:'.max((float) config('newlab.donation.min_amount', 1000), (float) ($page?->min_amount ?? 0)),
                'max:'.min((float) config('newlab.donation.max_amount', 100000000), (float) ($page?->max_amount ?? config('newlab.donation.max_amount', 100000000))),
            ],
            'transfer_reference' => ['required', 'string', 'min:4', 'max:120'],
            'message' => ['nullable', 'string', 'max:'.config('newlab.donation.max_message_length', 255)],
            'is_anonymous' => ['boolean'],
        ];

        if (! ($page->allow_anonymous ?? true)) {
            $rules['is_anonymous'][] = Rule::in([false]);
        }

        return $rules;
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'donor_name.required' => 'ກະລຸນາປ້ອນຊື່ຜູ້ໂດເນດ',
            'amount.required' => 'ກະລຸນາປ້ອນຈຳນວນເງິນທີ່ໂອນ',
            'amount.numeric' => 'ຈຳນວນເງິນຕ້ອງເປັນຕົວເລກ',
            'amount.min' => 'ຈຳນວນເງິນຕ້ອງຢ່າງໜ້ອຍ :min ກີບ',
            'amount.max' => 'ຈຳນວນເງິນຕ້ອງບໍ່ເກີນ :max ກີບ',
            'transfer_reference.required' => 'ກະລຸນາປ້ອນເລກອ້າງອີງຈາກຫຼັກຖານການໂອນ',
            'transfer_reference.min' => 'ເລກອ້າງອີງຕ້ອງມີຢ່າງໜ້ອຍ :min ຕົວອັກສອນ',
            'message.max' => 'ຂໍ້ຄວາມຍາວເກີນໄປ (ສູງສຸດ :max ຕົວອັກສອນ)',
        ];
    }
}
