<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Thông báo hoàn tiền</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #28a745;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            background-color: #f9f9f9;
            padding: 20px;
            border: 1px solid #ddd;
        }
        .info-box {
            background-color: white;
            padding: 15px;
            margin: 15px 0;
            border-left: 4px solid #28a745;
            border-radius: 4px;
        }
        .amount {
            font-size: 24px;
            font-weight: bold;
            color: #28a745;
            text-align: center;
            padding: 20px;
            background-color: #e8f5e9;
            border-radius: 5px;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            padding: 20px;
            color: #666;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Thông báo hoàn tiền đặt bàn</h2>
        </div>
        
        <div class="content">
            <h2>Xin chào {{ $reservation->user->name }},</h2>
            <p>Chúng tôi xin thông báo rằng khoản tiền cọc của bạn đã được hoàn lại thành công.</p>
            
            <div class="info-box">
                <h3>Thông tin đơn đặt bàn:</h3>
                <ul>
                    <li><strong>Mã đơn:</strong> {{ $reservation->reservation_code ?? '#' . $reservation->id }}</li>
                    <li><strong>Ngày đặt:</strong> {{ \Carbon\Carbon::parse($reservation->reservation_date)->format('d/m/Y') }}</li>
                    <li><strong>Ca:</strong> 
                        @if($reservation->shift == 'morning')
                            Ca sáng (8:00 - 13:00)
                        @elseif($reservation->shift == 'afternoon')
                            Ca trưa (13:00 - 18:00)
                        @elseif($reservation->shift == 'evening')
                            Ca tối (18:00 - 23:00)
                        @else
                            {{ $reservation->shift }}
                        @endif
                    </li>
                    <li><strong>Số người:</strong> {{ $reservation->num_people }} người</li>
                </ul>
            </div>

            <div class="amount">
                Số tiền được hoàn: {{ number_format($reservation->deposit ?? 0, 0, ',', '.') }} VND
            </div>

            @php
                $reasonParts = explode("\n\nSố tài khoản hoàn tiền: ", $reservation->cancellation_reason ?? '');
                $accountNumber = isset($reasonParts[1]) ? trim($reasonParts[1]) : null;
            @endphp

            @if($accountNumber)
                <div class="info-box">
                    <p><strong>Số tài khoản nhận hoàn tiền:</strong> {{ $accountNumber }}</p>
                    <p><strong>Ngày hoàn tiền:</strong> {{ \Carbon\Carbon::parse($reservation->refunded_at)->format('d/m/Y H:i') }}</p>
                </div>
            @endif

            
            <p>Trân trọng</p>
        </div>
        
        
    </div>
</body>
</html>

