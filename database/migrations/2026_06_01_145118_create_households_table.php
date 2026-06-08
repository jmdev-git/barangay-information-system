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
            // Note: foreign key to residents added after residents table is created
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('households');
    }
};
