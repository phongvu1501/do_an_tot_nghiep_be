<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thanh toán thất bại</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .payment-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            padding: 40px;
            max-width: 500px;
            width: 100%;
            text-align: center;
        }
        .failed-icon {
            width: 100px;
            height: 100px;
            background: #dc3545;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
            animation: scaleIn 0.5s ease-out;
        }
        .failed-icon i {
            font-size: 50px;
            color: white;
        }
        @keyframes scaleIn {
            from {
                transform: scale(0);
            }
            to {
                transform: scale(1);
            }
        }
        h1 {
            color: #dc3545;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .btn-retry {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            border: none;
            padding: 12px 40px;
            border-radius: 25px;
            color: white;
            font-weight: 600;
            transition: transform 0.2s;
            margin-right: 10px;
        }
        .btn-retry:hover {
            transform: translateY(-2px);
            color: white;
        }
        .btn-home {
            background: #6c757d;
            border: none;
            padding: 12px 40px;
            border-radius: 25px;
            color: white;
            font-weight: 600;
            transition: transform 0.2s;
        }
        .btn-home:hover {
            transform: translateY(-2px);
            color: white;
            background: #5a6268;
        }
    </style>
</head>
<body>
    <div class="payment-card">
        <div class="failed-icon">
            <i class="fas fa-times"></i>
        </div>
        <h1>Thanh toán thất bại!</h1>
        <p class="text-muted mb-4">Rất tiếc, quá trình thanh toán của bạn không thành công. Vui lòng thử lại.</p>
        
        @if(isset($reservation_code))
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            Mã đơn hàng: <strong>{{ $reservation_code }}</strong>
        </div>
        @endif

        <div class="mt-4">
            @if(isset($payment_url))
            <a href="{{ $payment_url }}" class="btn btn-retry">
                <i class="fas fa-redo me-2"></i>Thử lại thanh toán
            </a>
            @endif
            <a href="{{ env('FRONTEND_URL', 'http://localhost:5173') }}" class="btn btn-home">
                <i class="fas fa-home me-2"></i>Về trang chủ
            </a>
        </div>

        <p class="text-muted mt-4 small">
            <i class="fas fa-exclamation-triangle me-1"></i>
            Nếu vấn đề vẫn tiếp tục, vui lòng liên hệ với chúng tôi để được hỗ trợ.
        </p>
    </div>
</body>
</html>

