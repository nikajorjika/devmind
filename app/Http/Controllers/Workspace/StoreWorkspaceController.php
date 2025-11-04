<?php

namespace App\Http\Controllers\Workspace;

use App\Actions\Workspace\ActivateWorkspace;
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
        $workspace = auth()->user()->workspaces()->create($request->validated());
        // Activate the newly created workspace for the user.

        app(ActivateWorkspace::class)->handle($workspace, auth()->user());

        return redirect()->route('dashboard');
    }
}
