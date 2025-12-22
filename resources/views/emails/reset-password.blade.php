<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
        }

        .container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            color: #333;
            margin-top: 0;
        }

        .button {
            display: inline-block;
            padding: 14px 28px;
            background-color: #4CAF50;
            color: white !important;
            text-decoration: none;
            border-radius: 6px;
            margin: 20px 0;
            font-weight: bold;
        }

        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>🔐 Reset Your Password</h2>

        <p>Hi {{ $mailData['name'] ?? 'there' }},</p>

        <p>You are receiving this email because we received a password reset request for your account.</p>

        <p style="text-align: center;">
            <a href="{{ $mailData['resetUrl'] }}" class="button">Reset Password</a>
        </p>

        <p><strong>This link will expire in 1 hour.</strong></p>

        <p>If you did not request a password reset, no further action is required.</p>

        <div class="footer">
            <p>If you're having trouble clicking the button, copy and paste the URL below:</p>
            <p>{{ $mailData['resetUrl'] }}</p>
            <p style="margin-top: 20px;">— The EatZy Team</p>
        </div>
    </div>
</body>

</html>