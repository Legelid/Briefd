<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $digest->title }}</title>
</head>
<body style="margin:0;padding:0;background-color:#0a0a0f;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#0a0a0f;padding:24px 16px;">
        <tr>
            <td align="center">
                <table width="100%" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;">

                    {{-- Header --}}
                    <tr>
                        <td style="background-color:#0f0f18;border:1px solid #1e1e2e;border-radius:12px 12px 0 0;padding:24px 32px;">
                            <table width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td>
                                        <span style="color:#ff6b2b;font-size:20px;font-weight:700;text-decoration:none;">briefd</span>
                                    </td>
                                    <td align="right">
                                        <span style="color:#6b6b8a;font-size:12px;">{{ $digest->workspace->name ?? '' }}</span>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- Greeting --}}
                    <tr>
                        <td style="background-color:#0f0f18;border-left:1px solid #1e1e2e;border-right:1px solid #1e1e2e;padding:24px 32px 0 32px;">
                            <p style="margin:0 0 4px 0;color:#6b6b8a;font-size:14px;">Hi {{ $subscriber->name }},</p>
                            <p style="margin:0 0 24px 0;color:#6b6b8a;font-size:14px;">Here's your latest digest from <strong style="color:#ffffff;">{{ $digest->workspace->name ?? 'Briefd' }}</strong>:</p>
                        </td>
                    </tr>

                    {{-- Content --}}
                    <tr>
                        <td style="background-color:#0f0f18;border-left:1px solid #1e1e2e;border-right:1px solid #1e1e2e;padding:0 32px 24px 32px;color:#cccccc;font-size:14px;line-height:1.7;">
                            <style>
                                .digest-item { margin-bottom: 8px; }
                                .digest-item p { margin: 0 0 8px 0; font-size: 14px; color: #aaaaaa; }
                                .digest-item strong a { color: #ffffff; text-decoration: none; font-size: 16px; font-weight: 700; }
                                .digest-item .read-more { color: #ff6b2b !important; font-weight: 600; text-decoration: none; font-size: 13px; }
                                .digest-item hr { border: none; border-top: 1px solid #1e1e2e; margin: 20px 0; }
                                .source-group { margin-bottom: 8px; }
                                .source-header { color: #ffffff; font-size: 15px; font-weight: 700; margin: 0 0 6px 0; }
                                .source-badge { display: inline-block; font-size: 10px; font-weight: 700; padding: 2px 8px; border-radius: 999px; margin-bottom: 12px; text-transform: uppercase; letter-spacing: 0.05em; }
                                .source-badge--rss { background-color: #1f1008; color: #ff6b2b; }
                                .source-badge--discord { background-color: #1a1033; color: #7c6ef7; }
                                hr { border: none; border-top: 1px solid #1e1e2e; margin: 20px 0; }
                            </style>
                            {!! $digest->content !!}
                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td style="background-color:#080810;border:1px solid #1e1e2e;border-top:none;border-radius:0 0 12px 12px;padding:20px 32px;">
                            <p style="margin:0 0 6px 0;font-size:12px;color:#6b6b8a;">
                                You're receiving this because you subscribed to <strong style="color:#ffffff;">{{ $digest->workspace->name ?? 'Briefd' }}</strong>.
                            </p>
                            <p style="margin:0 0 6px 0;font-size:12px;color:#6b6b8a;">
                                <a href="{{ $unsubscribe_url }}" style="color:#6b6b8a;text-decoration:underline;">Unsubscribe</a>
                            </p>
                            <p style="margin:0;font-size:12px;color:#3a3a5c;">© {{ date('Y') }} Briefd. All rights reserved.</p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
