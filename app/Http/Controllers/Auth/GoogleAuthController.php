<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    public function redirect(): RedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback(): RedirectResponse
    {
        $googleUser = Socialite::driver('google')->user();

        $user = User::updateOrCreate(
            ['google_id' => $googleUser->getId()],
            [
                'name' => $googleUser->getName(),
                'email' => $googleUser->getEmail(),
                'google_id' => $googleUser->getId(),
                'email_verified_at' => now(),
                'plan' => 'free',
                'password' => bcrypt(\Illuminate\Support\Str::random(24)),
            ]
        );

        // Also match by email if google_id not found
        if (! $user->wasRecentlyCreated && ! $user->google_id) {
            $user->update(['google_id' => $googleUser->getId(), 'email_verified_at' => $user->email_verified_at ?? now()]);
        }

        if ($user->workspaces()->count() === 0) {
            $workspace = $user->workspaces()->create([
                'name' => $user->name . "'s Workspace",
            ]);
            session(['current_workspace_id' => $workspace->id]);
        } else {
            session(['current_workspace_id' => $user->workspaces()->first()->id]);
        }

        Auth::login($user);

        return redirect()->route('dashboard');
    }
}
