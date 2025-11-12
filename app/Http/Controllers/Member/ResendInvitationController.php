<?php

namespace App\Http\Controllers\Member;

use App\Enums\Workspace\Capabilities;
use App\Events\InvitationCreated;
use App\Http\Controllers\Controller;
use App\Http\Requests\Member\ResendInvitationRequest;
use App\Models\Invitation;
use Illuminate\Http\Request;

class ResendInvitationController extends Controller
{
    public function __invoke(ResendInvitationRequest $request, Invitation $invitation)
    {
        if (!$request->canPerformAction($invitation)) {
            abort(403, 'You do not have permission to resend this invitation.');
        }

        // State guard
        if (!$invitation->isPending()) {
            return back()->withErrors([
                'invitation' => 'This invitation is no longer pending.',
            ]);
        }

        event(new InvitationCreated($invitation));

        return back()->with('flash', [
            'status' => 'success',
            'title' => 'Invitation resent',
            'description' => 'We’ve re-sent the invitation to '.$invitation->email.'.',
        ]);
    }
}
