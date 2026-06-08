<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Fix blotters table for SQLite:
     *  - SQLite cannot ALTER ENUM columns, so we recreate the table with the new schema.
     *  - Adds: case_number, pending_review/rejected statuses, rejection_reason, resolved_at
     */
    public function up(): void
    {
        // Step 1: preserve existing data
        $existing = DB::table('blotters')->get();

        // Step 2: drop and recreate with full new schema
        Schema::drop('blotters');

        Schema::create('blotters', function (Blueprint $table) {
            $table->id();
            $table->string('case_number')->unique()->nullable();
            $table->unsignedBigInteger('complainant_id')->nullable();
            $table->string('complainant_name');
            $table->unsignedBigInteger('respondent_id')->nullable();
            $table->string('respondent_name');
            $table->date('incident_date');
            $table->text('incident_description');
            $table->text('location');
            // Extended status enum: pending_review and rejected added for resident self-service
            $table->enum('status', ['pending_review', 'open', 'closed', 'resolved', 'rejected'])
                  ->default('open');
            $table->text('rejection_reason')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();

            $table->foreign('complainant_id')
                ->references('id')->on('residents')->nullOnDelete();
            $table->foreign('respondent_id')
                ->references('id')->on('residents')->nullOnDelete();
        });

        // Step 3: restore existing rows
        foreach ($existing as $row) {
            DB::table('blotters')->insert([
                'id'                   => $row->id,
                'case_number'          => null,
                'complainant_id'       => $row->complainant_id,
                'complainant_name'     => $row->complainant_name,
                'respondent_id'        => $row->respondent_id,
                'respondent_name'      => $row->respondent_name,
                'incident_date'        => $row->incident_date,
                'incident_description' => $row->incident_description,
                'location'             => $row->location,
                'status'               => $row->status,
                'rejection_reason'     => null,
                'resolved_at'          => null,
                'created_at'           => $row->created_at,
                'updated_at'           => $row->updated_at,
            ]);
        }
    }

    public function down(): void
    {
        Schema::drop('blotters');

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
                ->references('id')->on('residents')->nullOnDelete();
            $table->foreign('respondent_id')
                ->references('id')->on('residents')->nullOnDelete();
        });
    }
};
