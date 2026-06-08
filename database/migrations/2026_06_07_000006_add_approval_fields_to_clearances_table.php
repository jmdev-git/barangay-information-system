<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Extend clearances for Req 5:
     *  - rejection_reason: admin must provide when rejecting (Req 5 AC5)
     *  - certificate_path: generated PDF stored here after approval (Req 5 AC4)
     *  - approved_by: which admin approved/rejected
     */
    public function up(): void
    {
        Schema::table('clearances', function (Blueprint $table) {
            $table->text('rejection_reason')->nullable()->after('status');
            $table->string('certificate_path')->nullable()->after('rejection_reason');
            $table->unsignedBigInteger('approved_by')->nullable()->after('certificate_path');
            $table->foreign('approved_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('clearances', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
            $table->dropColumn(['rejection_reason', 'certificate_path', 'approved_by']);
        });
    }
};
