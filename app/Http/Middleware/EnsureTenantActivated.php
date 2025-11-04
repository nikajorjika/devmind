<?php

namespace App\Http\Middleware;

use App\Models\Workspace;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTenantActivated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $currentWorkspaceId = $request->session()->get('current_workspace_id') ?? $user->current_workspace_id;

        /** @var Workspace $workspace */
        $workspace = $user->workspaces()->find($currentWorkspaceId) ?? $user->workspaces()->first();

        $request->session()->put(['current_workspace_id' => $workspace->getKey()]);

        $workspace->makeCurrent();

        if ($user->current_workspace_id !== $workspace->getKey()) {
            $user->forceFill(['current_workspace_id' => $workspace->getKey()])->save();
        }

        return $next($request);
    }
}
