<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thanh toán thành công</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
        .success-icon {
            width: 100px;
            height: 100px;
            background: #28a745;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
            animation: scaleIn 0.5s ease-out;
        }
        .success-icon i {
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
            color: #28a745;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .reservation-info {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin: 30px 0;
        }
        .info-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #dee2e6;
        }
        .info-item:last-child {
            border-bottom: none;
        }
        .info-label {
            font-weight: 600;
            color: #6c757d;
        }
        .info-value {
            color: #212529;
            font-weight: 500;
        }
        .btn-home {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
        }
    </style>
</head>
<body>
    <div class="payment-card">
        <div class="success-icon">
            <i class="fas fa-check"></i>
        </div>
        <h1>Thanh toán thành công!</h1>
        <p class="text-muted mb-4">Cảm ơn bạn đã thanh toán. Đơn đặt bàn của bạn đang chờ xác nhận.</p>
        
        @if(isset($reservation))
        <div class="reservation-info">
            <div class="info-item">
                <span class="info-label">Mã đơn hàng:</span>
                <span class="info-value">{{ $reservation->reservation_code }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Ngày đặt:</span>
                <span class="info-value">{{ \Carbon\Carbon::parse($reservation->reservation_date)->format('d/m/Y') }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Số tiền cọc:</span>
                <span class="info-value">{{ number_format($reservation->deposit, 0, ',', '.') }} VND</span>
            </div>
            <div class="info-item">
                <span class="info-label">Trạng thái:</span>
                <span class="info-value">
                    <span class="badge bg-warning text-dark">Chờ xác nhận</span>
                </span>
            </div>
        </div>
        @endif

        <div class="mt-4">
            <a href="{{ env('FRONTEND_URL', 'http://localhost:5173') }}" class="btn btn-home">
                <i class="fas fa-home me-2"></i>Về trang chủ
            </a>
        </div>

        <p class="text-muted mt-4 small">
            <i class="fas fa-info-circle me-1"></i>
            Vui lòng chờ quản trị viên xác nhận đơn đặt bàn của bạn.
        </p>
    </div>
</body>
</html>

