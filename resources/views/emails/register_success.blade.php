<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Đăng ký thành công</title>
</head>
<body>
    <h2>Xin chào {{ $user->name }},</h2>
    <p>Bạn đã đăng ký tài khoản thành công tại hệ thống nhà hàng của chúng tôi.</p>
    <p>Email: {{ $user->email }}</p>
    <p>Số điện thoại: {{ $user->phone }}</p>
    <br>
    <p>Cảm ơn bạn đã quan tâm đến nhà hàng chúng tôi</p>
</body>
</html>
