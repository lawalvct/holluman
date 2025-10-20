<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }}</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            font-size: 16px;
            line-height: 1.6;
            color: #333333;
            background-color: #f4f4f7;
        }
        .email-wrapper {
            width: 100%;
            background-color: #f4f4f7;
            padding: 40px 0;
        }
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        .email-header {
            background: linear-gradient(135deg, #2563EB 0%, #1D4ED8 100%);
            padding: 40px 30px;
            text-align: center;
        }
        .email-header img {
            max-width: 150px;
            height: auto;
            margin-bottom: 15px;
        }
        .email-header h1 {
            color: #ffffff;
            font-size: 28px;
            font-weight: 700;
            margin: 0;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
        }
        .email-body {
            padding: 40px 30px;
        }
        .email-body h2 {
            color: #2563EB;
            font-size: 24px;
            font-weight: 600;
            margin: 0 0 20px 0;
        }
        .email-body p {
            color: #555555;
            margin: 0 0 15px 0;
            line-height: 1.8;
        }
        .button-container {
            text-align: center;
            margin: 35px 0;
        }
        .reset-button {
            display: inline-block;
            padding: 16px 40px;
            background: linear-gradient(135deg, #2563EB 0%, #1D4ED8 100%);
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            font-size: 16px;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
            transition: all 0.3s ease;
        }
        .reset-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(37, 99, 235, 0.4);
        }
        .info-box {
            background-color: #FEF3C7;
            border-left: 4px solid #F59E0B;
            padding: 15px 20px;
            margin: 25px 0;
            border-radius: 4px;
        }
        .info-box p {
            color: #92400E;
            margin: 0;
            font-size: 14px;
        }
        .alert-box {
            background-color: #FEE2E2;
            border-left: 4px solid #EF4444;
            padding: 15px 20px;
            margin: 25px 0;
            border-radius: 4px;
        }
        .alert-box p {
            color: #991B1B;
            margin: 0;
            font-size: 14px;
        }
        .email-footer {
            background-color: #f9fafb;
            padding: 30px;
            text-align: center;
            border-top: 1px solid #e5e7eb;
        }
        .email-footer p {
            color: #6b7280;
            font-size: 13px;
            margin: 5px 0;
        }
        .email-footer a {
            color: #2563EB;
            text-decoration: none;
        }
        .email-footer a:hover {
            text-decoration: underline;
        }
        .link-text {
            word-break: break-all;
            font-size: 12px;
            color: #6b7280;
            margin-top: 20px;
        }
        @media only screen and (max-width: 600px) {
            .email-header, .email-body, .email-footer {
                padding: 25px 20px !important;
            }
            .email-header h1 {
                font-size: 24px;
            }
            .email-body h2 {
                font-size: 20px;
            }
            .reset-button {
                padding: 14px 30px;
                font-size: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="email-container">
            <!-- Header -->
            <div class="email-header">
                <img src="{{ asset('images/logo.png') }}" alt="Veesta Logo">
                <h1>Veesta</h1>
            </div>

            <!-- Body -->
            <div class="email-body">
                <h2>Password Reset Request</h2>

                <p>Hello{{ isset($notifiable) ? ' ' . $notifiable->name : '' }},</p>

                <p>We received a request to reset the password for your Veesta account. If you made this request, click the button below to create a new password:</p>

                <div class="button-container">
                    <a href="{{ $actionUrl }}" class="reset-button">Reset My Password</a>
                </div>

                <div class="info-box">
                    <p><strong>⏰ Time Sensitive:</strong> This password reset link will expire in {{ $displayableExpiry ?? '60 minutes' }}. Please use it soon!</p>
                </div>

                <div class="alert-box">
                    <p><strong>🔒 Security Notice:</strong> If you did not request a password reset, please ignore this email. Your password will remain unchanged and your account is secure.</p>
                </div>

                <p>For your security, never share this link with anyone. If you're having trouble with the button above, copy and paste the following URL into your browser:</p>

                <p class="link-text">{{ $actionUrl }}</p>
            </div>

            <!-- Footer -->
            <div class="email-footer">
                <p><strong>Veesta - Stay Connected</strong></p>
                <p>{{ $companySettings['address'] ?? 'Your trusted internet service provider' }}</p>
                @if(isset($companySettings['support_email']))
                    <p>Need help? Contact us at <a href="mailto:{{ $companySettings['support_email'] }}">{{ $companySettings['support_email'] }}</a></p>
                @endif
                @if(isset($companySettings['support_phone']))
                    <p>Call us: {{ $companySettings['support_phone'] }}</p>
                @endif
                <p style="margin-top: 20px;">© {{ date('Y') }} Veesta. All rights reserved.</p>
            </div>
        </div>
    </div>
</body>
</html>
