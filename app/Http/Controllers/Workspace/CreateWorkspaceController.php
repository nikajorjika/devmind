<?php

namespace App\Http\Controllers\Workspace;

use App\Http\Controllers\Controller;
use Inertia\Inertia;

class CreateWorkspaceController extends Controller
{
    public function __invoke()
    {
        return Inertia::render('workspace/Create', []);
    }
}
