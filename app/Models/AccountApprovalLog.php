<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountApprovalLog extends Model
{
    use HasFactory;

    /**
     * Immutable audit record — never update, only insert (Req 2 AC5).
     */
    protected $fillable = [
        'admin_id',
        'target_user_id',
        'action',
        'reason',
    ];

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function targetUser()
    {
        return $this->belongsTo(User::class, 'target_user_id');
    }
}
