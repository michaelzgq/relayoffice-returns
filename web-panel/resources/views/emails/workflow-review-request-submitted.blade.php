<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>New workflow review request</title>
</head>
<body style="margin:0;padding:24px;background:#f4efe8;color:#1f2937;font-family:Arial,Helvetica,sans-serif;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:720px;margin:0 auto;background:#fffaf3;border:1px solid #e6d9ca;border-radius:16px;overflow:hidden;">
        <tr>
            <td style="padding:24px 28px;border-bottom:1px solid #efe3d6;">
                <p style="margin:0 0 10px;font-size:12px;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;color:#9a5c2f;">New workflow review request</p>
                <h1 style="margin:0;font-size:28px;line-height:1.2;color:#12222f;">{{ $requestRecord->company_name }}</h1>
                <p style="margin:10px 0 0;font-size:16px;line-height:1.6;color:#556270;">
                    {{ $requestRecord->full_name }}
                    @if($requestRecord->role_title)
                        · {{ $requestRecord->role_title }}
                    @endif
                    · <a href="mailto:{{ $requestRecord->work_email }}" style="color:#0f5b5a;text-decoration:none;">{{ $requestRecord->work_email }}</a>
                </p>
            </td>
        </tr>
        <tr>
            <td style="padding:24px 28px;">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border-collapse:collapse;">
                    <tr>
                        <td style="padding:0 0 16px;width:180px;font-size:12px;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;color:#8c7d6f;">Monthly return volume</td>
                        <td style="padding:0 0 16px;font-size:16px;line-height:1.6;color:#1f2937;">{{ $requestRecord->volume_note ?: 'Not provided' }}</td>
                    </tr>
                    <tr>
                        <td style="padding:0 0 16px;width:180px;font-size:12px;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;color:#8c7d6f;">Submitted from</td>
                        <td style="padding:0 0 16px;font-size:16px;line-height:1.6;color:#1f2937;">
                            {{ $requestRecord->submitted_from_host ?: 'Unknown host' }}
                            @if($requestRecord->submitted_from_url)
                                <br>
                                <a href="{{ $requestRecord->submitted_from_url }}" style="color:#0f5b5a;text-decoration:none;">{{ $requestRecord->submitted_from_url }}</a>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:0;vertical-align:top;width:180px;font-size:12px;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;color:#8c7d6f;">Current workflow note</td>
                        <td style="padding:0;font-size:16px;line-height:1.7;color:#1f2937;">{{ $requestRecord->workflow_note }}</td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td style="padding:18px 28px;background:#f8f2ea;border-top:1px solid #efe3d6;font-size:14px;line-height:1.6;color:#556270;">
                Reply directly to this email to respond to {{ $requestRecord->full_name }}.
            </td>
        </tr>
    </table>
</body>
</html>
