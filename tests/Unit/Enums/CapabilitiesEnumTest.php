<?php

use App\Enums\Workspace\Capabilities;

test('Capabilities enum has correct values', function () {
    expect(Capabilities::MEMBER_SHOW->value)->toBe('member.show')
        ->and(Capabilities::MEMBER_INVITE->value)->toBe('member.invite')
        ->and(Capabilities::MEMBER_REMOVE->value)->toBe('member.remove')
        ->and(Capabilities::MEMBER_CHANGE_ROLE->value)->toBe('member.change_role')
        ->and(Capabilities::MEMBER_MAKE_ADMIN->value)->toBe('member.make_admin')
        ->and(Capabilities::MEMBER_MAKE_OWNER->value)->toBe('member.make_owner')
        ->and(Capabilities::WORKSPACE_UPDATE->value)->toBe('workspace.update')
        ->and(Capabilities::WORKSPACE_DELETE->value)->toBe('workspace.delete');
});

test('Capabilities enum has unique values', function () {
    $values = array_map(fn ($case) => $case->value, Capabilities::cases());

    expect($values)->toHaveCount(count(array_unique($values)));
});
