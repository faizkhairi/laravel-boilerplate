<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Verify Your Email - {{ config('app.name') }}</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f6f9fc; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Ubuntu, sans-serif;">
    <!-- Main Container -->
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f6f9fc; padding: 40px 0;">
        <tr>
            <td align="center">
                <!-- Email Content -->
                <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05); margin: 0 auto;">
                    <!-- Header -->
                    <tr>
                        <td style="padding: 20px 48px; border-bottom: 1px solid #eaeaea;">
                            <h2 style="margin: 0; font-size: 24px; font-weight: 700; color: #0f172a;">🚀 {{ config('app.name') }}</h2>
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="padding: 40px 48px 0;">
                            <h1 style="color: #1a1a1a; font-size: 32px; font-weight: 700; margin: 0 0 24px 0;">
                                Verify Your Email Address
                            </h1>

                            <p style="color: #4a4a4a; font-size: 16px; line-height: 1.6; margin: 0 0 16px 0;">
                                Hi {{ $user->name }},
                            </p>

                            <p style="color: #4a4a4a; font-size: 16px; line-height: 1.6; margin: 0 0 16px 0;">
                                Thank you for registering with {{ config('app.name') }}! Please verify your email address to activate your account.
                            </p>

                            <p style="color: #4a4a4a; font-size: 16px; line-height: 1.6; margin: 0 0 24px 0;">
                                Click the button below to verify your email address and complete your registration.
                            </p>

                            <!-- Button -->
                            <table cellpadding="0" cellspacing="0">
                                <tr>
                                    <td style="border-radius: 6px; background-color: #0f172a;">
                                        <a href="{{ $verificationUrl }}"
                                           style="display: inline-block; padding: 12px 32px; background-color: #0f172a; color: #ffffff; font-size: 16px; font-weight: 600; text-decoration: none; border-radius: 6px;">
                                            Verify Email Address
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <p style="color: #8898aa; font-size: 14px; line-height: 1.6; margin: 24px 0 0; padding: 16px; background-color: #f6f9fc; border-radius: 6px;">
                                If the button doesn't work, copy and paste this URL into your browser:<br>
                                <a href="{{ $verificationUrl }}" style="color: #0f172a; word-break: break-all;">{{ $verificationUrl }}</a>
                            </p>
                        </td>
                    </tr>

                    <!-- Divider -->
                    <tr>
                        <td style="padding: 32px 48px;">
                            <hr style="border: none; border-top: 1px solid #eaeaea; margin: 0;">
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="padding: 0 48px 40px;">
                            <p style="color: #8898aa; font-size: 14px; line-height: 1.5; text-align: center; margin: 0 0 8px 0;">
                                If you didn't create an account with {{ config('app.name') }}, you can safely ignore this email.
                            </p>
                            <p style="color: #8898aa; font-size: 14px; line-height: 1.5; text-align: center; margin: 0;">
                                © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
