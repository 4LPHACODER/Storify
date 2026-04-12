<?php

namespace App\Http\Controllers;

use App\Concerns\ResolvesDefaultAvatar;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Laravel\Socialite\Facades\Socialite;

class SocialiteLoginController extends Controller
{
    use ResolvesDefaultAvatar;

    /**
     * Redirect the user to the Google authentication page.
     */
    public function redirectToGoogle(): RedirectResponse
    {
        return Socialite::driver('google')
            ->with(['prompt' => 'select_account'])
            ->redirect();
    }

    /**
     * Handle the callback from Google after authentication.
     */
    public function handleGoogleCallback(): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();
        } catch (\Exception $e) {
            report($e);

            return redirect()->route('login')->withErrors([
                'socialite' => 'Google authentication failed. Please try again.',
            ]);
        }

        $user = $this->findOrCreateUser($googleUser);

        Auth::login($user);

        return redirect()->intended(route('dashboard'));
    }

    /**
     * Find an existing user by Google ID or email, or create a new one.
     */
    private function findOrCreateUser(\Laravel\Socialite\Contracts\User $googleUser): User
    {
        $user = User::where('google_id', $googleUser->getId())->first();

        if ($user) {
            return $user;
        }

        $user = User::where('email', $googleUser->getEmail())->first();

        if ($user) {
            $avatar = $user->getRawOriginal('avatar') ?: ($googleUser->getAvatar() ?: $this->defaultAvatarUrl($googleUser->getName()));

            $user->update([
                'google_id' => $googleUser->getId(),
                'avatar' => $avatar,
                'role' => $user->role ?: 'customer',
            ]);

            return $user;
        }

        return DB::transaction(function () use ($googleUser): User {
            $avatar = $googleUser->getAvatar() ?: $this->defaultAvatarUrl($googleUser->getName());

            $user = User::create([
                'name' => $googleUser->getName(),
                'email' => $googleUser->getEmail(),
                'google_id' => $googleUser->getId(),
                'avatar' => $avatar,
                'role' => 'customer',
                'email_verified_at' => now(),
            ]);

            event(new Registered($user));

            return $user;
        });
    }
}
