<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unsubscribe — Briefd</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #0a0a0f; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; }
        .card { background: #0f0f18; border: 1px solid #1e1e2e; border-radius: 16px; padding: 40px; max-width: 440px; width: 100%; text-align: center; }
        .logo { color: #ff6b2b; font-size: 22px; font-weight: 700; margin-bottom: 24px; display: block; }
        h1 { color: #ffffff; font-size: 20px; font-weight: 600; margin-bottom: 8px; }
        .sub { color: #6b6b8a; font-size: 14px; line-height: 1.6; margin-bottom: 24px; }
        .email { color: #ffffff; font-weight: 600; }
        .workspace { color: #ff6b2b; }
        .btn { display: inline-block; padding: 10px 24px; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; border: none; }
        .btn-danger { background: #e24b4a; color: white; }
        .btn-danger:hover { background: #c73b3a; }
        .success-box { background: #071a10; border: 1px solid #22c55e; border-radius: 10px; padding: 16px; margin-top: 16px; }
        .success-box p { color: #22c55e; font-size: 14px; }
        .already { color: #6b6b8a; font-size: 13px; margin-top: 16px; }
    </style>
</head>
<body>
    <div class="card">
        <span class="logo">briefd</span>

        @if(session('unsubscribed') || $subscriber->unsubscribed_at)
            <h1>You're unsubscribed</h1>
            <p class="sub">
                <span class="email">{{ $subscriber->email }}</span> has been removed from
                <span class="workspace">{{ $subscriber->workspace->name }}</span>.
            </p>
            <div class="success-box">
                <p>You won't receive any more digests from this workspace.</p>
            </div>
        @else
            <h1>Unsubscribe</h1>
            <p class="sub">
                Are you sure you want to unsubscribe <span class="email">{{ $subscriber->email }}</span> from
                <span class="workspace">{{ $subscriber->workspace->name }}</span>?
            </p>
            <form method="POST" action="{{ route('unsubscribe.confirm', $subscriber->unsubscribe_token) }}">
                @csrf
                <button type="submit" class="btn btn-danger">Yes, unsubscribe me</button>
            </form>
            <p class="already">Changed your mind? Just close this page.</p>
        @endif
    </div>
</body>
</html>
