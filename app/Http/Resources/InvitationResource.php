<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class   InvitationResource extends JsonResource
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
            'workspace_id' => $this->workspace_id,
            'inviter_id' => $this->inviter_id,

            'email' => $this->email,
            'role_name' => $this->role_name,

            'token' => $this->token,
            'expires_at' => $this->expires_at?->toISOString(),
            'accepted_at' => $this->accepted_at?->toISOString(),
            'accepted_by' => $this->accepted_by,
            'revoked_at' => $this->revoked_at?->toISOString(),

            'status' => $this->status,
            'meta' => $this->meta ?? null,

            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),

            'inviter' => $this->whenLoaded('inviter', function () {
                return [
                    'id' => $this->inviter->id,
                    'name' => $this->inviter->name,
                    'email' => $this->inviter->email,
                ];
            }),

            'accepted_user' => $this->whenLoaded('invitee', function () {
                return [
                    'id' => $this->invitee->id,
                    'name' => $this->invitee->name,
                    'email' => $this->invitee->email,
                ];
            }),
        ];
    }
}
