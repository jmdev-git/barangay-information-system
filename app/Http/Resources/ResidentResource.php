<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ResidentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                   => $this->id,
            'user_id'              => $this->user_id,
            'household_id'         => $this->household_id,
            'first_name'           => $this->first_name,
            'middle_name'          => $this->middle_name,
            'last_name'            => $this->last_name,
            'full_name'            => $this->full_name,
            'age'                  => $this->age,
            'birth_date'           => $this->birth_date?->toDateString(),
            'gender'               => $this->gender,
            'civil_status'         => $this->civil_status,
            'relationship_to_head' => $this->relationship_to_head,
            'contact_number'       => $this->contact_number,
            'address'              => $this->address,
            'source'               => $this->source,
            'created_at'           => $this->created_at->toIso8601String(),
            'updated_at'           => $this->updated_at->toIso8601String(),
            'user'                 => $this->whenLoaded('user', fn () => [
                'id'     => $this->user->id,
                'name'   => $this->user->name,
                'email'  => $this->user->email,
                'role'   => $this->user->role,
                'status' => $this->user->status,
            ]),
            'household'            => $this->whenLoaded('household', fn () => [
                'id'      => $this->household->id,
                'address' => $this->household->address,
                'purok'   => $this->household->purok,
                'barangay'=> $this->household->barangay,
            ]),
            'clearances'           => ClearanceResource::collection($this->whenLoaded('clearances')),
        ];
    }
}
