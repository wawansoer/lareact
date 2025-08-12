<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Facades\Socialite;
use Mockery;

uses(RefreshDatabase::class);

test('it redirects to google', function () {
    $response = $this->get(route('google.redirect'));

    $response->assertRedirect();
});

test('it handles google callback and logs in the user', function () {
    $socialiteUser = Mockery::mock(\Laravel\Socialite\Two\User::class);
    $socialiteUser->shouldReceive('getId')->andReturn('12345');
    $socialiteUser->shouldReceive('getName')->andReturn('Test User');
    $socialiteUser->shouldReceive('getEmail')->andReturn('test@example.com');
    $socialiteUser->shouldReceive('getAvatar')->andReturn('http://example.com/avatar.jpg');

    Socialite::shouldReceive('driver->user')->andReturn($socialiteUser);

    $response = $this->get(route('google.callback'));

    $response->assertRedirect('/dashboard');

    $this->assertAuthenticated();
});

test('it handles google callback error', function () {
    Socialite::shouldReceive('driver->user')->andThrow(new \Exception('Test exception'));

    $response = $this->get(route('google.callback'));

    $response->assertRedirect('/login');
    $response->assertSessionHasErrors('email');
    $this->assertGuest();
});
