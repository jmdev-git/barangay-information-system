<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Household extends Model
{
    use HasFactory;

    protected $fillable = [
        'household_head_id',
        'household_head_name',
        'address',
        'barangay',
        'purok',
    ];

    // Relationships
    public function head()
    {
        return $this->belongsTo(Resident::class, 'household_head_id');
    }

    public function residents()
    {
        return $this->hasMany(Resident::class);
    }
}
