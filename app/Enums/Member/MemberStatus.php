<?php

namespace App\Enums\Member;

enum MemberStatus: string
{
    case ACTIVE = 'Active';
    case INACTIVE = 'Inactive';
    case PENDING = 'Pending';
    case SUSPENDED = 'Suspended';

    public function description(): string
    {
        return match ($this) {
            self::ACTIVE => 'The member is active and has full access.',
            self::INACTIVE => 'The member is inactive and cannot access the system.',
            self::PENDING => 'The member registration is pending approval.',
            self::SUSPENDED => 'The member is suspended due to violations.',
        };
    }

    public function labels(): string
    {
        return match ($this) {
            self::ACTIVE => 'Active',
            self::INACTIVE => 'Inactive',
            self::PENDING => 'Pending',
            self::SUSPENDED => 'Suspended',
        };
    }
}
