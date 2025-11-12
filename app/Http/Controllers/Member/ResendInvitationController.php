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
            return back()->with('flash', [
                'status' => 'error',
                'title' => 'Cannot resend invitation',
                'description' => 'You do not have permission to resend this invitation.',
            ]);
        }

        // State guard
        if (!$invitation->isPending()) {
            return back()->with('flash', [
                'status' => 'warning',
                'title' => 'Cannot resend',
                'description' => 'This invitation is no longer pending.',
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
