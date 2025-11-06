<?php

namespace App\Http\Controllers\Member;

use App\Enums\Invitation\InvitationStatus;
use App\Enums\Workspace\RoleEnum;
use App\Events\InvitationCreated;
use App\Http\Controllers\Controller;
use App\Http\Requests\Member\InviteMemberRequest;
use App\Models\Invitation;
use Illuminate\Support\Str;

class InviteMemberController extends Controller
{
    public function __invoke(InviteMemberRequest $request)
    {
        $data = $request->validated();
        $invitation = Invitation::for($data['email'])
            ->inCurrentWorkspace()
            ->active()
            ->first();

        if ($invitation) {
            return redirect()->back()
                ->withErrors(['email' => 'An active invitation already exists for this email.']);
        }

        $invitation = Invitation::create([
            'workspace_id' => $request->session()->get('current_workspace_id'),
            'inviter_id' => auth()->id(),
            'email' => $data['email'],
            'role_name' => $data['role'],
            'token' => Str::ulid(),
            'expires_at' => now()->addDays(7),
            'status' => InvitationStatus::PENDING->value,
        ]);

        event(new InvitationCreated($invitation));

        request()->session()->flash('flash', [
            'status' => 'success',
            'title' => 'Invitation sent',
            'description' => 'We emailed an invite to the user. They will join as a '.RoleEnum::from($data['role'])->value.' role.',
        ]);

        return redirect()->back();
    }
}
