@extends('admin.layouts.main')

@section('noidung')
    <style>
        .detail-row {
            background: #fafafa;
        }

        .detail-box {
            padding: 15px;
            border-left: 3px solid #007bff;
            background: #ffffff;
            border-radius: 6px;
            margin-bottom: 10px;
        }

        .detail-title {
            font-weight: bold;
            margin-bottom: 8px;
            font-size: 15px;
        }
    </style>

    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>{{ $title ?? 'Quản lý bình luận' }}</h1>
                    </div>
                </div>
            </div>
        </section>

        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">

                        <div class="card">
                            <div class="card-header"></div>

                            <div class="card-body">

                                {{-- Alert thành công --}}
                                @if (session('success'))
                                    <div id="success-alert"
                                        class="alert alert-success alert-dismissible fade show position-fixed"
                                        role="alert"
                                        style="
                                            top: 20px;
                                            right: 20px;
                                            z-index: 1050;
                                            background-color: #d4edda;
                                            color: #155724;
                                            border-color: #c3e6cb;
                                            font-size: 14px;
                                            padding: 10px 15px;
                                            border-radius: 8px;
                                            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
                                            max-width: 300px;">
                                        {{ session('success') }}
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"
                                            style="outline: none;">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                @endif
                                <div class="card mb-3 bg-light">
                                    <div class="card-body">
                                        <form method="GET" class="row align-items-end">

                                            <div class="col-md-3">
                                                <label class="font-weight-bold">Người dùng</label>
                                                <select name="user_id" class="form-control">
                                                    <option value="">-- Tất cả --</option>
                                                    @foreach ($users as $user)
                                                        <option value="{{ $user->id }}"
                                                            {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                                            {{ $user->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="col-md-2">
                                                <label class="font-weight-bold">Đánh giá</label>
                                                <select name="rating" class="form-control">
                                                    <option value="">Tất cả</option>
                                                    @for ($i = 5; $i >= 1; $i--)
                                                        <option value="{{ $i }}"
                                                            {{ request('rating') == $i ? 'selected' : '' }}>
                                                            {{ $i }} ⭐
                                                        </option>
                                                    @endfor
                                                </select>
                                            </div>

                                            <div class="col-md-2">
                                                <label class="font-weight-bold">Trạng thái</label>
                                                <select name="status" class="form-control">
                                                    <option value="">Tất cả</option>
                                                    <option value="1"
                                                        {{ request('status') === '1' ? 'selected' : '' }}>
                                                        Hiển thị
                                                    </option>
                                                    <option value="0"
                                                        {{ request('status') === '0' ? 'selected' : '' }}>
                                                        Đã ẩn
                                                    </option>
                                                </select>
                                            </div>

                                            <div class="col-md-2">
                                                <label class="font-weight-bold">Từ ngày</label>
                                                <input type="date" name="from_date" class="form-control"
                                                    value="{{ request('from_date') }}">
                                            </div>

                                            <div class="col-md-2">
                                                <label class="font-weight-bold">Đến ngày</label>
                                                <input type="date" name="to_date" class="form-control"
                                                    value="{{ request('to_date') }}">
                                            </div>

                                            <div class="col-md-1">
                                                <button class="btn btn-primary btn-block">
                                                    <i class="fas fa-search">Tìm kiếm</i>
                                                </button>
                                                <a href="{{ route('admin.comments.index') }}"
                                                    class="btn btn-secondary btn-block mt-1">
                                                    <i class="fas fa-redo">Đặt lại</i>
                                                </a>
                                            </div>

                                        </form>
                                    </div>
                                </div>

                                <table id="example2" class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>STT</th>
                                            <th>Người dùng</th>
                                            <th>Bàn đặt</th>
                                            <th>Đánh giá</th>
                                            <th>Bình luận</th>
                                            <th>Trạng thái</th>
                                            <th>Ngày bình luận</th>
                                            <th>Thao tác</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @forelse ($comments as $index => $item)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>

                                                <td>{{ $item->user->name ?? 'Không rõ' }}</td>

                                                <td>
                                                    {{ $item->reservation->table->name ?? 'Bàn ' . $item->reservation_id }}
                                                </td>

                                                <td><strong>{{ $item->rating }}/5</strong></td>

                                                <td>{{ $item->comment }}</td>

                                                <td>
                                                    @if ($item->status == 1)
                                                        <span class="badge badge-success">Hiển thị</span>
                                                    @else
                                                        <span class="badge badge-secondary">Đã ẩn</span>
                                                    @endif
                                                </td>

                                                <td>{{ $item->created_at->format('d/m/Y H:i') }}</td>

                                                <td class="text-nowrap">

                                                    {{-- Nút xem chi tiết --}}
                                                    <button class="btn btn-info btn-sm"
                                                        onclick="toggleDetail({{ $item->id }})">
                                                        Chi tiết
                                                    </button>

                                                    {{-- Ẩn / Hiện --}}
                                                    <form action="{{ route('admin.comments.toggleStatus', $item->id) }}"
                                                        method="POST" style="display:inline;">
                                                        @csrf
                                                        @if ($item->status == 1)
                                                            <button class="btn btn-warning btn-sm">Ẩn</button>
                                                        @else
                                                            <button class="btn btn-success btn-sm">Hiện</button>
                                                        @endif
                                                    </form>
                                                </td>
                                            </tr>

                                            {{-- Hàng chi tiết --}}
                                            <tr id="detail-{{ $item->id }}" class="detail-row" style="display:none;">
                                                <td colspan="8">
                                                    <div class="detail-box">
                                                        <div class="detail-title">📌 Thông tin chi tiết:</div>

                                                        <p><strong>Người dùng:</strong> {{ $item->user->name }}</p>

                                                        <p><strong>Số điện thoại:</strong>
                                                            {{ $item->user->phone ?? 'Không có' }}
                                                        </p>

                                                        <p><strong>Bàn đặt:</strong>
                                                            {{ $item->reservation->table->name ?? 'Không xác định' }}
                                                        </p>

                                                        <p><strong>Thời gian đặt:</strong>
                                                            {{ $item->reservation->created_at->format('d/m/Y H:i') }}
                                                        </p>

                                                        <p><strong>Điểm đánh giá:</strong> {{ $item->rating }}/5</p>

                                                        <p><strong>Bình luận:</strong> {{ $item->comment }}</p>

                                                        <p><strong>Trạng thái:</strong>
                                                            @if ($item->status == 1)
                                                                <span class="badge badge-success">Hiển thị</span>
                                                            @else
                                                                <span class="badge badge-secondary">Đã ẩn</span>
                                                            @endif
                                                        </p>
                                                    </div>
                                                </td>
                                            </tr>

                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center">Không có bình luận nào.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>

                                    <tfoot>
                                        <tr>
                                            <th>STT</th>
                                            <th>Người dùng</th>
                                            <th>Bàn đặt</th>
                                            <th>Đánh giá</th>
                                            <th>Bình luận</th>
                                            <th>Trạng thái</th>
                                            <th>Thời gian bình luận</th>
                                            <th>Thao tác</th>
                                        </tr>
                                    </tfoot>
                                </table>

                                {{ $comments->links() }}

                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </section>
    </div>

    <script>
        function toggleDetail(id) {
            const row = document.getElementById("detail-" + id);
            row.style.display = row.style.display === "none" ? "" : "none";
        }

        setTimeout(() => {
            const alert = document.getElementById('success-alert');
            if (alert) {
                alert.classList.remove('show');
                alert.classList.add('fade');
                setTimeout(() => alert.remove(), 500);
            }
        }, 3000);
    </script>
@endsection
