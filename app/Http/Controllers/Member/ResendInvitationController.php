<?php

namespace App\Http\Controllers\Member;

use App\Events\InvitationCreated;
use App\Http\Controllers\Controller;
use App\Models\Invitation;
use Illuminate\Http\Request;

class ResendInvitationController extends Controller
{
    public function __invoke(Request $request, Invitation $invitation)
    {
        // Ensure the invitation belongs to the current workspace
        if ($invitation->workspace_id !== $request->session()->get('current_workspace_id')) {
            abort(403, 'Unauthorized action.');
        }

        // Ensure the invitation is still pending
        if (!$invitation->isPending()) {
            return redirect()->back()->withErrors([
                'invitation' => 'This invitation cannot be resent because it is no longer pending.',
            ]);
        }

        // Fire the event to send the email again
        event(new InvitationCreated($invitation));

        return redirect()->back()->with('flash', [
            'status' => 'success',
            'title' => 'Invitation resent',
            'description' => 'The invitation email has been sent again to '.$invitation->email,
        ]);
    }
}
