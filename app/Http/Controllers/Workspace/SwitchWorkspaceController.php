<?php

namespace App\Http\Controllers\Workspace;

use App\Actions\Workspace\ActivateWorkspace;
use App\Http\Controllers\Controller;
use App\Http\Requests\Workspace\SwitchWorkspaceRequest;

class SwitchWorkspaceController extends Controller
{
    public function __invoke(SwitchWorkspaceRequest $request)
    {
        $workspace = $request->workspace();

        app(ActivateWorkspace::class)->handle($workspace);

        return redirect()->route('dashboard');
    }
}
