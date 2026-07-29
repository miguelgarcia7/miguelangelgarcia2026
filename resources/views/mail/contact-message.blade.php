<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>New message from {{ $senderName }}</title>
</head>
{{-- Tables and inline styles: email clients strip <style> blocks and have no
     support for flexbox or grid. --}}
<body style="margin:0; padding:0; background-color:#0c0e11; -webkit-font-smoothing:antialiased;">
    <div style="display:none; max-height:0; overflow:hidden; opacity:0;">
        {{ $senderName }} sent a message from the contact form — {{ \Illuminate\Support\Str::limit($body, 90) }}
    </div>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#0c0e11;">
        <tr>
            <td align="center" style="padding:32px 16px;">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="max-width:600px; background-color:#13161a; border:1px solid rgba(255,255,255,0.07); border-radius:20px; overflow:hidden;">

                    <tr>
                        <td style="padding:28px 32px 0 32px;">
                            <p style="margin:0 0 6px 0; font-family:Arial,Helvetica,sans-serif; font-size:12px; font-weight:bold; letter-spacing:1.6px; text-transform:uppercase; color:#2ee6a6;">
                                miguelgarcia.site
                            </p>
                            <h1 style="margin:0; font-family:Arial,Helvetica,sans-serif; font-size:24px; line-height:1.25; font-weight:bold; color:#f3f5f6;">
                                New message from the contact form
                            </h1>
                        </td>
                    </tr>

                    @if ($assessment && ($assessment->isSuspicious() || ! $assessment->verified))
                        <tr>
                            <td style="padding:20px 32px 0 32px;">
                                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0"
                                       style="background-color:{{ $assessment->isSuspicious() ? 'rgba(255,122,122,0.10)' : 'rgba(255,255,255,0.05)' }}; border:1px solid {{ $assessment->isSuspicious() ? 'rgba(255,122,122,0.35)' : 'rgba(255,255,255,0.12)' }}; border-radius:10px;">
                                    <tr>
                                        <td style="padding:12px 16px; font-family:Arial,Helvetica,sans-serif; font-size:13px; line-height:1.5; color:{{ $assessment->isSuspicious() ? '#ff7a7a' : '#9aa0a9' }};">
                                            <strong>Spam check:</strong> {{ $assessment->summary() }}
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    @endif

                    <tr>
                        <td style="padding:24px 32px 0 32px;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td style="padding-bottom:14px; font-family:Arial,Helvetica,sans-serif; font-size:12px; font-weight:bold; letter-spacing:1px; text-transform:uppercase; color:#7f858e; width:80px; vertical-align:top;">
                                        Name
                                    </td>
                                    <td style="padding-bottom:14px; font-family:Arial,Helvetica,sans-serif; font-size:15px; color:#f3f5f6; vertical-align:top;">
                                        {{ $senderName }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding-bottom:14px; font-family:Arial,Helvetica,sans-serif; font-size:12px; font-weight:bold; letter-spacing:1px; text-transform:uppercase; color:#7f858e; vertical-align:top;">
                                        Email
                                    </td>
                                    <td style="padding-bottom:14px; font-family:Arial,Helvetica,sans-serif; font-size:15px; vertical-align:top;">
                                        <a href="mailto:{{ $senderEmail }}" style="color:#2ee6a6; text-decoration:none;">{{ $senderEmail }}</a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:10px 32px 0 32px;">
                            <p style="margin:0 0 10px 0; font-family:Arial,Helvetica,sans-serif; font-size:12px; font-weight:bold; letter-spacing:1px; text-transform:uppercase; color:#7f858e;">
                                Message
                            </p>
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#101317; border:1px solid rgba(255,255,255,0.07); border-radius:12px;">
                                <tr>
                                    <td style="padding:18px 20px; font-family:Arial,Helvetica,sans-serif; font-size:15px; line-height:1.7; color:#b3b9c1;">
                                        {!! nl2br(e($body)) !!}
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:24px 32px 32px 32px;">
                            <table role="presentation" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td style="background-color:#2ee6a6; border-radius:10px;">
                                        <a href="mailto:{{ $senderEmail }}?subject={{ rawurlencode('Re: your message via miguelgarcia.site') }}"
                                           style="display:inline-block; padding:13px 24px; font-family:Arial,Helvetica,sans-serif; font-size:15px; font-weight:bold; color:#08221a; text-decoration:none;">
                                            Reply to {{ \Illuminate\Support\Str::before($senderName, ' ') }}
                                        </a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                </table>

                <p style="margin:18px 0 0 0; font-family:Arial,Helvetica,sans-serif; font-size:12px; color:#7f858e;">
                    Sent from the contact form at miguelgarcia.site · {{ now()->format('M j, Y \a\t g:i A') }}
                </p>
            </td>
        </tr>
    </table>
</body>
</html>
