<?php

namespace App\Http\Middleware;

use App\Enums\Workspace\Capabilities;
use App\Enums\Workspace\RoleEnum;
use App\Models\Workspace;
use Closure;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;
use Symfony\Component\HttpFoundation\Response;

class HandlesInertiaTenantRequests
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        Inertia::share('currentWorkspace', Workspace::current());
        Inertia::share('roles', $this->getAvailableRoles());

        return $next($request);
    }


    protected function getAvailableRoles(): Collection
    {
        return Role::all();
    }
}
