<?php

use App\Http\Resources\InvitationResource;
use App\Models\Invitation;
use App\Models\User;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('InvitationResource transforms invitation data correctly', function () {
    $user = User::factory()->withWorkspace()->create();

    $invitation = Invitation::create([
        'workspace_id' => $user->current_workspace_id,
        'inviter_id' => $user->id,
        'email' => 'test@example.com',
        'role_name' => 'Member',
        'token' => \Illuminate\Support\Str::ulid(),
        'expires_at' => now()->addDays(7),
        'status' => 'pending',
    ]);

    $resource = new InvitationResource($invitation);
    $array = $resource->toArray(request());

    expect($array)->toHaveKeys([
        'id',
        'email',
        'role_name',
        'status',
        'expires_at',
        'created_at',
        'is_expired',
        'is_pending',
    ]);

    expect($array['id'])->toBe($invitation->id);
    expect($array['email'])->toBe($invitation->email);
    expect($array['role_name'])->toBe($invitation->role_name);
    expect($array['status'])->toBe('pending');
    expect($array['is_expired'])->toBeBool();
    expect($array['is_pending'])->toBeBool();
});

test('InvitationResource does not expose sensitive fields', function () {
    $user = User::factory()->withWorkspace()->create();

    $invitation = Invitation::create([
        'workspace_id' => $user->current_workspace_id,
        'inviter_id' => $user->id,
        'email' => 'test@example.com',
        'role_name' => 'Member',
        'token' => \Illuminate\Support\Str::ulid(),
        'expires_at' => now()->addDays(7),
        'status' => 'pending',
    ]);

    $resource = new InvitationResource($invitation);
    $array = $resource->toArray(request());

    // Ensure sensitive fields are not exposed
    expect($array)->not->toHaveKey('token');
    expect($array)->not->toHaveKey('workspace_id');
    expect($array)->not->toHaveKey('inviter_id');
    expect($array)->not->toHaveKey('accepted_by');
    expect($array)->not->toHaveKey('meta');
    expect($array)->not->toHaveKey('updated_at');
});

test('InvitationResource includes inviter when loaded', function () {
    $user = User::factory()->withWorkspace()->create();

    $invitation = Invitation::create([
        'workspace_id' => $user->current_workspace_id,
        'inviter_id' => $user->id,
        'email' => 'test@example.com',
        'role_name' => 'Member',
        'token' => \Illuminate\Support\Str::ulid(),
        'expires_at' => now()->addDays(7),
        'status' => 'pending',
    ]);

    $invitation->load('inviter');

    $resource = new InvitationResource($invitation);
    $array = $resource->toArray(request());

    expect($array)->toHaveKey('inviter');
    expect($array['inviter'])->toBeArray();
    expect($array['inviter'])->toHaveKeys(['id', 'name']);
    expect($array['inviter']['id'])->toBe($user->id);
    expect($array['inviter']['name'])->toBe($user->name);
});

test('InvitationResource helper methods work correctly', function () {
    $user = User::factory()->withWorkspace()->create();

    // Test pending invitation
    $pendingInvitation = Invitation::create([
        'workspace_id' => $user->current_workspace_id,
        'inviter_id' => $user->id,
        'email' => 'pending@example.com',
        'role_name' => 'Member',
        'token' => \Illuminate\Support\Str::ulid(),
        'expires_at' => now()->addDays(7),
        'status' => 'pending',
    ]);

    $resource = new InvitationResource($pendingInvitation);
    $array = $resource->toArray(request());

    expect($array['is_pending'])->toBeTrue();
    expect($array['is_expired'])->toBeFalse();

    // Test expired invitation
    $expiredInvitation = Invitation::create([
        'workspace_id' => $user->current_workspace_id,
        'inviter_id' => $user->id,
        'email' => 'expired@example.com',
        'role_name' => 'Member',
        'token' => \Illuminate\Support\Str::ulid(),
        'expires_at' => now()->subDays(1),
        'status' => 'pending',
    ]);

    $resource = new InvitationResource($expiredInvitation);
    $array = $resource->toArray(request());

    expect($array['is_expired'])->toBeTrue();
    expect($array['is_pending'])->toBeFalse();
});
