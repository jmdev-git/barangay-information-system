<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add foreign key from households.household_head_id -> residents.id
     * This runs AFTER both households and residents tables are created.
     */
    public function up(): void
    {
        Schema::table('households', function (Blueprint $table) {
            $table->foreign('household_head_id')
                ->references('id')
                ->on('residents')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('households', function (Blueprint $table) {
            $table->dropForeign(['household_head_id']);
        });
    }
};
