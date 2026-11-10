<?php

namespace App\Enums;

enum MealStatus: string
{
    case Wishlist = 'wishlist';
    case Shortlist = 'shortlist';
    case Planned = 'planned';
    case Discarded = 'discarded';

    public function label(): string
    {
        return __('meals.statuses.'.$this->value);
    }
}
