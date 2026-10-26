<?php

it('shows a patch for every palette step', function () {
    $this->get(route('colors'))
        ->assertSuccessful()
        ->assertSee(__('colors.title'))
        ->assertSee(__('colors.warm_green'))
        ->assertSee(__('colors.burgundy'))
        ->assertSee(__('colors.gold'))
        ->assertSee(__('colors.brown'))
        ->assertSee('--color-a-1')
        ->assertSee('--color-a-5')
        ->assertSee('--color-b-3')
        ->assertSee('--color-c-5')
        ->assertSee('--color-d-1')
        ->assertSee('--color-d-5');
});
