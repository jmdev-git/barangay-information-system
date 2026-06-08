<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('households', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('household_head_id')->nullable();
            $table->string('household_head_name')->nullable();
            $table->text('address');
            $table->string('barangay');
            $table->string('purok')->nullable();
            $table->timestamps();

            $table->foreign('household_head_id')
                ->references('id')
                ->on('residents')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('households');
    }
};
