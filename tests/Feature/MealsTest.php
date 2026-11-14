<?php

use App\Enums\Feature;
use App\Enums\MealStatus;
use App\Enums\Role;
use App\Models\Context;
use App\Models\Meal;
use App\Models\User;

it('asks guests to log in before meals', function () {
    $this->get(route('meals.index'))
        ->assertRedirect(route('login'));

    $this->post(route('meals.store'), [
        'name' => 'Pasta',
        'comment' => 'Med citron',
    ])->assertRedirect(route('login'));
});

it('shows the meals form in the right column and the meals color', function () {
    $this->actingAs(User::factory()->withFeatures(Feature::AddMealsToWishlist)->create())
        ->get(route('meals.index'))
        ->assertSuccessful()
        ->assertSee('data-section="f"', false)
        ->assertSee('data-color="f"', false)
        ->assertDontSee('--header-light', false)
        ->assertSee('href="'.route('meals.index').'"', false)
        ->assertSee(__('meals.meals'), false)
        ->assertSee('M13 .5c0-.276-.226-.506-.498-.465', false)
        ->assertSee('bg-white text-field-ink', false)
        ->assertDontSee('data-color="f" class="grid size-9', false)
        ->assertSee('aria-current="page"', false)
        ->assertSee('md:grid-cols-2', false)
        ->assertSee(__('meals.prompt'), false)
        ->assertSee('meals-form', false)
        ->assertSee('action="'.route('meals.store').'"', false)
        ->assertSee('bg-[var(--color-f-5)]', false)
        ->assertSee('text-[var(--color-f-1)]', false)
        ->assertSee('<textarea id="name" name="name" rows="3" required', false)
        ->assertSee('<textarea id="comment" name="comment" rows="4" maxlength="10000"', false)
        ->assertSee(__('meals.recipe_link'))
        ->assertSee(__('meals.comment'))
        ->assertDontSee('name="status"', false)
        ->assertDontSee(MealStatus::Wishlist->label())
        ->assertDontSee(MealStatus::Shortlist->label())
        ->assertDontSee(MealStatus::Planned->label())
        ->assertDontSee(MealStatus::Discarded->label())
        ->assertSee(__('meals.empty'));
});

it('lists wishlist meals under the form', function () {
    $owner = User::factory()->withFeatures(Feature::AddMealsToWishlist)->create(['name' => 'Ada']);
    $createdAt = now()->timezone(config('app.timezone'))->setTime(15, 45);
    Meal::factory()->for($owner)->create([
        'name' => 'Pasta',
        'recipe_link' => 'https://example.com/pasta',
        'comment' => 'Med citron',
        'status' => MealStatus::Wishlist,
        'created_at' => $createdAt,
    ]);
    Meal::factory()->for($owner)->create([
        'name' => 'Soppa',
        'status' => MealStatus::Shortlist,
    ]);
    Meal::factory()->for($owner)->create([
        'name' => 'Tacos',
        'recipe_link' => null,
        'comment' => null,
        'status' => MealStatus::Wishlist,
        'created_at' => $createdAt->copy()->addMinute(),
    ]);

    $response = $this->actingAs($owner)
        ->get(route('meals.index'))
        ->assertSuccessful()
        ->assertSee('meals-card', false)
        ->assertSee('rounded-3xl p-3', false)
        ->assertSee('x-data="{ open: false }"', false)
        ->assertSee(':aria-expanded="open.toString()"', false)
        ->assertSee('x-show="open"', false)
        ->assertSee('m19.5 8.25-7.5 7.5-7.5-7.5', false)
        ->assertSee('text-right text-sm italic', false)
        ->assertSee('Pasta')
        ->assertSee('Tacos')
        ->assertSee('href="https://example.com/pasta"', false)
        ->assertSee('Med citron')
        ->assertSee($createdAt->toDateString())
        ->assertSee('Ada')
        ->assertDontSee('Soppa')
        ->assertDontSee(__('meals.empty'));

    expect($response->getContent())
        ->toMatch('/meals-form.*Tacos.*Pasta/s')
        ->and(substr_count($response->getContent(), 'm19.5 8.25-7.5 7.5-7.5-7.5'))->toBe(1);
});

