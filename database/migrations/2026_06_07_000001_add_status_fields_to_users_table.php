<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add account status, rejection reason, and approved_by to users table.
     * This is the foundation for Req 1 (registration) and Req 2 (admin approval).
     *
     * Status flow:
     *   pending_verification → active   (admin approves)
     *   pending_verification → rejected (admin rejects)
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('status', ['pending_verification', 'active', 'rejected'])
                  ->default('active')
                  ->after('role');

            $table->text('rejection_reason')->nullable()->after('status');

            $table->unsignedBigInteger('approved_by')->nullable()->after('rejection_reason');
            $table->timestamp('approved_at')->nullable()->after('approved_by');

            $table->foreign('approved_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
            $table->dropColumn(['status', 'rejection_reason', 'approved_by', 'approved_at']);
        });
    }
};
