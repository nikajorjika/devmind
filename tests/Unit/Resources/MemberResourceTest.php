<?php

use App\Http\Resources\MemberResource;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;

uses(RefreshDatabase::class);

function fakePivot(object $model, array $data): void
{
    // Attach a lightweight pivot-like relation for the resource
    $model->setRelation('pivot', (object) $data);
}

function fakeRoles(object $model, array $roles): void
{
    // Attach a lightweight roles relation (bypassing Spatie internals for unit scope)
    // Each role item: ['name' => 'Owner', 'workspace_id' => 123]
    $model->setRelation('roles', collect(array_map(fn ($r) => (object) [
        'name'  => $r['name'],
        'pivot' => (object) ['workspace_id' => $r['workspace_id']],
    ], $roles)));
}

test('MemberResource transforms user data correctly and exposes joined_at & status from pivot', function () {
    $joinedAt = Carbon::now()->subDay();
    $status = 'active';

    $user = User::factory()->withWorkspace()->create();
    $workspaceId = $user->current_workspace_id;

    // Provide the pivot context expected by the resource
    fakePivot($user, [
        'workspace_id' => $workspaceId,
        'created_at'   => $joinedAt,
        'status'       => $status,
    ]);

    $array = (new MemberResource($user))->toArray(request());

    expect($array)->toHaveKeys([
        'id',
        'name',
        'email',
        'email_verified',
        'two_factor_enabled',
        'joined_at',
        'status',
    ])
        ->and($array['id'])->toBe($user->id)
        ->and($array['name'])->toBe($user->name)
        ->and($array['email'])->toBe($user->email)
        ->and($array['email_verified'])->toBeBool()
        ->and($array['two_factor_enabled'])->toBeBool()
        // joined_at/status come from pivot
        ->and($array['status'])->toBe($status)
        ->and($array['joined_at'])->toEqual($joinedAt);
    // Note: Resource returns Carbon instance for joined_at; equality checks by value.
});

test('MemberResource includes role information when roles are loaded and scopes to current workspace', function () {
    $user = User::factory()->withWorkspace()->create();
    $currentWorkspaceId = $user->current_workspace_id;

    // Provide pivot so the resource can resolve workspace context
    fakePivot($user, [
        'workspace_id' => $currentWorkspaceId,
        'created_at'   => now(),
        'status'       => 'active',
    ]);

    // Fake two roles: one for another workspace, one for current
    fakeRoles($user, [
        ['name' => 'Member', 'workspace_id' => 9999],               // should be ignored
        ['name' => 'Owner',  'workspace_id' => $currentWorkspaceId], // should be used
    ]);

    $array = (new MemberResource($user))->toArray(request());

    expect($array)
        ->toHaveKey('role')
        ->and($array['role'])->toBe('Owner')
        ->and($array)->toHaveKey('is_owner')
        ->and($array['is_owner'])->toBeTrue();
});

test('MemberResource returns null role and false is_owner when roles are not loaded', function () {
    $user = User::factory()->withWorkspace()->create();

    // Pivot present but roles NOT loaded
    fakePivot($user, [
        'workspace_id' => $user->current_workspace_id,
        'created_at'   => now(),
        'status'       => 'active',
    ]);

    $array = (new MemberResource($user))->toArray(request());

    expect($array)->toHaveKeys(['role', 'is_owner'])
        ->and($array['role'])->toBeNull()
        ->and($array['is_owner'])->toBeFalse();
});

test('MemberResource handles two factor authentication status', function () {
    $userWithout2FA = User::factory()->withoutTwoFactor()->withWorkspace()->create();
    $userWith2FA    = User::factory()->withWorkspace()->create();

    // Provide minimal pivot for both (so other fields serialize consistently)
    fakePivot($userWithout2FA, [
        'workspace_id' => $userWithout2FA->current_workspace_id,
        'created_at'   => now(),
        'status'       => 'active',
    ]);
    fakePivot($userWith2FA, [
        'workspace_id' => $userWith2FA->current_workspace_id,
        'created_at'   => now(),
        'status'       => 'active',
    ]);

    $resourceWithout = new MemberResource($userWithout2FA);
    $resourceWith = new MemberResource($userWith2FA);

    expect($resourceWithout->toArray(request())['two_factor_enabled'])->toBeFalse()
        ->and($resourceWith->toArray(request())['two_factor_enabled'])->toBeTrue();
});
