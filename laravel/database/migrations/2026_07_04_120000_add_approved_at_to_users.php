<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // null = สมัครใหม่ รอแอดมินอนุมัติ · มีค่า = อนุมัติแล้ว
            $table->timestamp('approved_at')->nullable()->after('is_active');
        });

        // ผู้ใช้เดิมทั้งหมด (ก่อนมีระบบอนุมัติ) ถือว่าอนุมัติแล้ว จะได้ไม่ค้างรออนุมัติ
        DB::table('users')->whereNull('approved_at')->update(['approved_at' => DB::raw('created_at')]);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('approved_at');
        });
    }
};
