<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Resident extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'email',
        'household_id',
        'first_name',
        'middle_name',
        'last_name',
        'birth_date',
        'gender',
        'civil_status',
        'relationship_to_head',
        'contact_number',
        'address',
        'photo_path',
        'verified_by',
        'source',
    ];

    protected $casts = [
        'birth_date' => 'date',
    ];

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function household()
    {
        return $this->belongsTo(Household::class);
    }

    public function clearances()
    {
        return $this->hasMany(Clearance::class);
    }

    public function complainantBlotters()
    {
        return $this->hasMany(Blotter::class, 'complainant_id');
    }

    public function respondentBlotters()
    {
        return $this->hasMany(Blotter::class, 'respondent_id');
    }

    /** Government ID documents uploaded for this resident profile */
    public function documents()
    {
        return $this->hasMany(ResidentDocument::class);
    }

    /** Admin user who verified this resident (census workflow) */
    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    // -------------------------------------------------------------------------
    // Accessors
    // -------------------------------------------------------------------------

    /** Full name with optional middle initial */
    public function getFullNameAttribute(): string
    {
        $parts = array_filter([
            $this->first_name,
            $this->middle_name ? $this->middle_name : null,
            $this->last_name,
        ]);
        return implode(' ', $parts);
    }

    /** Computed age from birth_date */
    public function getAgeAttribute(): ?int
    {
        return $this->birth_date ? Carbon::parse($this->birth_date)->age : null;
    }
}
