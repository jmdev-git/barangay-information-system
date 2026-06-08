<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BlotterResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                   => $this->id,
            'case_number'          => $this->case_number,       // fixed — column now exists
            'complainant_id'       => $this->complainant_id,
            'complainant_name'     => $this->complainant_name,
            'respondent_id'        => $this->respondent_id,
            'respondent_name'      => $this->respondent_name,
            'incident_date'        => $this->incident_date?->toDateString(),
            'incident_description' => $this->incident_description,
            'location'             => $this->location,
            'status'               => $this->status,
            'rejection_reason'     => $this->rejection_reason,
            'resolved_at'          => $this->resolved_at?->toIso8601String(),
            'created_at'           => $this->created_at->toIso8601String(),
            'updated_at'           => $this->updated_at->toIso8601String(),
            'complainant'          => $this->whenLoaded('complainant', fn () => [
                'id'        => $this->complainant->id,
                'full_name' => $this->complainant->full_name,
            ]),
            'respondent'           => $this->whenLoaded('respondent', fn () => [
                'id'        => $this->respondent->id,
                'full_name' => $this->respondent->full_name,
            ]),
        ];
    }
}
