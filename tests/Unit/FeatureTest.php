<?php

use App\Enums\Feature;
use App\Enums\Role;
use App\Enums\Section;
use Tests\TestCase;

uses(TestCase::class);

it('keeps action privileges off until they are granted', function () {
    expect(Feature::CreateThreads->defaultFor(Role::Member))->toBeFalse()
        ->and(Feature::PlanMeals->defaultFor(Role::Reader))->toBeFalse()
        ->and(Feature::CreateThreads->defaultFor(Role::Admin))->toBeTrue()
        ->and(Feature::VoteInVotes->defaultFor(Role::God))->toBeTrue();
});

it('groups privileges with their section', function () {
    $groups = collect(Feature::grouped())->keyBy(fn (array $group): string => $group['section']->value);

    expect($groups->keys()->all())->toBe([
        Section::Threads->value,
        Section::Calendar->value,
        Section::Lists->value,
        Section::Bookmarks->value,
        Section::Meals->value,
    ])
        ->and($groups[Section::Threads->value]['features'])->toContain(Feature::CreateVotes, Feature::ReplyInThreads)
        ->and($groups[Section::Calendar->value]['features'])->toBe([Feature::CreateNotifications]);
});
