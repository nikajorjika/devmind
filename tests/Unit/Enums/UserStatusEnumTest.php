<?php

use App\Enums\Member\MemberStatus;

test('MemberStatus has correct cases', function () {
    expect(MemberStatus::ACTIVE->value)->toBe('Active')
        ->and(MemberStatus::INACTIVE->value)->toBe('Inactive')
        ->and(MemberStatus::PENDING->value)->toBe('Pending')
        ->and(MemberStatus::SUSPENDED->value)->toBe('Suspended');
});

test('MemberStatus description method returns correct descriptions', function () {
    expect(MemberStatus::ACTIVE->description())
        ->toBe('The member is active and has full access.')
        ->and(MemberStatus::INACTIVE->description())
        ->toBe('The member is inactive and cannot access the system.')
        ->and(MemberStatus::PENDING->description())
        ->toBe('The member registration is pending approval.')
        ->and(MemberStatus::SUSPENDED->description())
        ->toBe('The member is suspended due to violations.');

});
