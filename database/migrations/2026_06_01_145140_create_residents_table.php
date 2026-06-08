<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('residents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('email')->nullable();
            $table->unsignedBigInteger('household_id')->nullable();
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->date('birth_date');
            $table->enum('gender', ['male', 'female', 'other']);
            $table->string('contact_number');
            $table->text('address');
            $table->timestamps();

           $table->foreign('user_id')
    ->references('id')
    ->on('users')
    ->nullOnDelete();

            $table->foreign('household_id')
                ->references('id')
                ->on('households')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('residents');
    }
};
