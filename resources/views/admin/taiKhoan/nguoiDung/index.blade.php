@extends('admin.layouts.main')

@section('noidung')
    <div class="content-wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">{{ $title }} </h3>
                        </div>
                        <div class="card-body">
                            {{-- @if (session('success'))
                                <div id="success-alert" class="alert alert-success alert-dismissible fade show position-fixed"
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
                            @endif --}}
                            {{-- <a href="{{ route('admin.banAn.create') }}" class="btn btn-success btn-sm mb-3 col-1">Thêm
                                mới</a> --}}
                            <table id="example2" class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>STT</th>
                                        <th>Tên ngươi dùng</th>
                                        <th>Email</th>
                                        <th>Vai trò</th>
                                        <th>Ngày tạo</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if (isset($accountUsers) && count($accountUsers) > 0)
                                        @foreach ($accountUsers as $index => $accountUser)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $accountUser->name }}</td>
                                                <td>{{ $accountUser->email }}</td>
                                                <td>{{ $accountUser->role }}</td>
                                                {{-- <td> @switch($accountUser->role)
                                                        @case('active')
                                                            <span class="badge badge-success">Hoạt động</span>
                                                        @break

                                                        @case('inactive')
                                                            <span class="badge badge-secondary">Tạm dừng</span>
                                                        @break

                                                        @default
                                                            {{ $accountUser->role }}
                                                    @endswitch
                                                </td> --}}
                                                <td>{{ $accountUser->created_at->format('d/m/Y') }}</td>
                                                <td>
                                                    <a href="{{ route('admin.taiKhoan.nguoiDung.show', $accountUser->id) }}"
                                                        class="btn btn-info btn-sm">Chi tiết</a>

                                                    @if ($accountUser->role === 'user')
                                                        <form
                                                            {{-- action="{{ route('admin.taiKhoan.nguoiDung.disable', $accountUser->id) }}"
                                                            method="POST" style="display:inline;">
                                                            @csrf
                                                            @method('PUT') --}}

                                                            <button type="submit" class="btn btn-danger btn-sm"
                                                                onclick="return confirm('Bạn có chắc chắn muốn đổi vai trò tài khoản {{ $accountUser->table_number }} này không?');">
                                                                </i> Đổi vai trò
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
                                            <td colspan="6">Không có tài khoản người dùng nào.</td>
                                        </tr>
                                    @endif
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>STT</th>
                                        <th>Tên ngươi dùng</th>
                                        <th>Email</th>
                                        <th>Vai trò</th>
                                        <th>Ngày tạo</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
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
