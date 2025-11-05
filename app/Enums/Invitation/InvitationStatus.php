<?php

namespace App\Enums\Invitation;

enum InvitationStatus: string
{
    case PENDING = 'pending';
    case ACCEPTED = 'accepted';
    case REVOKED = 'revoked';
    case EXPIRED = 'expired';

    public function description(): string
    {
        return match ($this) {
            self::PENDING => 'Invitation is pending and awaiting response.',
            self::ACCEPTED => 'Invitation has been accepted by the invitee.',
            self::REVOKED => 'Invitation has been revoked by the inviter.',
            self::EXPIRED => 'Invitation has expired and is no longer valid.',
        };
    }

    public function labels(): string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::ACCEPTED => 'Accepted',
            self::REVOKED => 'Revoked',
            self::EXPIRED => 'Expired',
        };
    }
}
