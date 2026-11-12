<?php

use App\Enums\Section;

it('assigns each section a color letter', function () {
    expect(Section::Start->color())->toBe('d')
        ->and(Section::Threads->color())->toBe('a')
        ->and(Section::Calendar->color())->toBe('b')
        ->and(Section::Lists->color())->toBe('c')
        ->and(Section::Bookmarks->color())->toBe('e')
        ->and(Section::Meals->color())->toBe('f')
        ->and(Section::Personal->color())->toBe('g');
});

it('knows which route is the section home', function () {
    expect(Section::Lists->isMainPage('lists.index'))->toBeTrue()
        ->and(Section::Lists->isMainPage('lists.show'))->toBeFalse()
        ->and(Section::Threads->isMainPage('threads.show'))->toBeFalse()
        ->and(Section::Personal->isMainPage('account'))->toBeTrue()
        ->and(Section::Personal->isMainPage('users.index'))->toBeFalse()
        ->and(Section::Start->isMainPage('home'))->toBeTrue();
});

it('keeps start, threads, lists, bookmarks, meals, and account in the header', function () {
    expect(Section::header())->toBe([
        Section::Start,
        Section::Threads,
        Section::Lists,
        Section::Bookmarks,
        Section::Meals,
        Section::Personal,
    ]);
});

it('maps route names onto their section', function (string $name, Section $section) {
    $matched = collect(Section::cases())->first(
        fn (Section $candidate): bool => collect($candidate->routes())->contains(
            fn (string $pattern): bool => str($name)->is($pattern),
        ),
    ) ?? Section::Start;

    expect($matched)->toBe($section);
})->with([
    'home' => ['home', Section::Start],
    'threads' => ['threads.index', Section::Threads],
    'thread' => ['threads.show', Section::Threads],
    'reply' => ['replies.destroy', Section::Threads],
    'lists' => ['lists.index', Section::Lists],
    'list' => ['lists.show', Section::Lists],
    'list items' => ['lists.items.store', Section::Lists],
    'handled list item' => ['lists.items.handle', Section::Lists],
    'bookmarks' => ['bookmarks.destroy', Section::Bookmarks],
    'meals' => ['meals.index', Section::Meals],
    'account' => ['account', Section::Personal],
    'users' => ['users.show', Section::Personal],
    'invites' => ['invites.store', Section::Personal],
    'context' => ['contexts.update', Section::Personal],
    'theme' => ['device.theme', Section::Start],
]);
