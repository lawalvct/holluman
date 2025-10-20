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
            background: linear-gradient(135deg, #8B5CF6 0%, #7C3AED 100%);
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
            color: #8B5CF6;
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
        .dashboard-button {
            display: inline-block;
            padding: 16px 40px;
            background: linear-gradient(135deg, #8B5CF6 0%, #7C3AED 100%);
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            font-size: 16px;
            box-shadow: 0 4px 12px rgba(139, 92, 246, 0.3);
            transition: all 0.3s ease;
        }
        .dashboard-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(139, 92, 246, 0.4);
        }
        .feature-box {
            background-color: #F5F3FF;
            border-left: 4px solid #8B5CF6;
            padding: 15px 20px;
            margin: 25px 0;
            border-radius: 4px;
        }
        .feature-box h3 {
            color: #6D28D9;
            font-size: 16px;
            font-weight: 600;
            margin: 0 0 10px 0;
        }
        .feature-box ul {
            margin: 0;
            padding-left: 20px;
            color: #6D28D9;
        }
        .feature-box li {
            margin: 5px 0;
            font-size: 14px;
        }
        .welcome-image {
            text-align: center;
            margin: 30px 0;
        }
        .welcome-image span {
            font-size: 80px;
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
            color: #8B5CF6;
            text-decoration: none;
        }
        .email-footer a:hover {
            text-decoration: underline;
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
            .dashboard-button {
                padding: 14px 30px;
                font-size: 15px;
            }
            .welcome-image span {
                font-size: 60px;
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
                <div class="welcome-image">
                    <span>🎉</span>
                </div>

                <h2>Welcome to Veesta, {{ $notifiable->name }}!</h2>

                <p>Congratulations! Your email has been verified and your account is now fully activated.</p>

                <p>We're thrilled to have you as part of the Veesta family. You now have access to reliable, high-speed internet services at your fingertips.</p>

                <div class="button-container">
                    <a href="{{ $actionUrl }}" class="dashboard-button">Go to My Dashboard</a>
                </div>

                <div class="feature-box">
                    <h3>🚀 What You Can Do Now:</h3>
                    <ul>
                        <li><strong>Browse Plans:</strong> Explore our affordable data subscription plans</li>
                        <li><strong>Manage SIMs:</strong> Add and manage your camera-enabled phone numbers</li>
                        <li><strong>Top Up Wallet:</strong> Fund your wallet for quick subscriptions</li>
                        <li><strong>Track Usage:</strong> Monitor your subscriptions and transaction history</li>
                        <li><strong>24/7 Support:</strong> Get help whenever you need it</li>
                    </ul>
                </div>

                <p>Ready to get started? Click the button above to access your dashboard and explore all the features Veesta has to offer.</p>

                <p style="margin-top: 25px;">If you have any questions or need assistance, our support team is always here to help!</p>
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
