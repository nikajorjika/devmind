<?php

namespace App\Listeners;

use App\Events\InvitationCreated;
use App\Mail\WorkspaceInvitationMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class SendInvitationEmail implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
    }

    /**
     * Handle the event.
     */
    public function handle(InvitationCreated $event): void
    {
        Mail::to($event->invitation->email)
            ->send(new WorkspaceInvitationMail($event->invitation));
    }
}
