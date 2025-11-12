<?php

use App\Enums\Invitation\InvitationStatus;
use App\Models\Invitation;
use App\Models\User;
use Illuminate\Support\Str;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('user can join with valid invitation token', function () {
    $inviter = User::factory()->withWorkspace()->create();
    $invitee = User::factory()->create(['email' => 'invitee@example.com']);

    $invitation = Invitation::create([
        'workspace_id' => $inviter->current_workspace_id,
        'inviter_id' => $inviter->id,
        'email' => 'invitee@example.com',
        'role_name' => 'Member',
        'token' => Str::ulid(),
        'expires_at' => now()->addDays(7),
        'status' => InvitationStatus::PENDING->value,
    ]);

    $response = $this->actingAs($invitee)->get(route('invitation.join', $invitation->token));

    $response->assertRedirect(route('invitation.accept', $invitation->token));
    expect(session('invitation.token'))->toBe((string) $invitation->token);
});

test('guest is redirected to login when joining invitation', function () {
    $inviter = User::factory()->withWorkspace()->create();

    $invitation = Invitation::create([
        'workspace_id' => $inviter->current_workspace_id,
        'inviter_id' => $inviter->id,
        'email' => 'guest@example.com',
        'role_name' => 'Member',
        'token' => Str::ulid(),
        'expires_at' => now()->addDays(7),
        'status' => InvitationStatus::PENDING->value,
    ]);

    $response = $this->get(route('invitation.join', $invitation->token));

    $response->assertRedirect(route('login'));
    expect(session('url.intended'))->toBe(route('invitation.accept', ['token' => $invitation->token]));
});

test('cannot join with expired invitation token', function () {
    $inviter = User::factory()->withWorkspace()->create();

    $invitation = Invitation::create([
        'workspace_id' => $inviter->current_workspace_id,
        'inviter_id' => $inviter->id,
        'email' => 'expired@example.com',
        'role_name' => 'Member',
        'token' => Str::ulid(),
        'expires_at' => now()->subDay(),
        'status' => InvitationStatus::PENDING->value,
    ]);

    $response = $this->get(route('invitation.join', $invitation->token));

    $response->assertRedirect(route('login'));
    $response->assertSessionHas('flash.status', 'warning');
});

test('cannot join with revoked invitation token', function () {
    $inviter = User::factory()->withWorkspace()->create();

    $invitation = Invitation::create([
        'workspace_id' => $inviter->current_workspace_id,
        'inviter_id' => $inviter->id,
        'email' => 'revoked@example.com',
        'role_name' => 'Member',
        'token' => Str::ulid(),
        'expires_at' => now()->addDays(7),
        'status' => InvitationStatus::REVOKED->value,
        'revoked_at' => now(),
    ]);

    $response = $this->get(route('invitation.join', $invitation->token));

    $response->assertRedirect(route('login'));
    $response->assertSessionHas('flash.status', 'warning');
});

test('user can approve invitation with matching email', function () {
    $inviter = User::factory()->withWorkspace()->create();
    $invitee = User::factory()->create(['email' => 'invitee@example.com']);

    $invitation = Invitation::create([
        'workspace_id' => $inviter->current_workspace_id,
        'inviter_id' => $inviter->id,
        'email' => 'invitee@example.com',
        'role_name' => 'Member',
        'token' => Str::ulid(),
        'expires_at' => now()->addDays(7),
        'status' => InvitationStatus::PENDING->value,
    ]);

    $response = $this->actingAs($invitee)->post(route('invitation.approve', $invitation->token));

    $response->assertRedirect(route('dashboard'));
    $response->assertSessionHas('flash.status', 'success');

    $invitation->refresh();
    expect($invitation->status)->toBe(InvitationStatus::ACCEPTED);
    expect($invitation->accepted_by)->toBe($invitee->id);
    expect($invitation->accepted_at)->not->toBeNull();

    // Check user was added to workspace
    $invitee->refresh();
    expect($invitee->workspaces()->where('workspaces.id', $inviter->current_workspace_id)->exists())->toBeTrue();
});

test('cannot approve invitation with mismatched email', function () {
    $inviter = User::factory()->withWorkspace()->create();
    $wrongUser = User::factory()->create(['email' => 'wrong@example.com']);

    $invitation = Invitation::create([
        'workspace_id' => $inviter->current_workspace_id,
        'inviter_id' => $inviter->id,
        'email' => 'correct@example.com',
        'role_name' => 'Member',
        'token' => Str::ulid(),
        'expires_at' => now()->addDays(7),
        'status' => InvitationStatus::PENDING->value,
    ]);

    $response = $this->actingAs($wrongUser)->post(route('invitation.approve', $invitation->token));

    $response->assertRedirect(route('invitation.accept', $invitation->token));
    $response->assertSessionHas('flash.title', 'Email mismatch');

    $invitation->refresh();
    expect($invitation->status)->toBe(InvitationStatus::PENDING);
});

test('cannot approve expired invitation', function () {
    $inviter = User::factory()->withWorkspace()->create();
    $invitee = User::factory()->create(['email' => 'invitee@example.com']);

    $invitation = Invitation::create([
        'workspace_id' => $inviter->current_workspace_id,
        'inviter_id' => $inviter->id,
        'email' => 'invitee@example.com',
        'role_name' => 'Member',
        'token' => Str::ulid(),
        'expires_at' => now()->subDay(),
        'status' => InvitationStatus::PENDING->value,
    ]);

    $response = $this->actingAs($invitee)->post(route('invitation.approve', $invitation->token));

    $response->assertRedirect(route('invitation.accept', $invitation->token));
    $response->assertSessionHas('flash.title', 'Invitation invalid');
});

