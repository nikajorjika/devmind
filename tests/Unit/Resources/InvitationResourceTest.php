<?php

use App\Http\Resources\InvitationResource;
use App\Models\Invitation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

uses(RefreshDatabase::class);

test('InvitationResource transforms invitation data correctly', function () {
    $user = User::factory()->withWorkspace()->create();

    $invitation = Invitation::create([
        'workspace_id' => $user->current_workspace_id,
        'inviter_id' => $user->id,
        'email' => 'test@example.com',
        'role_name' => 'Member',
        'token' => Str::ulid(),
        'expires_at' => now()->addDays(7),
        'status' => 'pending',
    ]);

    $array = (new InvitationResource($invitation))->toArray(request());

    expect($array)->toHaveKeys([
        'id',
        'email',
        'role_name',
        'status',
        'expires_at',
        'created_at',
        'is_expired',
        'is_pending',
        'revoked_at',
    ])
        ->and($array['id'])->toBe($invitation->id)
        ->and($array['email'])->toBe($invitation->email)
        ->and($array['role_name'])->toBe($invitation->role_name)
        ->and($array['status'])->toBe('pending')
        // timestamps are ISO strings from the resource
        ->and($array['expires_at'])->toBe($invitation->expires_at->toISOString())
        ->and($array['created_at'])->toBe($invitation->created_at->toISOString())
        ->and($array['revoked_at'])->toBeNull()
        // flags
        ->and($array['is_expired'])->toBeBool()
        ->and($array['is_pending'])->toBeBool();
});

test('InvitationResource does not expose sensitive fields', function () {
    $user = User::factory()->withWorkspace()->create();

    $invitation = Invitation::create([
        'workspace_id' => $user->current_workspace_id,
        'inviter_id' => $user->id,
        'email' => 'test@example.com',
        'role_name' => 'Member',
        'token' => Str::ulid(),
        'expires_at' => now()->addDays(7),
        'status' => 'pending',
    ]);

    $array = (new InvitationResource($invitation))->toArray(request());

    expect($array)
        ->not->toHaveKey('token')
        ->and($array)->not->toHaveKey('workspace_id')
        ->and($array)->not->toHaveKey('inviter_id')
        ->and($array)->not->toHaveKey('accepted_by')
        ->and($array)->not->toHaveKey('meta')
        ->and($array)->not->toHaveKey('updated_at');
});

test('InvitationResource includes inviter when loaded', function () {
    $user = User::factory()->withWorkspace()->create();

    $invitation = Invitation::create([
        'workspace_id' => $user->current_workspace_id,
        'inviter_id' => $user->id,
        'email' => 'test@example.com',
        'role_name' => 'Member',
        'token' => Str::ulid(),
        'expires_at' => now()->addDays(7),
        'status' => 'pending',
    ]);

    $invitation->load('inviter');

    $array = (new InvitationResource($invitation))->toArray(request());

    expect($array)->toHaveKey('inviter')
        ->and($array['inviter'])->toBeArray()
        ->and($array['inviter'])->toHaveKeys(['id', 'name'])
        ->and($array['inviter']['id'])->toBe($user->id)
        ->and($array['inviter']['name'])->toBe($user->name);
});

test('InvitationResource includes workspace when loaded', function () {
    $user = User::factory()->withWorkspace()->create();

    $invitation = Invitation::create([
        'workspace_id' => $user->current_workspace_id,
        'inviter_id' => $user->id,
        'email' => 'test@example.com',
        'role_name' => 'Member',
        'token' => Str::ulid(),
        'expires_at' => now()->addDays(7),
        'status' => 'pending',
    ]);

    $invitation->load('workspace');

    $array = (new InvitationResource($invitation))->toArray(request());

    expect($array)->toHaveKey('workspace')
        ->and($array['workspace'])->toBeArray()
        ->and($array['workspace'])->toHaveKeys(['id', 'name'])
        ->and($array['workspace']['id'])->toBe($user->currentWorkspace->id)
        ->and($array['workspace']['name'])->toBe($user->currentWorkspace->name);
});

test('InvitationResource helper flags for pending, expired, and revoked', function () {
    $user = User::factory()->withWorkspace()->create();

    // Pending (future expiry)
    $pending = Invitation::create([
        'workspace_id' => $user->current_workspace_id,
        'inviter_id' => $user->id,
        'email' => 'pending@example.com',
        'role_name' => 'Member',
        'token' => Str::ulid(),
        'expires_at' => now()->addDays(7),
        'status' => 'pending',
    ]);
    $pendingArr = (new InvitationResource($pending))->toArray(request());
    expect($pendingArr['is_pending'])->toBeTrue()
        ->and($pendingArr['is_expired'])->toBeFalse()
        ->and($pendingArr['revoked_at'])->toBeNull();

    // Expired (past expiry)
    $expired = Invitation::create([
        'workspace_id' => $user->current_workspace_id,
        'inviter_id' => $user->id,
        'email' => 'expired@example.com',
        'role_name' => 'Member',
        'token' => Str::ulid(),
        'expires_at' => now()->subDay(),
        'status' => 'pending',
    ]);
    $expiredArr = (new InvitationResource($expired))->toArray(request());
    expect($expiredArr['is_expired'])->toBeTrue()
        ->and($expiredArr['is_pending'])->toBeFalse();

    // Revoked (explicitly revoked)
    $revoked = Invitation::create([
        'workspace_id' => $user->current_workspace_id,
        'inviter_id' => $user->id,
        'email' => 'revoked@example.com',
        'role_name' => 'Member',
        'token' => Str::ulid(),
        'expires_at' => now()->addDays(7),
        'status' => 'revoked',
        'revoked_at' => now(),
    ]);
    $revokedArr = (new InvitationResource($revoked))->toArray(request());
    expect($revokedArr['status'])->toBe('revoked')
        ->and($revokedArr['revoked_at'])->toBe($revoked->revoked_at->toISOString())
        ->and($revokedArr['is_pending'])->toBeFalse();
});
