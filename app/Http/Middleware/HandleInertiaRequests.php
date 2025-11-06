<?php

namespace App\Http\Middleware;

use App\Enums\Workspace\Capabilities;
use App\Enums\Workspace\RoleEnum;
use App\Models\Workspace;
use Illuminate\Foundation\Inspiring;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Inertia\Middleware;
use Spatie\Permission\Models\Role;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'auth' => [
                'user' => $request->user(),
                'workspaces' => $request->user()?->workspaces,
            ],
            'sidebarOpen' => !$request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
            'flash' => $this->getSessionNotification($request)
        ];
    }

    /**
     * @param  Request  $request
     *
     * @return \Closure
     */
    public function getSessionNotification(Request $request): \Closure
    {
        return fn() => tap($request->session()->pull('flash'), function (&$f) {
            if (is_array($f)) {
                $f['id'] ??= (string) Str::uuid();
            }
        });
    }

}
