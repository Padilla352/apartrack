<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset Request</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f1f5f9; font-family: 'Inter', Helvetica, Arial, sans-serif;">
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <td style="padding: 40px 0; text-align: center;">
                <h1 style="color: #1e293b; margin: 0; font-size: 28px; font-weight: 800; letter-spacing: -1px;">APART<span style="color: #3b82f6;">Track</span></h1>
            </td>
        </tr>
        <tr>
            <td align="center">
                <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 500px; background-color: #ffffff; border-radius: 16px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); overflow: hidden; border: 1px solid #e2e8f0;">
                    <tr>
                        <td style="height: 6px; background: linear-gradient(to right, #1e293b, #3b82f6);"></td>
                    </tr>
                    <tr>
                        <td style="padding: 40px 30px;">
                            <h2 style="color: #0f172a; margin-top: 0; font-size: 22px; font-weight: 700;">Password Reset Request</h2>
                            <p style="color: #475569; font-size: 15px; line-height: 24px;">Hello Admin,</p>
                            <p style="color: #475569; font-size: 15px; line-height: 24px;">
                                Someone requested to reset your password for the <strong>AparTrack Admin Portal</strong>. If this was you, click the secure button below:
                            </p>
                            
                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="margin: 35px 0;">
                                <tr>
                                    <td align="center">
                                        <a href="{{ $url }}" style="background-color: #1e293b; color: #ffffff; padding: 14px 32px; text-decoration: none; border-radius: 12px; font-weight: 700; font-size: 15px; display: inline-block;">
                                            Reset My Password
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <p style="color: #64748b; font-size: 13px; line-height: 20px; text-align: center; background: #f8fafc; padding: 15px; border-radius: 8px;">
                                <strong style="color: #ef4444;">Note:</strong> Valid for 60 minutes.
                            </p>

                            <hr style="border: 0; border-top: 1px solid #e2e8f0; margin: 30px 0;">

                            <p style="color: #94a3b8; font-size: 12px; line-height: 18px;">
                                If the button doesn't work, paste this link:<br>
                                <a href="{{ $url }}" style="color: #3b82f6;">{{ $url }}</a>
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>