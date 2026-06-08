<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HouseholdResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                  => $this->id,
            'household_head_id'   => $this->household_head_id,
            'household_head_name' => $this->household_head_name
                ?? ($this->relationLoaded('head') && $this->head
                    ? $this->head->full_name
                    : null),
            'address'             => $this->address,
            'barangay'            => $this->barangay,
            'purok'               => $this->purok,
            'resident_count'      => $this->whenLoaded('residents', fn () => $this->residents->count()),
            'created_at'          => $this->created_at->toIso8601String(),
            'updated_at'          => $this->updated_at->toIso8601String(),
            'residents'           => $this->whenLoaded('residents', fn () =>
                $this->residents->map(fn ($r) => [
                    'id'        => $r->id,
                    'full_name' => $r->full_name,
                    'gender'    => $r->gender,
                    'age'       => $r->age,
                ])
            ),
        ];
    }
}
