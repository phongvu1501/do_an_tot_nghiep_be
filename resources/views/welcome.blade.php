<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>DATBAN - Đăng nhập Quản trị</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <style>
        /* Thiết lập body để chiếm toàn bộ chiều cao màn hình */
        html, body {
            height: 100%;
            background-color: #f8f9fa; /* Màu nền xám nhạt */
        }
        /* Sử dụng d-flex, justify-content-center, align-items-center để căn giữa thẻ */
        .login-container {
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            width: 100%;
            max-width: 400px; /* Giới hạn chiều rộng của form */
        }
    </style>
</head>
<body>

    <div class="login-container">
        <div class="card shadow-lg login-card">
            <div class="card-header bg-primary text-white text-center">
                <h3 class="mb-0">Đăng nhập Quản trị DATBAN</h3>
            </div>
            <div class="card-body p-4">
                <p class="text-center text-muted">Vui lòng nhập thông tin tài khoản của bạn</p>
                
                <form action="#" method="POST">
                    <div class="mb-3">
                        <label for="username" class="form-label">Tên đăng nhập hoặc Email</label>
                        <input type="text" class="form-control" id="username" placeholder="Nhập tên đăng nhập hoặc email" required>
                    </div>

                    <div class="mb-4">
                        <label for="password" class="form-label">Mật khẩu</label>
                        <input type="password" class="form-control" id="password" placeholder="Nhập mật khẩu" required>
                    </div>

                    <div class="mb-4 form-check">
                        <input type="checkbox" class="form-check-input" id="rememberMe">
                        <label class="form-check-label" for="rememberMe">Ghi nhớ tôi</label>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg">Đăng nhập</button>
                    </div>
                </form>
                </div>
            <div class="card-footer text-center">
                <small><a href="#" class="text-decoration-none">Quên mật khẩu?</a></small>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>
</html>