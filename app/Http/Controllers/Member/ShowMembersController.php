<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Http\Resources\MemberResource;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ShowMembersController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        return Inertia::render('Members/Show', [
            'members' => Inertia::defer(fn() => $this->getWorkspaceMembers()),
        ]);
    }

    protected function getWorkspaceMembers()
    {
        $members = auth()->user()->currentWorkspace->users()->with('roles')->get();

        return MemberResource::collection($members);
    }
}
