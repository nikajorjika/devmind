<?php

use App\Enums\Workspace\RoleEnum;

test('RoleEnum has correct cases', function () {
    expect(RoleEnum::OWNER->value)->toBe('Owner');
    expect(RoleEnum::ADMIN->value)->toBe('Admin');
    expect(RoleEnum::MEMBER->value)->toBe('Member');
    expect(RoleEnum::GUEST->value)->toBe('Guest');
});

test('RoleEnum description method returns correct descriptions', function () {
    expect(RoleEnum::OWNER->description())
        ->toBe('Administrator with full access to all resources.');
    
    expect(RoleEnum::ADMIN->description())
        ->toBe('User with administrative privileges.');
    
    expect(RoleEnum::MEMBER->description())
        ->toBe('Regular user with standard access.');
    
    expect(RoleEnum::GUEST->description())
        ->toBe('User with limited access to resources.');
});
