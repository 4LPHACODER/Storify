<?php

use App\Models\User;
use Laravel\Socialite\Contracts\Factory as SocialiteFactory;
use Laravel\Socialite\Two\GoogleProvider;
use Laravel\Socialite\Two\User as SocialiteUser;

beforeEach(function () {
    $this->user = (new SocialiteUser)
        ->setToken('test-token')
        ->setRefreshToken('test-refresh-token')
        ->setExpiresIn(3600)
        ->map([
            'id' => '123456789',
            'name' => 'Test User',
            'email' => 'test@example.com',
            'avatar' => 'https://example.com/avatar.jpg',
        ]);
});

it('redirects to Google for authentication', function () {
    $response = $this->get('/auth/google/redirect');

    $response->assertRedirect();
    $response->assertStatus(302);
    $targetUrl = $response->headers->get('Location');
    expect($targetUrl)->toContain('accounts.google.com');
});

it('creates a new user from Google callback', function () {
    $provider = Mockery::mock(GoogleProvider::class);
    $provider->shouldReceive('user')->andReturn($this->user);

    $socialite = $this->app->make(SocialiteFactory::class);
    $socialite->shouldReceive('driver')->with('google')->andReturn($provider);

    $this->get('/auth/google/callback');

    $this->assertDatabaseHas('users', [
        'email' => 'test@example.com',
        'name' => 'Test User',
        'google_id' => '123456789',
        'role' => 'customer',
    ]);
});

it('logs in existing user from Google callback', function () {
    User::factory()->create([
        'email' => 'test@example.com',
        'google_id' => '123456789',
    ]);

    $provider = Mockery::mock(GoogleProvider::class);
    $provider->shouldReceive('user')->andReturn($this->user);

    $socialite = $this->app->make(SocialiteFactory::class);
    $socialite->shouldReceive('driver')->with('google')->andReturn($provider);

    $this->get('/auth/google/callback');

    $this->assertAuthenticated();
    expect(User::where('email', 'test@example.com')->count())->toBe(1);
});

it('links Google account to existing user with same email', function () {
    User::factory()->create([
        'email' => 'test@example.com',
        'google_id' => null,
        'role' => null,
    ]);

    $provider = Mockery::mock(GoogleProvider::class);
    $provider->shouldReceive('user')->andReturn($this->user);

    $socialite = $this->app->make(SocialiteFactory::class);
    $socialite->shouldReceive('driver')->with('google')->andReturn($provider);

    $this->get('/auth/google/callback');

    $user = User::where('email', 'test@example.com')->first();
    expect($user->google_id)->toBe('123456789');
    expect($user->role)->toBe('customer');
    $this->assertAuthenticated();
});

it('redirects authenticated users away from Google redirect', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/auth/google/redirect');

    $response->assertRedirect('/dashboard');
});

it('redirects authenticated users away from Google callback', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/auth/google/callback');

    $response->assertRedirect('/dashboard');
});
