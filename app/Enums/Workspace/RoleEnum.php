<?php

namespace App\Enums\Workspace;

enum RoleEnum: string
{
    case OWNER = 'Owner';
    case ADMIN = 'Admin';
    case MEMBER = 'Member';
    case GUEST = 'Guest';

    public function description(): string
    {
        return match ($this) {
            self::OWNER => 'Administrator with full access to all resources.',
            self::ADMIN => 'User with administrative privileges.',
            self::MEMBER => 'Regular user with standard access.',
            self::GUEST => 'User with limited access to resources.',
        };
    }

    public function labels(): string
    {
        return match ($this) {
            self::OWNER => 'Owner',
            self::ADMIN => 'Admin',
            self::MEMBER => 'Member',
            self::GUEST => 'Guest',
        };
    }

}