it('saves a new meal on the wishlist', function () {
    $user = User::factory()->withFeatures(Feature::AddMealsToWishlist)->create();

    $this->actingAs($user)
        ->post(route('meals.store'), [
            'name' => ' Pasta ',
            'recipe_link' => ' https://example.com/pasta ',
            'comment' => ' Med citron ',
        ])
        ->assertRedirect(route('meals.index'))
        ->assertSessionHas('status', __('meals.created'));

    $meal = Meal::query()->sole();

    expect($meal->name)->toBe('Pasta')
        ->and($meal->recipe_link)->toBe('https://example.com/pasta')
        ->and($meal->comment)->toBe('Med citron')
        ->and($meal->status)->toBe(MealStatus::Wishlist)
        ->and($meal->user_id)->toBe($user->id)
        ->and($meal->context_id)->toBe($user->currentContextId());

    $this->actingAs($user)
        ->get(route('meals.index'))
        ->assertSuccessful()
        ->assertSee(__('meals.created'));
});

it('keeps a posted meal on the wishlist', function () {
    $user = User::factory()->withFeatures(Feature::AddMealsToWishlist)->create();

    $this->actingAs($user)
        ->post(route('meals.store'), [
            'name' => 'Soppa',
            'recipe_link' => '   ',
            'comment' => '   ',
            'status' => MealStatus::Shortlist->value,
        ])
        ->assertRedirect(route('meals.index'));

    $meal = Meal::query()->sole();

    expect($meal->recipe_link)->toBeNull()
        ->and($meal->comment)->toBeNull()
        ->and($meal->status)->toBe(MealStatus::Wishlist);
});

it('requires only a name', function () {
    $this->actingAs(User::factory()->withFeatures(Feature::AddMealsToWishlist)->create())
        ->post(route('meals.store'), [
            'name' => '   ',
            'recipe_link' => 'not a url',
            'comment' => '   ',
            'status' => 'cooked',
        ])
        ->assertSessionHasErrors(['name', 'recipe_link'])
        ->assertSessionDoesntHaveErrors(['comment', 'status']);

    expect(Meal::query()->count())->toBe(0);
});

it('keeps meals inside the household', function () {
    $user = User::factory()->create();
    $elsewhere = Context::query()->create(['name' => 'Annan']);

    Meal::factory()->for($user)->create([
        'name' => 'Hemmamat',
        'context_id' => $user->currentContextId(),
    ]);
    Meal::factory()->create([
        'name' => 'Grannens mat',
        'context_id' => $elsewhere->id,
    ]);

    $this->actingAs($user)
        ->get(route('meals.index'))
        ->assertSuccessful()
        ->assertSee('Hemmamat')
        ->assertDontSee('Grannens mat');
});

it('lets only those with the wishlist permission add a meal', function () {
    $member = User::factory()->create();
    $reader = User::factory()->withRole(Role::Reader)->create();
    $admin = User::factory()->withRole(Role::Admin)->create();

    foreach ([$member, $reader] as $user) {
        $this->actingAs($user)
            ->get(route('meals.index'))
            ->assertSuccessful()
            ->assertDontSee('action="'.route('meals.store').'"', false);

        $this->actingAs($user)
            ->post(route('meals.store'), ['name' => 'Pasta'])
            ->assertForbidden();
    }

    expect(Meal::query()->count())->toBe(0);

    $this->actingAs($admin)
        ->post(route('meals.store'), ['name' => 'Pasta'])
        ->assertRedirect(route('meals.index'));

    expect(Meal::query()->sole()->context_id)->toBe($admin->currentContextId());
});
