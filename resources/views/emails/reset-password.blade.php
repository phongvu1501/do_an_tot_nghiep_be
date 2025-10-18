<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Your Password</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f6f8;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 40px auto;
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        h2 {
            color: #0d6efd;
        }
        p {
            line-height: 1.6;
        }
        .btn {
            display: inline-block;
            padding: 12px 20px;
            margin: 20px 0;
            font-size: 16px;
            color: #fff;
            background-color: #0d6efd;
            text-decoration: none;
            border-radius: 6px;
        }
        .footer {
            font-size: 12px;
            color: #777;
            margin-top: 30px;
            text-align: center;
        }
        @media (max-width: 600px) {
            .container {
                margin: 20px;
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Password Reset Request</h2>
        <p>Hi,</p>
        <p>We received a request to reset your password. Click the button below to set a new password:</p>
        <a href="{{ env('FRONTEND_URL') }}/reset-password?token={{ $token }}&email={{ $email }}" class="btn">Reset Password</a>
        <p>If the button doesn't work, copy and paste this URL into your browser:</p>
        <p><a href="{{ env('FRONTEND_URL') }}/reset-password?token={{ $token }}&email={{ $email }}">{{ env('FRONTEND_URL') }}/reset-password?token={{ $token }}&email={{ $email }}</a></p>
        <p>If you did not request a password reset, please ignore this email.</p>
        <div class="footer">
            &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
        </div>
    </div>
</body>
</html>