test('can approve invitation with case-insensitive email match', function () {
    $inviter = User::factory()->withWorkspace()->create();
    $invitee = User::factory()->create(['email' => 'InViTee@Example.com']);

    $invitation = Invitation::create([
        'workspace_id' => $inviter->current_workspace_id,
        'inviter_id' => $inviter->id,
        'email' => 'invitee@example.com',
        'role_name' => 'Member',
        'token' => Str::ulid(),
        'expires_at' => now()->addDays(7),
        'status' => InvitationStatus::PENDING->value,
    ]);

    $response = $this->actingAs($invitee)->post(route('invitation.approve', $invitation->token));

    $response->assertRedirect(route('dashboard'));
    $response->assertSessionHas('flash.status', 'success');
});

test('user can reject invitation', function () {
    $inviter = User::factory()->withWorkspace()->create();
    $invitee = User::factory()->create(['email' => 'invitee@example.com']);

    $invitation = Invitation::create([
        'workspace_id' => $inviter->current_workspace_id,
        'inviter_id' => $inviter->id,
        'email' => 'invitee@example.com',
        'role_name' => 'Member',
        'token' => Str::ulid(),
        'expires_at' => now()->addDays(7),
        'status' => InvitationStatus::PENDING->value,
    ]);

    $response = $this->actingAs($invitee)->delete(route('invitation.reject', $invitation->token));

    $response->assertRedirect(route('dashboard'));
    $response->assertSessionHas('flash.title', 'Invitation declined');

    $invitation->refresh();
    expect($invitation->status)->toBe(InvitationStatus::DECLINED);
    expect($invitation->accepted_at)->toBeNull();
    expect($invitation->accepted_by)->toBeNull();
});

test('cannot reject invitation with mismatched email', function () {
    $inviter = User::factory()->withWorkspace()->create();
    $wrongUser = User::factory()->create(['email' => 'wrong@example.com']);

    $invitation = Invitation::create([
        'workspace_id' => $inviter->current_workspace_id,
        'inviter_id' => $inviter->id,
        'email' => 'correct@example.com',
        'role_name' => 'Member',
        'token' => Str::ulid(),
        'expires_at' => now()->addDays(7),
        'status' => InvitationStatus::PENDING->value,
    ]);

    $response = $this->actingAs($wrongUser)->delete(route('invitation.reject', $invitation->token));

    $response->assertRedirect(route('invitation.accept', $invitation->token));
    $response->assertSessionHas('flash.title', 'Email mismatch');
});

test('already accepted invitation returns error', function () {
    $inviter = User::factory()->withWorkspace()->create();
    $invitee = User::factory()->create(['email' => 'invitee@example.com']);

    $invitation = Invitation::create([
        'workspace_id' => $inviter->current_workspace_id,
        'inviter_id' => $inviter->id,
        'email' => 'invitee@example.com',
        'role_name' => 'Member',
        'token' => Str::ulid(),
        'expires_at' => now()->addDays(7),
        'status' => InvitationStatus::ACCEPTED->value,
        'accepted_at' => now(),
        'accepted_by' => $invitee->id,
    ]);

    $response = $this->actingAs($invitee)->post(route('invitation.approve', $invitation->token));

    $response->assertRedirect(route('invitation.accept', $invitation->token));
    $response->assertSessionHas('flash.title', 'Invitation invalid');
});

test('already declined invitation returns error', function () {
    $inviter = User::factory()->withWorkspace()->create();
    $invitee = User::factory()->create(['email' => 'invitee@example.com']);

    $invitation = Invitation::create([
        'workspace_id' => $inviter->current_workspace_id,
        'inviter_id' => $inviter->id,
        'email' => 'invitee@example.com',
        'role_name' => 'Member',
        'token' => Str::ulid(),
        'expires_at' => now()->addDays(7),
        'status' => InvitationStatus::DECLINED->value,
    ]);

    $response = $this->actingAs($invitee)->delete(route('invitation.reject', $invitation->token));

    $response->assertRedirect(route('invitation.accept', $invitation->token));
    $response->assertSessionHas('flash.title', 'Invitation invalid');
});

test('cannot use same token twice from different users', function () {
    $inviter = User::factory()->withWorkspace()->create();
    $invitee = User::factory()->create(['email' => 'invitee@example.com']);
    $attacker = User::factory()->create(['email' => 'attacker@example.com']);

    $invitation = Invitation::create([
        'workspace_id' => $inviter->current_workspace_id,
        'inviter_id' => $inviter->id,
        'email' => 'invitee@example.com',
        'role_name' => 'Member',
        'token' => Str::ulid(),
        'expires_at' => now()->addDays(7),
        'status' => InvitationStatus::PENDING->value,
    ]);

    // Attacker tries to use the token
    $response = $this->actingAs($attacker)->post(route('invitation.approve', $invitation->token));

    $response->assertRedirect(route('invitation.accept', $invitation->token));
    $response->assertSessionHas('flash.title', 'Email mismatch');

    // Invitation should still be pending
    $invitation->refresh();
    expect($invitation->status)->toBe(InvitationStatus::PENDING);
});
