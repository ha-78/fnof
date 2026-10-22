<?php

namespace App\Enums;

enum Role: string
{
    case God = 'god';
    case Admin = 'admin';
    case User = 'user';
    case Reader = 'reader';

    public function label(): string
    {
        return __('users.roles.'.$this->value);
    }

    public function atLeast(self $role): bool
    {
        return $this->rank() >= $role->rank();
    }

    private function rank(): int
    {
        return match ($this) {
            self::Reader => 1,
            self::User => 2,
            self::Admin => 3,
            self::God => 4,
        };
    }
}
