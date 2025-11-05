<?php

namespace App\Listeners;

use App\Events\InvitationCreated;
use App\Mail\WorkspaceInvitationMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;
use Spatie\Multitenancy\Jobs\NotTenantAware;

class SendInvitationEmail implements ShouldQueue, NotTenantAware
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
