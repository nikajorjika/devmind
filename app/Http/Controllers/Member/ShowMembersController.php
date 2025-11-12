<?php

namespace App\Http\Controllers\Member;

use App\Enums\Workspace\Capabilities;
use App\Http\Controllers\Controller;
use App\Http\Resources\InvitationResource;
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
        return Inertia::render('members/Show', [
            'members' => Inertia::defer(fn() => $this->getWorkspaceMembers()),
            'invitations' => Inertia::defer(fn() => $this->getInvitations()),
        ]);
    }

    protected function getWorkspaceMembers()
    {
        $members = auth()->user()->currentWorkspace->users()->with('roles')->get();

        return MemberResource::collection($members);
    }

    protected function getInvitations()
    {
        $invitations = auth()->user()->currentWorkspace->invitations()->active()->with('inviter')->get();

        return InvitationResource::collection($invitations);
    }
}
