<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('households', function (Blueprint $table) {
            if (! Schema::hasColumn('households', 'household_head_name')) {
                $table->string('household_head_name')->nullable();
            }
        });

        Schema::table('blotters', function (Blueprint $table) {
            if (! Schema::hasColumn('blotters', 'complainant_name')) {
                $table->string('complainant_name')->nullable();
            }

            if (! Schema::hasColumn('blotters', 'respondent_name')) {
                $table->string('respondent_name')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('blotters', function (Blueprint $table) {
            if (Schema::hasColumn('blotters', 'respondent_name')) {
                $table->dropColumn('respondent_name');
            }

            if (Schema::hasColumn('blotters', 'complainant_name')) {
                $table->dropColumn('complainant_name');
            }
        });

        Schema::table('households', function (Blueprint $table) {
            if (Schema::hasColumn('households', 'household_head_name')) {
                $table->dropColumn('household_head_name');
            }
        });
    }
};
