<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blotters', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('complainant_id')->nullable();
            $table->string('complainant_name');
            $table->unsignedBigInteger('respondent_id')->nullable();
            $table->string('respondent_name');
            $table->date('incident_date');
            $table->text('incident_description');
            $table->text('location');
            $table->enum('status', ['open', 'closed', 'resolved'])->default('open');
            $table->timestamps();

            $table->foreign('complainant_id')
                ->references('id')
                ->on('residents')
                ->nullOnDelete();

            $table->foreign('respondent_id')
                ->references('id')
                ->on('residents')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blotters');
    }
};
