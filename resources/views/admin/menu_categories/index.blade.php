@extends('admin.layouts.main')

@section('title', 'Danh mục món ăn')

@section('noidung')
    <div class="content-wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        {{-- Header --}}
                        <div class="card-header bg-white">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <h3 class="mb-0 font-weight-bold">Danh mục món ăn</h3>
                                </div>
                                <div class="col-md-6 text-right">
                                    <a href="{{ route('admin.menu_categories.create') }}" class="btn btn-primary">
                                        <i class="fas fa-plus"></i> Thêm danh mục
                                    </a>
                                </div>
                            </div>
                        </div>

                        {{-- Body --}}
                        <div class="card-body">

                            {{-- Thông báo --}}
                            @if (session('success'))
                                <div id="notify-alert" class="alert alert-dismissible fade show shadow-sm position-fixed"
                                    role="alert"
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
                                        <button type="button" class="close ml-2" data-dismiss="alert"
                                            style="color:#2d6a4f">
                                            <span>&times;</span>
                                        </button>
                                    </div>
                                </div>
                            @endif

                            {{-- Bảng --}}
                            <table class="table table-bordered table-striped mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th width="5%">ID</th>
                                        <th width="20%">Tên danh mục</th>
                                        <th>Mô tả</th>
                                        <th width="20%">Hành động</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($categories as $category)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                <a href="{{ route('admin.menus.index', ['category_id' => $category->id]) }}"
                                                    class="font-weight-bold text-primary" style="text-decoration: none;">
                                                    {{ $category->name }}
                                                </a>
                                            </td>
                                            <td>{{ $category->description ?? '—' }}</td>
                                            <td>
                                                <a href="{{ route('admin.menus.index', ['category_id' => $category->id]) }}"
                                                    class="btn btn-sm btn-info mr-1" title="Xem món ăn">
                                                    <i class="fas fa-list"></i> Xem món
                                                </a>

                                                <a href="{{ route('admin.menu_categories.edit', $category->id) }}"
                                                    class="btn btn-sm btn-warning mr-1">
                                                    Sửa
                                                </a>

                                                <form action="{{ route('admin.menu_categories.destroy', $category->id) }}"
                                                    method="POST" class="d-inline"
                                                    onsubmit="return confirm('Bạn có chắc muốn xóa danh mục này?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-sm btn-danger" type="submit">
                                                        Xóa
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center">Chưa có danh mục nào.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>

                        </div>
                    </div>

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
