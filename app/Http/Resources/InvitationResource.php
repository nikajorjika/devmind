<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InvitationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'email' => $this->email,
            'role_name' => $this->role_name,
            'status' => $this->status,
            'expires_at' => $this->expires_at?->toISOString(),
            'created_at' => $this->created_at?->toISOString(),

            'inviter' => $this->whenLoaded('inviter', function () {
                return [
                    'id' => $this->inviter->id,
                    'name' => $this->inviter->name,
                ];
            }),

            'is_expired' => $this->isExpired(),
            'is_pending' => $this->isPending(),
            'revoked_at' => $this->revoked_at?->toISOString(),
        ];
    }
}
