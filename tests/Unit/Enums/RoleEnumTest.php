<?php

use App\Enums\Workspace\RoleEnum;

test('RoleEnum has correct cases', function () {
    expect(RoleEnum::OWNER->value)->toBe('Owner')
        ->and(RoleEnum::ADMIN->value)->toBe('Admin')
        ->and(RoleEnum::MEMBER->value)->toBe('Member')
        ->and(RoleEnum::GUEST->value)->toBe('Guest');
});

test('RoleEnum description method returns correct descriptions', function () {
    expect(RoleEnum::OWNER->description())
        ->toBe('Administrator with full access to all resources.')
        ->and(RoleEnum::ADMIN->description())
        ->toBe('User with administrative privileges.')
        ->and(RoleEnum::MEMBER->description())
        ->toBe('Regular user with standard access.')
        ->and(RoleEnum::GUEST->description())
        ->toBe('User with limited access to resources.');

});
