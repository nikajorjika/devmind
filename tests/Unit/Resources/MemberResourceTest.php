<?php

use App\Http\Resources\MemberResource;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('MemberResource transforms user data correctly', function () {
    $user = User::factory()->withWorkspace()->create();
    $workspace = $user->currentWorkspace;

    $resource = new MemberResource($user);
    $array = $resource->toArray(request());

    expect($array)->toHaveKeys([
        'id',
        'name',
        'email',
        'email_verified',
        'two_factor_enabled',
    ])->and($array['id'])->toBe($user->id)
        ->and($array['name'])->toBe($user->name)
        ->and($array['email'])->toBe($user->email)
        ->and($array['email_verified'])->toBeBool()
        ->and($array['two_factor_enabled'])->toBeBool();

});

test('MemberResource includes role information when roles are loaded', function () {
    $user = User::factory()->withWorkspace()->create();
    $user->load('roles');

    $resource = new MemberResource($user);
    $array = $resource->toArray(request());

    expect($array)->toHaveKey('role')
        ->and($array)->toHaveKey('is_owner');
});

test('MemberResource handles two factor authentication status', function () {
    $userWithout2FA = User::factory()->withoutTwoFactor()->withWorkspace()->create();
    $userWith2FA = User::factory()->withWorkspace()->create();

    $resourceWithout = new MemberResource($userWithout2FA);
    $resourceWith = new MemberResource($userWith2FA);

    expect($resourceWithout->toArray(request())['two_factor_enabled'])->toBeFalse()
        ->and($resourceWith->toArray(request())['two_factor_enabled'])->toBeTrue();
});
