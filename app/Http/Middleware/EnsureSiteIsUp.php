<?php

namespace App\Http\Middleware;

use App\Services\GlobalSettings;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSiteIsUp
{
    public function __construct(private readonly GlobalSettings $settings)
    {
    }

    /**
     * โหมดปิดปรับปรุง: ผู้เยี่ยมชมทั่วไปเห็นหน้าปิดปรับปรุง
     * แต่แอดมินยังเข้าใช้งานได้ตามปกติ (จะได้แก้ไขระบบต่อได้)
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $this->settings->get('maintenance_enabled', false)) {
            return $next($request);
        }

        if ($request->user()?->isAdmin()) {
            return $next($request);
        }

        return response()->view('maintenance', [
            'message' => $this->settings->get('maintenance_message'),
            'siteName' => $this->settings->get('platform_name'),
        ], 503);
    }
}
