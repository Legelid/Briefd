<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Laravel\Socialite\Facades\Socialite;

class DiscordAuthController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('discord')
            ->scopes(['identify', 'guilds'])
            ->redirect();
    }

    public function callback()
    {
        $socialiteUser = Socialite::driver('discord')->user();

        $token = $socialiteUser->token;

        $guildsResponse = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('https://discord.com/api/v10/users/@me/guilds');

        $guilds = $guildsResponse->successful() ? $guildsResponse->json() : [];

        session([
            'discord_guilds' => $guilds,
            'discord_token'  => $token,
        ]);

        return redirect()->route('sources', ['discord' => 1]);
    }
}
