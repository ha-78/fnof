<?php

namespace App\Enums;

use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;

enum Feature: string
{
    case CreateThreads = 'create-threads';
    case ReplyInThreads = 'reply-in-threads';
    case CreateVotes = 'create-votes';
    case VoteInVotes = 'vote-in-votes';
    case CreateNotifications = 'create-notifications';
    case PublishLists = 'publish-lists';
    case PublishListItems = 'publish-list-items';
    case PublishBookmarks = 'publish-bookmarks';
    case AddMealsToWishlist = 'add-meals-to-wishlist';
    case VoteOnMeals = 'vote-on-meals';
    case AddMealsToShortlist = 'add-meals-to-shortlist';
    case PlanMeals = 'plan-meals';

    public function section(): Section
    {
        return match ($this) {
            self::CreateThreads, self::ReplyInThreads, self::CreateVotes, self::VoteInVotes => Section::Threads,
            self::CreateNotifications => Section::Calendar,
            self::PublishLists, self::PublishListItems => Section::Lists,
            self::PublishBookmarks => Section::Bookmarks,
            self::AddMealsToWishlist, self::VoteOnMeals, self::AddMealsToShortlist, self::PlanMeals => Section::Meals,
        };
    }

    public function label(): string
    {
        $key = 'users.features.'.$this->value;

        if (Lang::has($key)) {
            return __($key);
        }

        return Str::headline($this->value);
    }

    public static function groupLabel(Section $section): string
    {
        return match ($section) {
            Section::Calendar => __('welcome.calendar_title'),
            default => $section->label(),
        };
    }

    public function defaultFor(Role $role): bool
    {
        if ($role->atLeast(Role::Admin)) {
            return true;
        }

        return false;
    }

    /**
     * @return list<array{section: Section, label: string, features: list<self>}>
     */
    public static function grouped(): array
    {
        $bySection = [];

        foreach (self::cases() as $feature) {
            $bySection[$feature->section()->value][] = $feature;
        }

        $groups = [];

        foreach (Section::cases() as $section) {
            if (! isset($bySection[$section->value])) {
                continue;
            }

            $groups[] = [
                'section' => $section,
                'label' => self::groupLabel($section),
                'features' => $bySection[$section->value],
            ];
        }

        return $groups;
    }
}
