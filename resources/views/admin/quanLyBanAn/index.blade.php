@extends('admin.layouts.main')

@section('noidung')
    <div class="content-wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            {{-- <h3 class="card-title">Chi tiết các lớp trong khối : {{ $lopHoc10 }} </h3> --}}
                        </div>
                        <div class="card-body">
                            @if (session('success'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert" id="success-alert">
                                    {{ session('success') }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif
                            <a href="{{ route('admin.banAn.create') }}" class="btn btn-success btn-sm mb-3 col-1">Thêm
                                mới</a>
                            <table id="example2" class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>STT</th>
                                        <th>Tên bàn</th>
                                        <th>Sức chứa</th>
                                        <th>Trạng thái</th>
                                        <th>Ngày tạo</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if (isset($tables) && count($tables) > 0)
                                        @foreach ($tables as $index => $table)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $table->table_number }}</td>
                                                <td>{{ $table->capacity }}</td>
                                                <td> @switch($table->status)
                                                        @case('active')
                                                            <span class="badge badge-success">Hoạt động</span>
                                                        @break

                                                        @case('inactive')
                                                            <span class="badge badge-secondary">Tạm dừng</span>
                                                        @break

                                                        @default
                                                            {{ $table->status }}
                                                    @endswitch
                                                </td>
                                                <td>{{ $table->created_at->format('d/m/Y') }}</td>
                                                <td>
                                                    <a href="{{ route('admin.banAn.show', $table->id) }}"
                                                        class="btn btn-info btn-sm">Chi tiết</a>

                                                    <a href="{{ route('admin.banAn.edit', $table->id) }}"
                                                        class="btn btn-warning btn-sm">Chỉnh sửa</a>

                                                    @if ($table->status === 'active')
                                                        <form action="{{ route('admin.banAn.disable', $table->id) }}"
                                                            method="POST" style="display:inline;">
                                                            @csrf
                                                            @method('PUT')

                                                            <button type="submit" class="btn btn-danger btn-sm"
                                                                onclick="return confirm('Bạn có chắc chắn muốn DỪNG HOẠT ĐỘNG bàn {{ $table->table_number }} này không?');">
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
                                        <th>Tên bàn</th>
                                        <th>Sức chứa</th>
                                        <th>Trạng thái</th>
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
        $(document).ready(function() {
            if ($('#success-alert').length) {
                setTimeout(function() {
                    $('#success-alert').fadeOut('slow', function() {
                        $(this).remove();
                    });
                }, 3000);
            }
        });
    </script>
@endsection
