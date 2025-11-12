<?php

namespace App\Http\Controllers\Member;

use App\Enums\Invitation\InvitationStatus;
use App\Enums\Workspace\Capabilities;
use App\Http\Controllers\Controller;
use App\Http\Requests\Member\RevokeInvitationRequest;
use App\Models\Invitation;
use Illuminate\Http\Request;

class RevokeInvitationController extends Controller
{
    public function __invoke(RevokeInvitationRequest $request, Invitation $invitation)
    {
        if (!$request->canPerformAction($invitation)) {
            abort(403, 'You do not have permission to revoke this invitation.');
        }

        if (!$invitation->isPending()) {
            return back()->withErrors([
                'invitation' => 'This invitation is no longer pending.',
            ]);
        }

        $invitation->update([
            'revoked_at' => now(),
            'status' => InvitationStatus::REVOKED->value,
        ]);

        return back()->with('flash', [
            'status' => 'success',
            'title' => 'Invitation revoked',
            'description' => 'The invitation for '.$invitation->email.' has been revoked.',
        ]);
    }
}
