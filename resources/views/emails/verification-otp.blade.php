<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Prokash Verification Code</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f8fafc;
        }
        .container {
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo {
            font-size: 32px;
            font-weight: bold;
            color: #2563eb;
            margin-bottom: 10px;
        }
        .otp-box {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 12px;
            text-align: center;
            margin: 30px 0;
        }
        .otp-code {
            font-size: 48px;
            font-weight: bold;
            letter-spacing: 8px;
            margin: 20px 0;
            font-family: 'Courier New', monospace;
        }
        .warning {
            background: #fef3c7;
            border: 1px solid #f59e0b;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            color: #6b7280;
            font-size: 14px;
        }
        .btn {
            display: inline-block;
            background: #2563eb;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 6px;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">🚨 PROKASH</div>
            <h1>Email Verification</h1>
            <p>Hello {{ $userName }},</p>
            <p>Welcome to Prokash Community Alert System!</p>
        </div>

        <div class="otp-box">
            <h2>Your Verification Code</h2>
            <div class="otp-code">{{ $otp }}</div>
            <p>This code will expire in <strong>10 minutes</strong></p>
        </div>

        <div style="text-align: center; margin: 30px 0;">
            <p>Enter this code in the Prokash app to verify your email address and start receiving community alerts.</p>
        </div>

        <div class="warning">
            <strong>⚠️ Security Notice:</strong>
            <ul style="margin: 10px 0; padding-left: 20px;">
                <li>Never share this code with anyone</li>
                <li>Prokash will never ask for this code via phone or email</li>
                <li>This code expires in 10 minutes</li>
                <li>If you didn't request this, please ignore this email</li>
            </ul>
        </div>

        <div style="text-align: center; margin: 30px 0;">
            <h3>What is Prokash?</h3>
            <p>Prokash is a community-powered alert system that helps you stay informed about:</p>
            <ul style="text-align: left; display: inline-block;">
                <li>🚦 Traffic & Transportation updates</li>
                <li>🛡️ Crime & Safety alerts</li>
                <li>💰 Price & Market updates</li>
                <li>🔍 Lost & Found items</li>
                <li>🤝 Local Services & Help</li>
                <li>🚨 Emergency Alerts</li>
            </ul>
        </div>

        <div class="footer">
            <p>This email was sent by Prokash Alert System</p>
            <p>If you have any questions, please contact our support team.</p>
            <p style="margin-top: 20px;">
                <strong>Stay Safe, Stay Informed!</strong><br>
                The Prokash Team
            </p>
        </div>
    </div>
</body>
</html>
