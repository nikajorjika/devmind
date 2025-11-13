<?php

use App\Enums\Invitation\InvitationStatus;
use App\Events\InvitationCreated;
use App\Mail\WorkspaceInvitationMail;
use App\Models\Invitation;
use App\Models\User;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('can invite a new member', function () {
    Mail::fake();
    Event::fake();

    $user = User::factory()->withWorkspace()->create();

    $response = $this->actingAs($user)->post(route('invitationCreate'), [
        'email' => 'newmember@example.com',
        'role' => 'Member',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('flash.status', 'success');

    $this->assertDatabaseHas('invitations', [
        'workspace_id' => $user->current_workspace_id,
        'email' => 'newmember@example.com',
        'role_name' => 'Member',
        'status' => InvitationStatus::PENDING->value,
    ]);

    Event::assertDispatched(InvitationCreated::class);
});

test('invitation created event fires email listener', function () {
    Mail::fake();

    $user = User::factory()->withWorkspace()->create();

    $invitation = Invitation::create([
        'workspace_id' => $user->current_workspace_id,
        'inviter_id' => $user->id,
        'email' => 'test@example.com',
        'role_name' => 'Member',
        'token' => \Illuminate\Support\Str::ulid(),
        'expires_at' => now()->addDays(7),
        'status' => InvitationStatus::PENDING->value,
    ]);

    event(new InvitationCreated($invitation));

    Mail::assertSent(WorkspaceInvitationMail::class, function ($mail) use ($invitation) {
        return $mail->hasTo($invitation->email) &&
            $mail->invitation->id === $invitation->id;
    });
});

test('invitation email contains correct information', function () {
    $user = User::factory()->withWorkspace()->create();

    $invitation = Invitation::create([
        'workspace_id' => $user->current_workspace_id,
        'inviter_id' => $user->id,
        'email' => 'test@example.com',
        'role_name' => 'Member',
        'token' => \Illuminate\Support\Str::ulid(),
        'expires_at' => now()->addDays(7),
        'status' => InvitationStatus::PENDING->value,
    ]);

    $mailable = new WorkspaceInvitationMail($invitation);

    expect($mailable->envelope()->subject)->toContain($user->currentWorkspace->name);
    expect($mailable->content()->view)->toBe('emails.workspace-invitation');
});

test('prevents duplicate active invitations for same email', function () {
    $user = User::factory()->withWorkspace()->create();

    // Create first invitation
    Invitation::create([
        'workspace_id' => $user->current_workspace_id,
        'inviter_id' => $user->id,
        'email' => 'duplicate@example.com',
        'role_name' => 'Member',
        'token' => \Illuminate\Support\Str::ulid(),
        'expires_at' => now()->addDays(7),
        'status' => InvitationStatus::PENDING->value,
    ]);

    // Try to create duplicate
    $response = $this->actingAs($user)->post(route('invitationCreate'), [
        'email' => 'duplicate@example.com',
        'role' => 'Member',
    ]);

    $response->assertSessionHasErrors(['email']);
});

test('can resend pending invitation', function () {
    Mail::fake();
    Event::fake();

    $user = User::factory()->withWorkspace()->create();

    $invitation = Invitation::create([
        'workspace_id' => $user->current_workspace_id,
        'inviter_id' => $user->id,
        'email' => 'resend@example.com',
        'role_name' => 'Member',
        'token' => \Illuminate\Support\Str::ulid(),
        'expires_at' => now()->addDays(7),
        'status' => InvitationStatus::PENDING->value,
    ]);

    $response = $this->actingAs($user)->post(route('invitationResend', $invitation));

    $response->assertRedirect();
    $response->assertSessionHas('flash.status', 'success');

    Event::assertDispatched(InvitationCreated::class);
});

test('cannot resend non-pending invitation', function () {
    $user = User::factory()->withWorkspace()->create();

    $invitation = Invitation::create([
        'workspace_id' => $user->current_workspace_id,
        'inviter_id' => $user->id,
        'email' => 'revoked@example.com',
        'role_name' => 'Member',
        'token' => \Illuminate\Support\Str::ulid(),
        'expires_at' => now()->addDays(7),
        'status' => InvitationStatus::REVOKED->value,
        'revoked_at' => now(),
    ]);

    $response = $this->actingAs($user)->post(route('invitationResend', $invitation));

    $response->assertSessionHasErrors(['invitation']);
});

test('can revoke pending invitation', function () {
    $user = User::factory()->withWorkspace()->create();

    $invitation = Invitation::create([
        'workspace_id' => $user->current_workspace_id,
        'inviter_id' => $user->id,
        'email' => 'revoke@example.com',
        'role_name' => 'Member',
        'token' => \Illuminate\Support\Str::ulid(),
        'expires_at' => now()->addDays(7),
        'status' => InvitationStatus::PENDING->value,
    ]);

    $response = $this->actingAs($user)->delete(route('invitationRevoke', $invitation));

    $response->assertRedirect();
    $response->assertSessionHas('flash.status', 'success');

    $invitation->refresh();

    expect($invitation->status)->toBe(InvitationStatus::REVOKED);
    expect($invitation->revoked_at)->not->toBeNull();
});

test('cannot revoke already revoked invitation', function () {
    $user = User::factory()->withWorkspace()->create();

    $invitation = Invitation::create([
        'workspace_id' => $user->current_workspace_id,
        'inviter_id' => $user->id,
        'email' => 'already-revoked@example.com',
        'role_name' => 'Member',
        'token' => \Illuminate\Support\Str::ulid(),
        'expires_at' => now()->addDays(7),
        'status' => InvitationStatus::REVOKED->value,
        'revoked_at' => now(),
    ]);

    $response = $this->actingAs($user)->delete(route('invitationRevoke', $invitation));

    $response->assertSessionHasErrors(['invitation']);
});

test('cannot access invitation from different workspace', function () {
    $user1 = User::factory()->withWorkspace()->create();
    $user2 = User::factory()->withWorkspace()->create();

    $invitation = Invitation::create([
        'workspace_id' => $user2->current_workspace_id,
        'inviter_id' => $user2->id,
        'email' => 'other-workspace@example.com',
        'role_name' => 'Member',
        'token' => \Illuminate\Support\Str::ulid(),
        'expires_at' => now()->addDays(7),
        'status' => InvitationStatus::PENDING->value,
    ]);

    $response = $this->actingAs($user1)->post(route('invitationResend', $invitation));
    $response->assertForbidden();

    $response = $this->actingAs($user1)->delete(route('invitationRevoke', $invitation));
    $response->assertForbidden();
});

test('invitation model methods work correctly', function () {
    $user = User::factory()->withWorkspace()->create();

    $pendingInvitation = Invitation::create([
        'workspace_id' => $user->current_workspace_id,
        'inviter_id' => $user->id,
        'email' => 'pending@example.com',
        'role_name' => 'Member',
        'token' => \Illuminate\Support\Str::ulid(),
        'expires_at' => now()->addDays(7),
        'status' => InvitationStatus::PENDING->value,
    ]);

    expect($pendingInvitation->isPending())->toBeTrue();
    expect($pendingInvitation->isExpired())->toBeFalse();
    expect($pendingInvitation->isRevoked())->toBeFalse();
    expect($pendingInvitation->isAccepted())->toBeFalse();

    $expiredInvitation = Invitation::create([
        'workspace_id' => $user->current_workspace_id,
        'inviter_id' => $user->id,
        'email' => 'expired@example.com',
        'role_name' => 'Member',
        'token' => \Illuminate\Support\Str::ulid(),
        'expires_at' => now()->subDays(1),
        'status' => InvitationStatus::PENDING->value,
    ]);

    expect($expiredInvitation->isExpired())->toBeTrue();
    expect($expiredInvitation->isPending())->toBeFalse();
});

test('guests cannot invite members', function () {
    $response = $this->post(route('invitationCreate'), [
        'email' => 'test@example.com',
        'role' => 'Member',
    ]);

    $response->assertRedirect(route('login'));
});
