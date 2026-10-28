<?php

use App\Models\User;
use App\Models\UserRememberDevice;
use App\Services\DeviceRememberService;
use App\Support\ColorTheme;
use Illuminate\Cookie\CookieValuePrefix;
use Illuminate\Support\Str;

it('logs a user in with a device cookie and removes that device on logout', function () {
    $user = User::factory()->create([
        'email' => 'ada@example.com',
        'password' => 'password',
    ]);

    $login = $this->post('/login', [
        'email' => 'ada@example.com',
        'password' => 'password',
        'color_theme' => 'dark',
    ]);

    $cookieName = app(DeviceRememberService::class)->cookieName();

    $login->assertCookie($cookieName);

    $device = UserRememberDevice::query()->whereBelongsTo($user)->first();
    $plain = CookieValuePrefix::remove(decrypt($login->getCookie($cookieName)->getValue(), false));

    expect($device)->not->toBeNull()
        ->and($device->color_theme)->toBe('dark')
        ->and($device->token)->toBe(hash('sha256', Str::after($plain, '|')))
        ->and($device->token)->not->toContain('|');

    auth()->logout();
    $this->flushSession();

    $this->withCookie($cookieName, $plain)
        ->get('/')
        ->assertSuccessful();

    expect(auth()->id())->toBe($user->id);

    $this->withCookie($cookieName, $plain)
        ->post(route('logout'))
        ->assertCookieExpired($cookieName);

    expect(UserRememberDevice::query()->whereBelongsTo($user)->exists())->toBeFalse();
});

it('extends a valid device cookie and ignores an expired one', function () {
    $user = User::factory()->create();
    $plainToken = Str::random(64);
    $cookieName = app(DeviceRememberService::class)->cookieName();

    $device = $user->rememberDevices()->create([
        'token' => hash('sha256', $plainToken),
        'device_name' => 'Chrome on Mac',
        'color_theme' => 'dark',
        'last_used_at' => now()->subDay(),
        'expires_at' => now()->addDay(),
    ]);

    auth()->logout();

    $this->withCookie($cookieName, $device->id.'|'.$plainToken)
        ->get('/')
        ->assertSuccessful();

    expect(auth()->id())->toBe($user->id)
        ->and($device->refresh()->expires_at->greaterThan(now()->addDays(6)))->toBeTrue();

    $expired = $user->rememberDevices()->create([
        'token' => hash('sha256', 'expired-token'),
        'expires_at' => now()->subMinute(),
    ]);

    auth()->logout();
    $this->flushSession();

    $this->withCookie($cookieName, $expired->id.'|expired-token')
        ->get('/');

    expect(auth()->check())->toBeFalse();
});

it('does not authenticate a cookie with the wrong token', function () {
    $user = User::factory()->create();
    $device = $user->rememberDevices()->create([
        'token' => hash('sha256', Str::random(64)),
        'expires_at' => now()->addWeek(),
    ]);

    $this->withCookie(app(DeviceRememberService::class)->cookieName(), $device->id.'|not-the-token')
        ->get('/');

    expect(auth()->check())->toBeFalse();
});

it('stores a theme only for the signed-in users device', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();
    $plainToken = Str::random(64);
    $cookieName = app(DeviceRememberService::class)->cookieName();

    $device = $user->rememberDevices()->create([
        'token' => hash('sha256', $plainToken),
        'expires_at' => now()->addWeek(),
    ]);

    $otherDevice = $other->rememberDevices()->create([
        'token' => hash('sha256', Str::random(64)),
        'color_theme' => 'light',
        'expires_at' => now()->addWeek(),
    ]);

    $this->actingAs($user)
        ->withCookie($cookieName, $device->id.'|'.$plainToken)
        ->postJson(route('device.theme'), ['theme' => 'not-a-theme'])
        ->assertNoContent();

    expect($device->fresh()->color_theme)->toBe(ColorTheme::normalize('not-a-theme'));

    $this->actingAs($user)
        ->withCookie($cookieName, $otherDevice->id.'|wrong')
        ->postJson(route('device.theme'), ['theme' => 'dark'])
        ->assertNoContent();

    expect($otherDevice->fresh()->color_theme)->toBe('light');
});

it('removes only the device that logged out', function () {
    $user = User::factory()->create();
    $plainToken = Str::random(64);
    $cookieName = app(DeviceRememberService::class)->cookieName();

    $current = $user->rememberDevices()->create([
        'token' => hash('sha256', $plainToken),
        'expires_at' => now()->addWeek(),
    ]);

    $other = $user->rememberDevices()->create([
        'token' => hash('sha256', Str::random(64)),
        'expires_at' => now()->addWeek(),
    ]);

    $this->actingAs($user)
        ->withCookie($cookieName, $current->id.'|'.$plainToken)
        ->post(route('logout'));

    expect(UserRememberDevice::query()->whereKey($current->id)->exists())->toBeFalse()
        ->and(UserRememberDevice::query()->whereKey($other->id)->exists())->toBeTrue();
});

it('lists signed-in devices on the account page and marks this one', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();
    $plainToken = Str::random(64);
    $cookieName = app(DeviceRememberService::class)->cookieName();

    $current = $user->rememberDevices()->create([
        'token' => hash('sha256', $plainToken),
        'device_name' => 'Chrome on Mac',
        'last_used_at' => now()->subHour(),
        'expires_at' => now()->addDay(),
    ]);

    $user->rememberDevices()->create([
        'token' => hash('sha256', Str::random(64)),
        'device_name' => 'Safari on iPhone',
        'last_used_at' => now()->subDay(),
        'expires_at' => now()->addDay(),
    ]);

    $user->rememberDevices()->create([
        'token' => hash('sha256', Str::random(64)),
        'device_name' => 'Expired phone',
        'last_used_at' => now()->subDays(3),
        'expires_at' => now()->subMinute(),
    ]);

    $other->rememberDevices()->create([
        'token' => hash('sha256', Str::random(64)),
        'device_name' => 'Someone elses laptop',
        'last_used_at' => now(),
        'expires_at' => now()->addDay(),
    ]);

    $this->actingAs($user)
        ->withCookie($cookieName, $current->id.'|'.$plainToken)
        ->get(route('account'))
        ->assertSuccessful()
        ->assertSee('data-table', false)
        ->assertSeeInOrder([
            __('users.sessions'),
            __('users.devices.name'),
            __('users.devices.last_used_at'),
            'Chrome on Mac',
            __('users.devices.current'),
            $current->last_used_at->timezone(config('app.timezone'))->format('Y-m-d H:i'),
            'Safari on iPhone',
        ])
        ->assertDontSee('Expired phone')
        ->assertDontSee('Someone elses laptop')
        ->assertSee(__('users.log_out_hint'));
});

it('says when no devices are signed in', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('account'))
        ->assertSuccessful()
        ->assertSee(__('users.devices.empty'))
        ->assertDontSee(__('users.devices.current'));
});
