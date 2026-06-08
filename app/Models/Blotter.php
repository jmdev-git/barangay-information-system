<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Blotter extends Model
{
    use HasFactory;

    /**
     * Valid status values (Req 6, 7):
     *  - pending_review : resident-submitted, awaiting admin action
     *  - open           : admin approved / admin-created blotter
     *  - closed         : case closed
     *  - resolved       : case resolved
     *  - rejected       : admin rejected resident submission
     */
    public const STATUSES = ['pending_review', 'open', 'closed', 'resolved', 'rejected'];

    protected $fillable = [
        'case_number',
        'complainant_id',
        'complainant_name',
        'respondent_id',
        'respondent_name',
        'incident_date',
        'incident_description',
        'location',
        'status',
        'rejection_reason',
        'resolved_at',
    ];

    protected $casts = [
        'incident_date' => 'date',
        'resolved_at'   => 'datetime',
    ];

    // -------------------------------------------------------------------------
    // Model events
    // -------------------------------------------------------------------------

    protected static function booted(): void
    {
        static::creating(function (Blotter $blotter) {
            if (empty($blotter->case_number)) {
                $blotter->case_number = static::generateCaseNumber();
            }
        });
    }

    /**
     * Generate a formatted case number: BLT-YYYY-NNNN
     * e.g. BLT-2026-0001
     */
    public static function generateCaseNumber(): string
    {
        $year  = now()->year;
        $count = static::whereYear('created_at', $year)->count() + 1;
        return 'BLT-' . $year . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
    }

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    public function complainant()
    {
        return $this->belongsTo(Resident::class, 'complainant_id');
    }

    public function respondent()
    {
        return $this->belongsTo(Resident::class, 'respondent_id');
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    public function scopeClosed($query)
    {
        return $query->where('status', 'closed');
    }

    public function scopePendingReview($query)
    {
        return $query->where('status', 'pending_review');
    }
}
