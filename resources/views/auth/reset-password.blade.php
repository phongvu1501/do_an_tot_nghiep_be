<!DOCTYPE html>
<html>
<head>
    <title>Đổi mật khẩu | Đặt bàn nhà hàng</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="d-flex align-items-center justify-content-center" style="height:100vh; background:#222; color:white;">

<div class="card p-4 col-md-4" style="background:rgba(255,255,255,0.1); backdrop-filter:blur(10px); border:none;">
    <h3 class="text-center mb-3 fw-bold text-warning">Đổi mật khẩu</h3>

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

        <button class="btn btn-warning w-100">Cập nhật mật khẩu</button>
    </form>
</div>

</body>
</html>
