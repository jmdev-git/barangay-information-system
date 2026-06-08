<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('resident_documents', function (Blueprint $table) {
            // Cloudinary public_id — stored in file_path column (repurposed)
            // Cloudinary secure URL for direct access / admin preview
            $table->string('cloudinary_url')->nullable()->after('file_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('resident_documents', function (Blueprint $table) {
            $table->dropColumn('cloudinary_url');
        });
    }
};
