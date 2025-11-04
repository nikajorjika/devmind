<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\User
 */
class MemberResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Determine current workspace context
        $workspaceId = data_get($this, 'pivot.workspace_id') // typical when users()->withPivot('workspace_id')
            ?? data_get($this, 'current_workspace_id');

        // Roles filtered to current workspace (only if roles are loaded)
        $rolesCollection = collect($this->whenLoaded('roles') ?? [])
            ->filter(fn ($role) => (int) data_get($role, 'pivot.workspace_id') === (int) $workspaceId)
            ->values();

        $primaryRole = $rolesCollection->first();

        return [
            'id'              => $this->id,
            'name'            => $this->name,
            'email'           => $this->email,
            'status'           => 'active',
            'email_verified'  => ! is_null($this->email_verified_at),
            'created_at'      => optional($this->created_at)->toIso8601String(),
            'updated_at'      => optional($this->updated_at)->toIso8601String(),

            // Workspace membership context
            'workspace_id'    => $workspaceId,
            'membership'      => [
                'workspace_id' => $workspaceId,
                'user_id'      => $this->id,
            ],

            'role'            => data_get($primaryRole, 'name'),
            'role_id'         => data_get($primaryRole, 'id'),
            'is_owner'        => (bool) $rolesCollection->firstWhere('name', 'Owner'),

            // All roles in current workspace (guarded to what's loaded)
            'roles'           => $rolesCollection->map(fn ($r) => [
                'id'          => $r->id,
                'name'        => $r->name,
                'guard_name'  => $r->guard_name,
                'description' => $r->description,
            ])->all(),

            // Security flags
            'two_factor_enabled' => ! is_null($this->two_factor_confirmed_at),
        ];
    }
}
