<?php

use App\Http\Resources\MemberResource;
use App\Models\User;
use App\Models\Workspace;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

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
    ]);

    expect($array['id'])->toBe($user->id);
    expect($array['name'])->toBe($user->name);
    expect($array['email'])->toBe($user->email);
    expect($array['email_verified'])->toBeBool();
    expect($array['two_factor_enabled'])->toBeBool();
});

test('MemberResource includes role information when roles are loaded', function () {
    $user = User::factory()->withWorkspace()->create();
    $user->load('roles');

    $resource = new MemberResource($user);
    $array = $resource->toArray(request());

    expect($array)->toHaveKey('role');
    expect($array)->toHaveKey('is_owner');
});

test('MemberResource handles two factor authentication status', function () {
    $userWithout2FA = User::factory()->withoutTwoFactor()->withWorkspace()->create();
    $userWith2FA = User::factory()->withWorkspace()->create();

    $resourceWithout = new MemberResource($userWithout2FA);
    $resourceWith = new MemberResource($userWith2FA);

    expect($resourceWithout->toArray(request())['two_factor_enabled'])->toBeFalse();
    expect($resourceWith->toArray(request())['two_factor_enabled'])->toBeTrue();
});
