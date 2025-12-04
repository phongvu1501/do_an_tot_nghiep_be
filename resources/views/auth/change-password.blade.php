<!DOCTYPE html>
<html>
<head>
    <title>Đổi mật khẩu | Đặt bàn nhà hàng</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)),
                        url('https://images.unsplash.com/photo-1504674900247-0877df9cc836?auto=format&fit=crop&w=1500&q=80') no-repeat center center/cover;
            color: #fff;
            font-family: 'Segoe UI', sans-serif;
            height: 100vh;
        }
        .card {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            backdrop-filter: blur(14px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.4);
            color: #fff;
        }
        label {
            color: #f5d88f;
            font-weight: 500;
        }
        input.form-control {
            background: rgba(255,255,255,0.85);
            color: #000;
            border: 1px solid #ddd;
        }
        input.form-control:focus {
            box-shadow: 0 0 5px #c59d5f;
            border-color: #c59d5f;
        }
        .btn-warning {
            background-color: #c59d5f;
            border: none;
        }
        .btn-warning:hover {
            background-color: #b58c4e;
        }
        a {
            color: #f5d88f;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
        small.text-warning {
            display: block;
            margin-top: 4px;
        }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center">

<div class="card p-4 col-md-4">
    <h3 class="text-center mb-3 fw-bold" style="color: #f5d88f;">Đổi mật khẩu</h3>

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('password.change') }}">
        @csrf

        <div class="mb-3">
            <label>Mật khẩu hiện tại</label>
            <input type="password" name="current_password" class="form-control" required>
            @error('current_password')
                <small class="text-warning">{{ $message }}</small>
            @enderror
        </div>

        <div class="mb-3">
            <label>Mật khẩu mới</label>
            <input type="password" name="new_password" class="form-control" required>
            @error('new_password')
                <small class="text-warning">{{ $message }}</small>
            @enderror
        </div>

        <div class="mb-3">
            <label>Xác nhận mật khẩu mới</label>
            <input type="password" name="new_password_confirmation" class="form-control" required>
        </div>

        <button class="btn btn-warning w-100 py-2 mt-2">Cập nhật mật khẩu</button>
        
        <a href="{{ route('login') }}" class="d-block text-center mt-3">Quay lại đăng nhập</a>
    </form>
</div>

</body>
</html>
