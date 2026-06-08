<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Audit log for every admin approval / rejection action (Req 2 AC5).
     * Immutable — never updated, only inserted.
     */
    public function up(): void
    {
        Schema::create('account_approval_logs', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('admin_id');
            $table->foreign('admin_id')->references('id')->on('users')->cascadeOnDelete();

            $table->unsignedBigInteger('target_user_id');
            $table->foreign('target_user_id')->references('id')->on('users')->cascadeOnDelete();

            $table->enum('action', ['approved', 'rejected']);
            $table->text('reason')->nullable(); // populated on rejection

            $table->timestamps(); // created_at = the timestamp of the action
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('account_approval_logs');
    }
};
