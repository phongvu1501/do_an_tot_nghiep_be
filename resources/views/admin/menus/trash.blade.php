@extends('admin.layouts.main')

@section('noidung')
    <div class="content-wrapper">
        <div class="container-fluid">

            {{-- Header --}}
            <div class="row mb-3">
                <div class="col-12 d-flex justify-content-between align-items-center">
                    <h1><b>Thùng rác - Món ăn đã xóa</b></h1>
                    <a href="{{ route('admin.menus.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Quay lại danh sách
                    </a>
                </div>
            </div>

            {{-- Thông báo --}}
            @if (session('success'))
                <div id="notify-alert" class="alert alert-dismissible fade show shadow-sm position-fixed" role="alert"
                    style="
                                            top: 20px;
                                            right: 20px;
                                            z-index: 1050;
                                            min-width: 320px;
                                            max-width: 420px;
                                            border-radius: 10px;
                                            font-size: 14px;
                                            background-color: #e9f7ef;
                                            border: 1px solid #b7e4c7;
                                            color: #2d6a4f;
                                        ">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-check-circle mr-2" style="color:#40916c;font-size:18px;"></i>
                        <div class="flex-grow-1">
                            {{ session('success') }}
                        </div>
                        <button type="button" class="close ml-2" data-dismiss="alert" style="color:#2d6a4f">
                            <span>&times;</span>
                        </button>
                    </div>
                </div>
            @endif

            {{-- Bảng thùng rác --}}
            <div class="card">
                <div class="card-body table-responsive">
                    <table class="table table-bordered table-striped text-center align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Tên món</th>
                                <th>Ảnh</th>
                                <th>Danh mục</th>
                                <th>Giá</th>
                                <th>Ngày xóa</th>
                                <th>Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($trashedMenus as $menu)
                                <tr>
                                    <td>{{ $menu->id }}</td>
                                    <td>{{ $menu->name }}</td>

                                    {{-- Ảnh món ăn --}}
                                    <td>
                                        @if ($menu->image)
                                            <img src="{{ asset('storage/' . $menu->image) }}" width="100"
                                                class="rounded shadow-sm" alt="{{ $menu->name }}">
                                        @else
                                            <span class="text-muted">Không có</span>
                                        @endif
                                    </td>

                                    <td>{{ $menu->category->name ?? 'Không xác định' }}</td>
                                    <td>{{ number_format($menu->price, 0, ',', '.') }} VNĐ</td>
                                    <td>{{ $menu->deleted_at->format('d/m/Y H:i') }}</td>

                                    {{-- Hành động --}}
                                    <td>
                                        {{-- Khôi phục --}}
                                        <form action="{{ route('admin.menus.restore', $menu->id) }}" method="POST"
                                            class="d-inline">
                                            @csrf
                                            <button class="btn btn-success btn-sm">
                                                <i class="fas fa-undo"></i> Khôi phục
                                            </button>
                                        </form>

                                        {{-- Xóa vĩnh viễn --}}
                                        <form action="{{ route('admin.menus.forceDelete', $menu->id) }}" method="POST"
                                            class="d-inline"
                                            onsubmit="return confirm('Bạn có chắc muốn xóa vĩnh viễn món này?');">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-danger btn-sm">
                                                <i class="fas fa-trash"></i> Xóa vĩnh viễn
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted">Không có món nào trong thùng rác.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
    <script>
        setTimeout(() => {
            const alert = document.getElementById('notify-alert');
            if (alert) {
                alert.classList.remove('show');
                alert.classList.add('fade');
                setTimeout(() => alert.remove(), 300);
            }
        }, 3000);
    </script>
@endsection
