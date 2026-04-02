<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $digest->title }}</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #f4f4f5; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 12px; overflow: hidden; }
        .header { background: #0f0f18; padding: 24px 32px; }
        .logo { color: #ff6b2b; font-size: 20px; font-weight: 700; text-decoration: none; }
        .body { padding: 32px; color: #111; line-height: 1.6; }
        .body h3 { margin-top: 1.5em; margin-bottom: 0.5em; font-size: 16px; }
        .body h3 a { color: #ff6b2b; text-decoration: none; }
        .body p { margin-top: 0; color: #444; font-size: 14px; }
        .footer { background: #f9f9f9; padding: 20px 32px; border-top: 1px solid #e5e5e5; }
        .footer p { font-size: 12px; color: #888; margin: 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <span class="logo">briefd</span>
        </div>
        <div class="body">
            <p style="color: #666; font-size: 14px; margin-top: 0;">Hi {{ $subscriber->name }},</p>
            <p style="color: #666; font-size: 14px;">Here's your latest digest:</p>
            {!! $digest->content !!}
        </div>
        <div class="footer">
            <p>You're receiving this because you subscribed to {{ $digest->workspace->name ?? 'Briefd' }}.</p>
            <p style="margin-top: 4px;">© {{ date('Y') }} Briefd. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
