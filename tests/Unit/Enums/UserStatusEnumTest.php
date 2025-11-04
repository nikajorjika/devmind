<?php

use App\Enums\Workspace\UserStatusEnum;

test('UserStatusEnum has correct cases', function () {
    expect(UserStatusEnum::ACTIVE->value)->toBe('Active');
    expect(UserStatusEnum::INACTIVE->value)->toBe('Inactive');
    expect(UserStatusEnum::PENDING->value)->toBe('Pending');
    expect(UserStatusEnum::SUSPENDED->value)->toBe('Suspended');
});

test('UserStatusEnum description method returns correct descriptions', function () {
    expect(UserStatusEnum::ACTIVE->description())
        ->toBe('The user is active and has full access.');
    
    expect(UserStatusEnum::INACTIVE->description())
        ->toBe('The user is inactive and cannot access the system.');
    
    expect(UserStatusEnum::PENDING->description())
        ->toBe('The user registration is pending approval.');
    
    expect(UserStatusEnum::SUSPENDED->description())
        ->toBe('The user is suspended due to violations.');
});
