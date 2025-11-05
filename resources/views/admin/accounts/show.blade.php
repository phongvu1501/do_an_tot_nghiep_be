@extends('admin.layouts.main')

@section('noidung')
<div class="content-wrapper p-4" style="background-color: #f8f9fa; min-height: 100vh;">
    
    <a href="{{ route('user.accounts') }}" class="btn btn-secondary mb-3 shadow-sm">
        ← Quay lại danh sách
    </a>

    <!-- Thông tin tài khoản -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body">
            <h3 class="fw-bold mb-3 text-primary"> Thông tin tài khoản</h3>
            <div class="row mb-2">
                <div class="col-md-6">
                    <p><strong>Họ tên:</strong> {{ $user->name }}</p>
                    <p><strong>Email:</strong> {{ $user->email }}</p>
                    <p><strong>Số điện thoại:</strong> {{ $user->phone ?? 'Chưa có' }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Vai trò:</strong> {{ ucfirst($user->role ?? 'user') }}</p>
                    <p><strong>Ngày tạo:</strong> {{ $user->created_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Đơn đặt bàn -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body">
            <h4 class="fw-bold mb-3 text-success"> Danh sách các lần đặt bàn</h4>

            @forelse($user->reservations as $reservation)
                <div class="border rounded-3 p-3 mb-3 bg-white shadow-sm">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h5 class="fw-bold mb-0 text-primary">Đơn đặt #{{ $reservation->id }}</h5>
                        <small class="text-muted">
                            <i class="fas fa-calendar-alt me-1"></i> 
                            {{ $reservation->reservation_date }}
                        </small>
                    </div>

                    <p class="mb-1"><strong>Số người:</strong> {{ $reservation->num_people }}</p>
                    <p class="mb-2"><strong>Khu vực:</strong> {{ $reservation->depsection ?? 'Không rõ' }}</p>

                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="fw-bold text-dark mt-3"> Bàn đã đặt:</h6>
                            @if($reservation->tables->isNotEmpty())
                                <ul class="mb-0">
                                    @foreach($reservation->tables as $table)
                                        <li>Bàn số {{ $table->id }} {{ $table->name ? '(' . $table->name . ')' : '' }}</li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="text-muted">Không có bàn được chọn.</p>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <h6 class="fw-bold text-dark mt-3"> Món ăn đã đặt:</h6>
                            @if($reservation->menus->isNotEmpty())
                                <ul class="mb-0">
                                    @foreach($reservation->menus as $menu)
                                        <li>{{ $menu->name }} — SL: {{ $menu->pivot->quantity }}</li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="text-muted">Không có món ăn được chọn.</p>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="alert alert-warning text-center mb-0">
                    Người dùng này chưa có đặt bàn nào.
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
