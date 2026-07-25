<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('donations', function (Blueprint $table) {
            $table->string('transfer_reference', 120)->nullable()->after('currency')->index();
            $table->decimal('platform_fee', 14, 2)->default(0)->after('amount');
            $table->decimal('net_amount', 14, 2)->nullable()->after('platform_fee');
            $table->timestamp('verified_at')->nullable()->after('status');
            $table->foreignId('verified_by')->nullable()->after('verified_at')->constrained('users')->nullOnDelete();
            $table->text('rejection_reason')->nullable()->after('verified_by');
        });

        Schema::create('withdrawals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('streamer_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 14, 2);
            $table->decimal('fee', 14, 2)->default(0);
            $table->decimal('net_amount', 14, 2);
            $table->string('currency', 3)->default('LAK');
            $table->string('status', 20)->default('pending')->index();
            $table->string('bank_name', 120);
            $table->string('account_name', 120);
            $table->string('account_number', 60);
            $table->string('payment_method', 60)->nullable();
            $table->text('creator_note')->nullable();
            $table->text('admin_note')->nullable();
            $table->string('payment_reference', 120)->nullable();
            $table->timestamp('requested_at');
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['streamer_id', 'status']);
        });

        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('streamer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('donation_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('withdrawal_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type', 32);
            $table->decimal('amount', 14, 2);
            $table->decimal('balance_after', 14, 2);
            $table->string('description', 255)->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique('donation_id');
            $table->unique(['withdrawal_id', 'type']);
            $table->index(['streamer_id', 'id']);
        });

        // Existing verified donations become the opening creator balance.
        $balances = [];
        DB::table('donations')
            ->where('status', 'completed')
            ->orderBy('id')
            ->each(function ($donation) use (&$balances) {
                $net = (float) ($donation->net_amount ?? $donation->amount);
                $balances[$donation->streamer_id] = ($balances[$donation->streamer_id] ?? 0) + $net;

                DB::table('donations')->where('id', $donation->id)->update([
                    'net_amount' => $net,
                    'verified_at' => $donation->created_at,
                ]);

                DB::table('wallet_transactions')->insert([
                    'streamer_id' => $donation->streamer_id,
                    'donation_id' => $donation->id,
                    'withdrawal_id' => null,
                    'type' => 'donation_credit',
                    'amount' => $net,
                    'balance_after' => $balances[$donation->streamer_id],
                    'description' => 'Opening credit from previously completed donation',
                    'created_by' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            });
    }

    public function down(): void
    {
        Schema::dropIfExists('wallet_transactions');
        Schema::dropIfExists('withdrawals');

        Schema::table('donations', function (Blueprint $table) {
            $table->dropForeign(['verified_by']);
            $table->dropColumn([
                'transfer_reference',
                'platform_fee',
                'net_amount',
                'verified_at',
                'verified_by',
                'rejection_reason',
            ]);
        });
    }
};
