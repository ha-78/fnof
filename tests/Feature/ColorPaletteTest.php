<?php

it('shows a patch for every palette step', function () {
    $this->get(route('colors'))
        ->assertSuccessful()
        ->assertSee(__('colors.title'))
        ->assertSee(__('colors.green'))
        ->assertSee(__('colors.burgundy'))
        ->assertSee(__('colors.yellow'))
        ->assertSee(__('colors.brown'))
        ->assertSee(__('colors.turquoise'))
        ->assertSee(__('colors.purple'))
        ->assertSee(__('colors.blue'))
        ->assertSee('--color-a-1')
        ->assertSee('--color-a-5')
        ->assertSee('--color-b-3')
        ->assertSee('--color-c-5')
        ->assertSee('--color-d-1')
        ->assertSee('--color-d-5')
        ->assertSee('--color-e-2')
        ->assertSee('--color-f-4')
        ->assertSee('--color-g-5');
});
