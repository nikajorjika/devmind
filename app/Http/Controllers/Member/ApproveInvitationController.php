<?php

namespace App\Http\Controllers\Member;

use App\Enums\Invitation\InvitationStatus;
use App\Enums\Member\MemberStatus;
use App\Http\Controllers\Controller;
use App\Models\Invitation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApproveInvitationController extends Controller
{
    public function __invoke(Request $request, string $token)
    {
        $user = $request->user();

        /** @var Invitation $invitation */
        $invitation = Invitation::query()
            ->where('token', $token)
            ->firstOrFail();
        if (! $invitation || ! $invitation->isPending() || $invitation->isExpired()) {
            return redirect()
                ->route('invitation.accept', $token)
                ->with('flash', [
                    'status' => 'warning',
                    'title' => 'Invitation invalid',
                    'description' => 'This invitation is invalid, expired, or revoked.',
                ]);
        }

        if (strtolower($invitation->email) !== strtolower($user->email)) {
            return redirect()
                ->route('invitation.accept', $token)
                ->with('flash', [
                    'status' => 'warning',
                    'title' => 'Email mismatch',
                    'description' => 'You are logged in as a different user than the invited email.',
                ]);
        }

        if ($invitation->status === InvitationStatus::ACCEPTED && $invitation->accepted_by === $user->id) {
            return $this->redirectAfterAccept($request, $invitation);
        }

        DB::transaction(function () use ($invitation, $user) {
            setPermissionsTeamId($invitation->workspace_id);

            $user->workspaces()->syncWithoutDetaching([
                $invitation->workspace_id => [
                    'role_name' => $invitation->role_name,
                    'status' => MemberStatus::ACTIVE->value,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);

            $user->assignRole($invitation->role_name);

            $invitation->forceFill([
                'status' => InvitationStatus::ACCEPTED->value,
                'accepted_at' => now(),
                'accepted_by' => $user->id,
            ])->save();

            setPermissionsTeamId(null);
        });

        return $this->redirectAfterAccept($request, $invitation);
    }

    protected function redirectAfterAccept(Request $request, Invitation $invitation)
    {
        $intended = $request->session()->pull('url.intended');
        if ($intended) {
            return redirect()->to($intended)->with('flash', [
                'status' => 'success',
                'title' => 'Invitation accepted',
                'description' => 'Welcome to the workspace.',
            ]);
        }

        return redirect()->route('dashboard')->with('flash', [
            'status' => 'success',
            'title' => 'Invitation accepted',
            'description' => 'Welcome to the workspace.',
        ]);
    }
}
