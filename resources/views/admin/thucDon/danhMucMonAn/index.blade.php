@extends('admin.layouts.main')

@section('noidung')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>{{ $title }}</h1>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card">
                                <div class="card-header">

                                </div>
                                <!-- /.card-header -->
                                <div class="card-body">
                                    @if (session('success'))
                                        <div id="success-alert"
                                            class="alert alert-success alert-dismissible fade show position-fixed"
                                            role="alert"
                                            style="
                                        top: 20px;
                                        right: 20px;
                                        z-index: 1050;
                                        background-color: #d4edda; /* xanh lá nhạt */
                                        color: #155724;
                                        border-color: #c3e6cb;
                                        font-size: 14px;
                                        padding: 10px 15px;
                                        border-radius: 8px;
                                        box-shadow: 0 2px 6px rgba(0,0,0,0.1);
                                        max-width: 300px;
                                    ">
                                            {{ session('success') }}
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"
                                                style="outline: none;">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                    @endif
                                    <a href="{{ route('admin.thucDon.danhMucMonAn.create') }}"
                                        class="btn btn-success btn-sm mb-3 col-1">Thêm
                                        mới</a>
                                    <table id="example2" class="table table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th>STT</th>
                                                <th>Tên danh mục món ăn</th>
                                                <th>Miêu tả</th>
                                                <th>Trạng thái</th>
                                                <th>Ngày tạo</th>
                                                <th>Thao tác</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if (isset($menuCategories) && count($menuCategories) > 0)
                                                @foreach ($menuCategories as $index => $menuCategory)
                                                    <tr>
                                                        <td>{{ $index + 1 }}</td>
                                                        <td>{{ $menuCategory->name }}</td>
                                                        <td>{{ $menuCategory->description }}</td>
                                                        <td> @switch($menuCategory->status)
                                                                @case('active')
                                                                    <span class="badge badge-success">Hoạt động</span>
                                                                @break

                                                                @case('inactive')
                                                                    <span class="badge badge-secondary">Tạm dừng</span>
                                                                @break

                                                                @default
                                                                    {{ $menuCategory->status }}
                                                            @endswitch
                                                        </td>
                                                        <td>{{ $menuCategory->created_at->format('d/m/Y') }}</td>
                                                        <td>
                                                            <a href="{{ route('admin.thucDon.danhMucMonAn.show', $menuCategory->id) }}"
                                                                class="btn btn-info btn-sm">Chi tiết</a>

                                                            <a href="{{ route('admin.thucDon.danhMucMonAn.edit', $menuCategory->id) }}"
                                                                class="btn btn-warning btn-sm">Chỉnh sửa</a>

                                                            @if ($menuCategory->status === 'active')
                                                                <form action="{{ route('admin.thucDon.danhMucMonAn.disable', $menuCategory->id) }}" method="POST" style="display:inline;">
                                                                    @csrf
                                                                    @method('PUT')
                                                                    <button type="submit" class="btn btn-danger btn-sm"
                                                                        onclick="return confirm('Bạn có chắc chắn muốn DỪNG HOẠT ĐỘNG danh mục món ăn : {{ $menuCategory->name }} này không?');">
                                                                        </i> Dừng hoạt động
                                                                    </button>
                                                                </form>
                                                            @else
                                                                <span class="badge badge-secondary">Đã dừng</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td colspan="6">Không có bàn ăn nào.</td>
                                                </tr>
                                            @endif
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th>STT</th>
                                                <th>Tên danh mục món ăn</th>
                                                <th>Miêu tả</th>
                                                <th>Trạng thái</th>
                                                <th>Ngày tạo</th>
                                                <th>Thao tác</th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                                <!-- /.card-body -->
                            </div>
                            <!-- /.card -->
                        </div>
                        <!-- /.col -->
                    </div>
                    <!-- /.row -->
                </div>
                <!-- /.container-fluid -->
        </section>
        <!-- /.content -->
    </div>
    <script>
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
