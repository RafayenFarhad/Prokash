<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Emergency Alert - {{ $broadcast->title }}</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f8f9fa;
        }
        .container {
            background-color: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .header {
            background-color: {{ $severityColor }};
            color: white;
            padding: 20px;
            text-align: center;
        }
        .alert-icon {
            font-size: 48px;
            margin-bottom: 10px;
        }
        .severity-badge {
            background-color: rgba(255, 255, 255, 0.2);
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .content {
            padding: 30px;
        }
        .message {
            background-color: #f8f9fa;
            border-left: 4px solid {{ $severityColor }};
            padding: 20px;
            margin: 20px 0;
            border-radius: 0 4px 4px 0;
        }
        .details {
            background-color: #f1f3f4;
            padding: 15px;
            border-radius: 4px;
            margin: 20px 0;
        }
        .details-item {
            margin-bottom: 8px;
        }
        .details-label {
            font-weight: bold;
            color: #666;
        }
        .action-button {
            display: inline-block;
            background-color: {{ $severityColor }};
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 4px;
            font-weight: bold;
            margin: 20px 0;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #666;
            border-top: 1px solid #e9ecef;
        }
        .warning-box {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
            padding: 15px;
            border-radius: 4px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="alert-icon">🚨</div>
            <h1 style="margin: 0; font-size: 24px;">EMERGENCY ALERT</h1>
            <div class="severity-badge">{{ strtoupper($broadcast->severity) }} PRIORITY</div>
        </div>

        <!-- Content -->
        <div class="content">
            <h2 style="color: {{ $severityColor }}; margin-top: 0;">{{ $broadcast->title }}</h2>
            
            <div class="message">
                <p style="margin: 0; font-size: 16px; line-height: 1.6;">
                    {{ $broadcast->message }}
                </p>
            </div>

            @if($broadcast->target_area)
            <div class="warning-box">
                <strong>📍 Target Area:</strong> {{ $broadcast->target_area }}
            </div>
            @endif

            <div class="details">
                <div class="details-item">
                    <span class="details-label">Alert Time:</span> 
                    {{ $broadcast->created_at->format('F j, Y \a\t g:i A T') }}
                </div>
                <div class="details-item">
                    <span class="details-label">Severity Level:</span> 
                    {{ ucfirst($broadcast->severity) }}
                </div>
                @if($broadcast->target_area)
                <div class="details-item">
                    <span class="details-label">Affected Area:</span> 
                    {{ $broadcast->target_area }}
                </div>
                @endif
            </div>

            <div style="text-align: center;">
                <a href="{{ $appUrl }}" class="action-button">
                    View Full Alert Details
                </a>
            </div>

            <div style="margin-top: 30px; padding: 15px; background-color: #e3f2fd; border-radius: 4px;">
                <h3 style="margin-top: 0; color: #1565c0;">📱 Stay Connected</h3>
                <p style="margin-bottom: 0; font-size: 14px;">
                    For real-time updates and additional information, please check the Prokash Alert System app 
                    or visit our website. Follow official channels for the latest developments.
                </p>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p style="margin: 0;">
                <strong>Prokash Alert System</strong><br>
                This is an automated emergency notification. Please do not reply to this email.
            </p>
            <p style="margin: 10px 0 0 0;">
                Sent to: {{ $user->name }} ({{ $user->email }})<br>
                Alert ID: #{{ $broadcast->id }} | {{ $broadcast->created_at->format('Y-m-d H:i:s T') }}
            </p>
        </div>
    </div>

    <!-- Important Notice -->
    <div style="margin-top: 20px; padding: 15px; background-color: white; border-radius: 4px; border: 2px solid {{ $severityColor }};">
        <h4 style="margin-top: 0; color: {{ $severityColor }};">⚠️ Important Safety Notice</h4>
        <ul style="margin-bottom: 0; font-size: 14px;">
            <li>Follow official emergency procedures and local authorities' instructions</li>
            <li>Stay informed through official news sources and emergency services</li>
            <li>Share this information responsibly with family and friends</li>
            <li>Do not spread unverified information or rumors</li>
        </ul>
    </div>
</body>
</html>
