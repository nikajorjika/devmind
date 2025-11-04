<?php

namespace App\Enums\Workspace;

enum UserStatusEnum: string
{
    case ACTIVE = 'Active';
    case INACTIVE = 'Inactive';
    case PENDING = 'Pending';
    case SUSPENDED = 'Suspended';

    public function description(): string
    {
        return match ($this) {
            self::ACTIVE => 'The user is active and has full access.',
            self::INACTIVE => 'The user is inactive and cannot access the system.',
            self::PENDING => 'The user registration is pending approval.',
            self::SUSPENDED => 'The user is suspended due to violations.',
        };
    }
}
