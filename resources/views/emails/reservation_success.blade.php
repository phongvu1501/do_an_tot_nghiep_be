<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Đặt bàn thành công</title>
</head>
<body>
    <h2>Xin chào {{ $reservation->user->name }},</h2>
    <p>Bạn đã đặt bàn thành công tại nhà hàng của chúng tôi.</p>
    
    <h3>Thông tin đặt bàn:</h3>
    <ul>
        <li><strong>Mã đơn:</strong> {{ $reservation->reservation_code }}</li>
        <li><strong>Ngày đặt:</strong> {{ $reservation->reservation_date->format('d/m/Y') }}</li>
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
    
    <br>
    <p>Cảm ơn bạn đã đặt bàn tại nhà hàng chúng tôi. Chúng tôi rất mong được phục vụ bạn!</p>
    <p>Nếu có bất kỳ thắc mắc nào, vui lòng liên hệ với chúng tôi.</p>
</body>
</html>

