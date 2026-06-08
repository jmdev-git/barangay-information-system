<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ResidentDocument extends Model
{
    use HasFactory;

    /**
     * Accepted government ID types (Req 1, 8)
     */
    public const DOCUMENT_TYPES = [
        'philsys'         => 'PhilSys National ID',
        'drivers_license' => "Driver's License",
        'umid'            => 'UMID',
        'passport'        => 'Passport',
        'other'           => 'Other Government ID',
    ];

    protected $fillable = [
        'user_id',
        'resident_id',
        'document_type',
        'file_path',       // Cloudinary public_id
        'cloudinary_url',  // Cloudinary secure URL
        'original_name',
        'mime_type',
        'file_size',
        'context',
    ];

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function resident()
    {
        return $this->belongsTo(Resident::class);
    }

    // -------------------------------------------------------------------------
    // Accessors
    // -------------------------------------------------------------------------

    /** Human-readable document type label */
    public function getDocumentTypeLabelAttribute(): string
    {
        return self::DOCUMENT_TYPES[$this->document_type] ?? 'Government ID';
    }

    /** Full storage URL (for admin preview) — uses Cloudinary URL if available */
    public function getUrlAttribute(): string
    {
        if ($this->cloudinary_url) {
            return $this->cloudinary_url;
        }
        // Fallback for legacy local files
        return route('admin.documents.show', $this->id);
    }

    /** File size formatted e.g. "1.2 MB" */
    public function getFileSizeFormattedAttribute(): string
    {
        $bytes = $this->file_size;
        if ($bytes >= 1048576) {
            return round($bytes / 1048576, 2) . ' MB';
        }
        if ($bytes >= 1024) {
            return round($bytes / 1024, 2) . ' KB';
        }
        return $bytes . ' B';
    }
}
