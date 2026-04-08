<?php

namespace App\Http\Controllers;

use App\Models\Subscriber;

class UnsubscribeController extends Controller
{
    public function show(string $token)
    {
        $subscriber = Subscriber::where('unsubscribe_token', $token)->firstOrFail();

        return view('unsubscribe', compact('subscriber'));
    }

    public function confirm(string $token)
    {
        $subscriber = Subscriber::where('unsubscribe_token', $token)->firstOrFail();

        if ($subscriber->unsubscribed_at === null) {
            $subscriber->update(['unsubscribed_at' => now()]);
        }

        return redirect()->route('unsubscribe', $token)->with('unsubscribed', true);
    }
}
