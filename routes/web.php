<?php

use App\Http\Controllers\Auth\DiscordAuthController;
use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UnsubscribeController;
use App\Http\Controllers\WorkspaceController;
use App\Livewire\Dashboard;
use App\Livewire\Digests\DigestList;
use App\Livewire\Digests\DigestPreview;
use App\Livewire\Digests\ScheduledDigests;
use App\Livewire\Settings;
use App\Livewire\Sources\SourceList;
use App\Livewire\Subscribers\SubscriberList;
use App\Livewire\Workspaces\WorkspaceList;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Google OAuth
Route::get('/auth/google', [GoogleAuthController::class, 'redirect'])->name('auth.google');
Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])->name('auth.google.callback');

// Discord OAuth
Route::get('/auth/discord', [DiscordAuthController::class, 'redirect'])->middleware('auth')->name('auth.discord');
Route::get('/auth/discord/callback', [DiscordAuthController::class, 'callback'])->middleware('auth')->name('auth.discord.callback');

// Unsubscribe (public)
Route::get('/unsubscribe/{token}', [UnsubscribeController::class, 'show'])->name('unsubscribe');
Route::post('/unsubscribe/{token}', [UnsubscribeController::class, 'confirm'])->name('unsubscribe.confirm');

// Workspace switch
Route::post('/workspace/switch', [WorkspaceController::class, 'switch'])->middleware('auth')->name('workspace.switch');

// Protected app routes
Route::middleware(['auth', 'workspace'])->group(function () {
    Route::get('/dashboard', Dashboard::class)->name('dashboard');
    Route::get('/digests', DigestList::class)->name('digests');
    Route::get('/digests/{digest}', DigestPreview::class)->name('digests.preview');
    Route::get('/scheduled', ScheduledDigests::class)->name('scheduled');
    Route::get('/workspaces', WorkspaceList::class)->name('workspaces');
    Route::get('/sources', SourceList::class)->name('sources');
    Route::get('/subscribers', SubscriberList::class)->name('subscribers');
    Route::get('/settings', Settings::class)->name('settings');
});

// Profile
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
