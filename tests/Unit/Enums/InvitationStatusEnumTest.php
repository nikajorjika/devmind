<?php

use App\Enums\Invitation\InvitationStatus;

test('InvitationStatus has correct cases', function () {
    expect(InvitationStatus::PENDING->value)->toBe('pending')
        ->and(InvitationStatus::ACCEPTED->value)->toBe('accepted')
        ->and(InvitationStatus::REVOKED->value)->toBe('revoked')
        ->and(InvitationStatus::EXPIRED->value)->toBe('expired');
});

test('InvitationStatus description method returns correct descriptions', function () {
    expect(InvitationStatus::PENDING->description())
        ->toBe('Invitation is pending and awaiting response.')
        ->and(InvitationStatus::ACCEPTED->description())
        ->toBe('Invitation has been accepted by the invitee.')
        ->and(InvitationStatus::REVOKED->description())
        ->toBe('Invitation has been revoked by the inviter.')
        ->and(InvitationStatus::EXPIRED->description())
        ->toBe('Invitation has expired and is no longer valid.');
});
