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

it('shows each primitive in every section color', function () {
    $response = $this->get(route('colors'))
        ->assertSuccessful()
        ->assertSee(__('colors.primitives'))
        ->assertSee(__('colors.primary'))
        ->assertSee(__('colors.tonal'))
        ->assertSee(__('colors.ghost'))
        ->assertSee(__('colors.danger'))
        ->assertSee(__('colors.field'))
        ->assertSee(__('colors.note'))
        ->assertSee(__('colors.add'))
        ->assertSee(__('colors.group'))
        ->assertSee(__('colors.row'))
        ->assertSee(__('colors.meta'))
        ->assertSee(__('colors.open'))
        ->assertSee(__('colors.archive'))
        ->assertSee(__('colors.chip'))
        ->assertSee(__('colors.chip_idle'))
        ->assertSee(__('colors.sheet'))
        ->assertSee(__('colors.open_sheet'))
        ->assertSee(__('colors.sheet_body'))
        ->assertSee(__('colors.close'))
        ->assertSee(__('colors.required'))
        ->assertSee('bg-primary', false)
        ->assertSee('bg-tonal', false)
        ->assertSee('bg-field', false)
        ->assertSee('rounded-field', false)
        ->assertSee('rounded-card', false)
        ->assertSee('rounded-t-sheet', false)
        ->assertSee('role="group"', false)
        ->assertSee('role="dialog"', false)
        ->assertSee('aria-pressed="true"', false)
        ->assertSee('aria-pressed="false"', false);

    foreach (['a', 'b', 'c', 'd', 'e', 'f', 'g'] as $letter) {
        $response->assertSee('data-color="'.$letter.'"', false);
    }
});
