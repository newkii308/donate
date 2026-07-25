<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ActivityLogService;
use App\Services\GlobalSettings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function __construct(
        private readonly GlobalSettings $settings,
        private readonly ActivityLogService $activity,
    ) {}

    public function edit(): View
    {
        return view('admin.settings', [
            'settings' => $this->settings->all(),
            'currencies' => config('newlab.currencies'),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        foreach (['registration_open', 'central_payment_enabled', 'announcement_enabled', 'maintenance_enabled'] as $bool) {
            $request->merge([$bool => $request->boolean($bool)]);
        }

        $data = $request->validate([
            // Identity / branding
            'platform_name' => ['required', 'string', 'max:60'],
            'tagline' => ['nullable', 'string', 'max:160'],
            'logo_url' => ['nullable', 'string', 'max:1000'],
            'favicon_url' => ['nullable', 'string', 'max:1000'],
            'brand_color' => ['nullable', 'string', 'max:9'],
            'meta_description' => ['nullable', 'string', 'max:300'],

            // Behavior
            'default_currency' => ['required', Rule::in(config('newlab.currencies'))],
            'registration_open' => ['boolean'],

            // Central account / creator payouts
            'central_payment_enabled' => ['boolean'],
            'central_bank_name' => ['nullable', 'string', 'max:120'],
            'central_account_name' => ['nullable', 'string', 'max:120'],
            'central_account_number' => ['nullable', 'string', 'max:60'],
            'central_qr_url' => ['nullable', 'string', 'max:1000'],
            'platform_fee_percent' => ['required', 'numeric', 'min:0', 'max:100'],
            'withdrawal_min_amount' => ['required', 'numeric', 'min:0', 'max:1000000000'],
            'withdrawal_fee' => ['required', 'numeric', 'min:0', 'max:1000000000'],
            'withdrawal_processing_days' => ['required', 'integer', 'min:1', 'max:30'],

            // Announcement
            'announcement_enabled' => ['boolean'],
            'announcement_text' => ['nullable', 'string', 'max:300'],

            // Maintenance
            'maintenance_enabled' => ['boolean'],
            'maintenance_message' => ['nullable', 'string', 'max:500'],

            // Contact / social
            'contact_email' => ['nullable', 'email', 'max:120'],
            'social_facebook' => ['nullable', 'string', 'max:500'],
            'social_youtube' => ['nullable', 'string', 'max:500'],
            'social_tiktok' => ['nullable', 'string', 'max:500'],
            'social_discord' => ['nullable', 'string', 'max:500'],
            'social_telegram' => ['nullable', 'string', 'max:500'],
            'social_line' => ['nullable', 'string', 'max:500'],
            'footer_text' => ['nullable', 'string', 'max:300'],
        ]);

        $this->settings->update($data);

        $this->activity->log('settings.updated', 'Global settings updated');

        return back()->with('success', 'ບັນທຶກການຕັ້ງຄ່າລະບົບສຳເລັດແລ້ວ');
    }
}
