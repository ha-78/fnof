<?php

namespace App\Enums;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

enum Section: string
{
    case Start = 'start';
    case Threads = 'threads';
    case Calendar = 'calendar';
    case Lists = 'lists';
    case Bookmarks = 'bookmarks';
    case Meals = 'meals';
    case Personal = 'personal';

    public function color(): string
    {
        return match ($this) {
            self::Start => 'd',
            self::Threads => 'a',
            self::Calendar => 'b',
            self::Lists => 'c',
            self::Bookmarks => 'e',
            self::Meals => 'f',
            self::Personal => 'g',
        };
    }

    /**
     * @return list<string>
     */
    public function routes(): array
    {
        return match ($this) {
            self::Start => ['home'],
            self::Threads => ['threads.*', 'replies.*'],
            self::Calendar => [],
            self::Lists => ['lists.*'],
            self::Bookmarks => ['bookmarks.*'],
            self::Meals => ['meals.*'],
            self::Personal => ['account', 'users.*', 'invites.*', 'contexts.*'],
        };
    }

    public function inHeader(): bool
    {
        return match ($this) {
            self::Start, self::Threads, self::Lists, self::Bookmarks, self::Meals, self::Personal => true,
            default => false,
        };
    }

    public function routeName(): ?string
    {
        return match ($this) {
            self::Start => 'home',
            self::Threads => 'threads.index',
            self::Lists => 'lists.index',
            self::Bookmarks => 'bookmarks.index',
            self::Meals => 'meals.index',
            self::Personal => 'account',
            default => null,
        };
    }

    public function isMainPage(?string $routeName = null): bool
    {
        $home = $this->routeName();

        return $home !== null && ($routeName ?? Route::currentRouteName()) === $home;
    }

    public function label(): string
    {
        return match ($this) {
            self::Start => __('home.title'),
            self::Threads => __('threads.threads'),
            self::Lists => __('lists.lists'),
            self::Bookmarks => __('bookmarks.bookmarks'),
            self::Meals => __('meals.meals'),
            self::Personal => __('users.account'),
            default => '',
        };
    }

    public static function current(): self
    {
        $name = Route::currentRouteName();

        if ($name === null) {
            return self::Start;
        }

        foreach (self::cases() as $section) {
            foreach ($section->routes() as $pattern) {
                if (Str::is($pattern, $name)) {
                    return $section;
                }
            }
        }

        return self::Start;
    }

    /**
     * @return list<self>
     */
    public static function header(): array
    {
        return array_values(array_filter(
            self::cases(),
            fn (self $section): bool => $section->inHeader(),
        ));
    }
}
