<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClearanceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'resident_id'      => $this->resident_id,
            'purpose'          => $this->purpose,
            'status'           => $this->status,
            'rejection_reason' => $this->rejection_reason,
            'requested_at'     => $this->requested_at?->toIso8601String(),
            'issued_at'        => $this->issued_at?->toIso8601String(),
            'approved_by'      => $this->approved_by,
            'created_at'       => $this->created_at->toIso8601String(),
            'updated_at'       => $this->updated_at->toIso8601String(),
            'resident'         => $this->whenLoaded('resident', fn () => [
                'id'        => $this->resident->id,
                'full_name' => $this->resident->full_name,
                'email'     => $this->resident->email,
            ]),
        ];
    }
}
