<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Extend residents table for:
     *  - Census workflow (Req 8): civil_status, relationship_to_head, verified_by, photo_path
     *  - Standard PH barangay fields
     */
    public function up(): void
    {
        Schema::table('residents', function (Blueprint $table) {
            $table->enum('civil_status', ['single', 'married', 'widowed', 'separated', 'annulled'])
                  ->default('single')
                  ->after('gender');

            // Relationship of resident to the household head
            $table->string('relationship_to_head')->nullable()->after('civil_status');

            // Resident photo path (Step 3 of census: capture photo)
            $table->string('photo_path')->nullable()->after('relationship_to_head');

            // Enumerator who verified/created this profile (Req 8 AC4)
            $table->unsignedBigInteger('verified_by')->nullable()->after('photo_path');
            $table->foreign('verified_by')->references('id')->on('users')->nullOnDelete();

            // Whether profile was created via census workflow or self-registration
            $table->enum('source', ['self_registration', 'census'])->default('self_registration')->after('verified_by');
        });
    }

    public function down(): void
    {
        Schema::table('residents', function (Blueprint $table) {
            $table->dropForeign(['verified_by']);
            $table->dropColumn(['civil_status', 'relationship_to_head', 'photo_path', 'verified_by', 'source']);
        });
    }
};
