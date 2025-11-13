<?php

namespace App\Actions\Workspace;

use App\Models\User;
use App\Models\Workspace;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Auth;

class ActivateWorkspace
{
    /**
     * Activate the given workspace for the user and current request.
     *
     * - Verifies membership (optional toggle).
     * - Forgets existing tenant context (if any).
     * - Makes $workspace current (runs Multitenancy tasks).
     * - Persists selection for next requests via session.
     * - Updates user's current_workspace_id (handy default).
     *
     * @throws AuthorizationException if $ensureMember = true and user isn't a member.
     */
    public function handle(Workspace $workspace, ?User $user = null, bool $ensureMember = true): void
    {
        $user ??= Auth::user();
        $workspaceId = $workspace->getKey();

        if ($ensureMember && $user && ! $user->workspaces()->whereKey($workspaceId)->exists()) {
            throw new AuthorizationException('You are not a member of this workspace.');
        }

        if (Workspace::current()) {
            Workspace::forgetCurrent();
        }

        $workspace->makeCurrent();

        session(['current_workspace_id' => $workspaceId]);

        session(['ensure_valid_tenant_session_tenant_id' => $workspaceId]);

        if ($user->current_workspace_id !== $workspaceId) {
            $user->forceFill(['current_workspace_id' => $workspaceId])->save();
        }
    }

    public static function run(Workspace $workspace, ?User $user = null, bool $ensureMember = true): void
    {
        app(self::class)->handle($workspace, $user, $ensureMember);
    }
}
