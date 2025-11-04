<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class EnsureWorkspaceExists
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        $user = $request->user();

        if ($user && $user->workspaces()->exists()) {
            return $next($request);
        }

        if ($request->header('X-Inertia') && !$request->isMethod('GET')) {
            return Inertia::location(route('workspace.create'));
        }

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Workspace required.',
                'redirect' => route('workspace.create'),
            ], 409);
        }

        return redirect()->route('workspace.create');
    }
}
