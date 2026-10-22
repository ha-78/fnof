<?php

namespace App\Enums;

use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;

enum Feature: string
{
    public function label(): string
    {
        $key = 'users.features.'.$this->value;

        if (Lang::has($key)) {
            return __($key);
        }

        return Str::headline($this->value);
    }

    public function defaultFor(Role $role): bool
    {
        if ($role->atLeast(Role::Admin)) {
            return true;
        }

        return match ($this) {
            default => $role === Role::User,
        };
    }
}
