<?php

use App\Enums\Role;

it('orders roles from reader up to god', function () {
    expect(Role::Reader->atLeast(Role::Reader))->toBeTrue()
        ->and(Role::User->atLeast(Role::Reader))->toBeTrue()
        ->and(Role::Admin->atLeast(Role::User))->toBeTrue()
        ->and(Role::God->atLeast(Role::Admin))->toBeTrue()
        ->and(Role::Reader->atLeast(Role::User))->toBeFalse()
        ->and(Role::Admin->atLeast(Role::God))->toBeFalse();
});
