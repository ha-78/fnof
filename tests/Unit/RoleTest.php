<?php

use App\Enums\Role;

it('orders roles from reader up to god', function () {
    expect(Role::Reader->atLeast(Role::Reader))->toBeTrue()
        ->and(Role::Member->atLeast(Role::Reader))->toBeTrue()
        ->and(Role::Admin->atLeast(Role::Member))->toBeTrue()
        ->and(Role::God->atLeast(Role::Admin))->toBeTrue()
        ->and(Role::Reader->atLeast(Role::Member))->toBeFalse()
        ->and(Role::Admin->atLeast(Role::God))->toBeFalse();
});
