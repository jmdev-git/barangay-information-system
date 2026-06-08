<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Store government ID documents uploaded during:
     *  - Self-registration (Req 1): file stored until admin reviews
     *  - Census workflow (Req 8 Step 4): enumerator captures/uploads ID
     *
     * Files are stored in storage/app/private/resident-documents/ (outside web root).
     */
    public function up(): void
    {
        Schema::create('resident_documents', function (Blueprint $table) {
            $table->id();

            // Link to user (populated during registration, before resident profile exists)
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();

            // Link to resident profile (populated after profile is created via census)
            $table->unsignedBigInteger('resident_id')->nullable();
            $table->foreign('resident_id')->references('id')->on('residents')->cascadeOnDelete();

            // Document type
            $table->enum('document_type', [
                'philsys',
                'drivers_license',
                'umid',
                'passport',
                'other',
            ])->default('other');

            // Stored file path (relative to storage disk)
            $table->string('file_path');
            $table->string('original_name');
            $table->string('mime_type');
            $table->unsignedBigInteger('file_size'); // bytes

            // Review fields
            $table->enum('context', ['registration', 'census'])->default('registration');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resident_documents');
    }
};
