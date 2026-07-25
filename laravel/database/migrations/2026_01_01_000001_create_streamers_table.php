<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('streamers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('username')->unique();          // used in /donate/{username}
            $table->string('overlay_key', 64)->unique();   // used in /overlay/{overlay_key}
            $table->string('display_name');
            $table->text('description')->nullable();
            $table->string('avatar_path')->nullable();
            $table->string('qr_code_path')->nullable();
            $table->string('truewallet_phone', 16)->nullable(); // เบอร์ TrueMoney Wallet สำหรับรับซอง (Angpao)
            $table->string('payment_method')->nullable();   // PromptPay, Bank Transfer, ... (เก็บไว้ใช้อนาคต)
            $table->string('account_name')->nullable();
            $table->string('account_number')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('currency', 8)->default(config('newlab.default_currency', 'LAK'));
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('streamers');
    }
};
