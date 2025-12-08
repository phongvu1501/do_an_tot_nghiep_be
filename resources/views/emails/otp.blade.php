<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Mã OTP</title>
</head>
<body>
    <p>Chào {{ $user->name }},</p>
    <p>Mã OTP của bạn là: <strong>{{ $otp }}</strong></p>
    <p>Mã có hiệu lực trong 5 phút.</p>
</body>
</html>
