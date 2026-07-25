<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('overlay_settings', function (Blueprint $table) {
            // โครงการ์ดแจ้งเตือน: classic | banner | pill | hero | side-accent
            $table->string('alert_style', 24)->default('classic')->after('theme');
        });
    }

    public function down(): void
    {
        Schema::table('overlay_settings', function (Blueprint $table) {
            $table->dropColumn('alert_style');
        });
    }
};
