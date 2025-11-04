<?php

namespace App\Http\Controllers\Workspace;

use App\Actions\Workspace\ActivateWorkspace;
use App\Enums\Workspace\RoleEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Workspace\StoreWorkspaceRequest;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class StoreWorkspaceController extends Controller
{
    /**
     * @param  StoreWorkspaceRequest  $request
     *
     * @return Response
     * @throws \Exception
     */
    public function __invoke(StoreWorkspaceRequest $request)
    {
        $user = auth()->user();
        $workspace = $user->workspaces()->create($request->validated());

        app(ActivateWorkspace::class)->handle($workspace, $user);

        setPermissionsTeamId($workspace->id);

        $user->assignRole(RoleEnum::OWNER);

        return redirect()->route('dashboard');
    }
}
