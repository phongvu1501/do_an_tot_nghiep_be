@extends('admin.layouts.main')

@section('noidung')
    <div class="content-wrapper">
        <div class="container-fluid">
            <div class="row mb-3">
                <div class="col-12 d-flex justify-content-between align-items-center">
                    <h1><b>Danh sách món ăn</b></h1>
                    <a href="{{ route('admin.menus.trash') }}" class="btn btn-outline-danger me-2">
                        <i class="fas fa-trash"></i> Thùng rác
                        @if ($trashedCount > 0)
                            <span class="badge bg-danger">{{ $trashedCount }}</span>
                        @endif
                    </a>
                    <a href="{{ route('admin.menus.create') }}" class="btn btn-primary">+ Thêm món ăn</a>
                </div>
            </div>

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

            {{-- Thông báo khi đang lọc theo danh mục --}}
            @if ($selectedCategoryId)
                @php
                    $selectedCategory = $categories->firstWhere('id', $selectedCategoryId);
                @endphp
                @if ($selectedCategory)
                    <div class="alert alert-info d-flex justify-content-between align-items-center">
                        <span>
                            <i class="fas fa-filter"></i> Đang hiển thị món ăn của danh mục:
                            <strong>{{ $selectedCategory->name }}</strong>
                            <span class="badge bg-primary ms-2">{{ $menus->count() }} món</span>
                        </span>
                        <a href="{{ route('admin.menus.index') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-times"></i> Xóa lọc
                        </a>
                    </div>
                @endif
            @endif

            {{-- Bộ lọc theo danh mục --}}
            <div class="card mb-3">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.menus.index') }}" id="filterForm">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-4">
                                <label for="category_id" class="form-label"><strong>Lọc theo danh mục:</strong></label>
                                <select name="category_id" id="category_id" class="form-control">
                                    <option value="">Tất cả</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}"
                                            {{ (string) $selectedCategoryId == (string) $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <script>
                document.getElementById('category_id').addEventListener('change', function() {
                    document.getElementById('filterForm').submit();
                });
            </script>

            <div class="card">
                <div class="card-body table-responsive">
                    <table class="table table-bordered text-center align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Tên món</th>
                                <th>Ảnh</th>
                                <th>Danh mục</th>
                                <th>Giá (VNĐ)</th>
                                <th>Trạng thái</th>
                                <th>Mô tả</th>
                                <th>Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($menus as $menu)
                                <tr>
                                    <td>{{ $menu->id }}</td>
                                    <td>{{ $menu->name }}</td>
                                    <td>
                                        @if ($menu->image)
                                            <img src="{{ asset('storage/' . $menu->image) }}" width="150"
                                                class="rounded shadow-sm" alt="Ảnh món ăn">
                                        @else
                                            <span class="text-muted">Không có ảnh</span>
                                        @endif
                                    </td>
                                    <td>{{ $menu->category->name ?? 'Không có' }}</td>
                                    <td>{{ number_format($menu->price, 0, ',', '.') }}</td>
                                    <td>
                                        @if ($menu->status)
                                            <span class="badge bg-success">Hoạt động</span>
                                        @else
                                            <span class="badge bg-secondary">Ẩn</span>
                                        @endif
                                    </td>
                                    <td>{{ $menu->description ?? '---' }}</td>
                                    <td>
                                        <!-- Nút Xem -->
                                        <button type="button" class="btn btn-info btn-sm me-1" data-bs-toggle="modal"
                                            data-bs-target="#viewMenuModal{{ $menu->id }}">
                                            <i class="fas fa-eye"></i> Xem
                                        </button>
                                        <a href="{{ route('admin.menus.edit', $menu->id) }}"
                                            class="btn btn-sm btn-warning">Sửa</a>
                                        <form action="{{ route('admin.menus.destroy', $menu->id) }}" method="POST"
                                            class="d-inline"
                                            onsubmit="return confirm('Bạn có chắc chắn muốn xóa món này không?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-danger">Xóa</button>
                                        </form>
                                    </td>
                                </tr>

                                <!-- 🔹 Modal xem chi tiết cho từng món -->
                                <div class="modal fade" id="viewMenuModal{{ $menu->id }}" tabindex="-1"
                                    aria-labelledby="viewMenuLabel{{ $menu->id }}" aria-hidden="true">
                                    <div class="modal-dialog modal-lg modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header bg-primary text-white">
                                                <h5 class="modal-title" id="viewMenuLabel{{ $menu->id }}">
                                                    {{ $menu->name }}
                                                </h5>
                                                <button type="button" class="btn-close btn-close-white"
                                                    data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>

                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-md-4 text-center">
                                                        @if ($menu->image)
                                                            <img src="{{ asset('storage/' . $menu->image) }}"
                                                                alt="{{ $menu->name }}"
                                                                class="img-fluid rounded shadow-sm">
                                                        @else
                                                            <p class="text-muted">Chưa có ảnh</p>
                                                        @endif
                                                    </div>
                                                    <div class="col-md-8">
                                                        <p><strong>Danh mục:</strong>
                                                            {{ $menu->category->name ?? 'Không xác định' }}</p>
                                                        <p><strong>Mô tả:</strong>
                                                            {{ $menu->description ?? 'Không có mô tả' }}</p>
                                                        <p><strong>Giá:</strong>
                                                            {{ number_format($menu->price, 0, ',', '.') }}
                                                            VNĐ</p>
                                                        <p>
                                                            <strong>Trạng thái:</strong>
                                                            @if ($menu->status)
                                                                <span class="badge bg-success">Hiển thị</span>
                                                            @else
                                                                <span class="badge bg-secondary">Ẩn</span>
                                                            @endif
                                                        </p>
                                                        <p><small><strong>Ngày tạo:</strong>
                                                                {{ $menu->created_at->format('d/m/Y H:i') }}</small></p>
                                                        <p><small><strong>Cập nhật lần cuối:</strong>
                                                                {{ $menu->updated_at->format('d/m/Y H:i') }}</small></p>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                    <i class="fas fa-times"></i> Đóng
                                                </button>
                                                <a href="{{ route('admin.menus.edit', $menu->id) }}"
                                                    class="btn btn-primary">
                                                    <i class="fas fa-edit"></i> Sửa món
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted">Chưa có món ăn nào.</td>
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
