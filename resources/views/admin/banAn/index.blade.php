@extends('admin.layouts.main')

@section('noidung')
    <div class="content-wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Danh sách bàn ăn</h3>
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
                            <div class="d-flex justify-content-between mb-3">
                                <a href="{{ route('admin.banAn.create') }}" class="btn btn-success btn-sm">Thêm mới</a>

                                <form action="{{ route('admin.banAn.index') }}" method="GET" class="d-flex align-items-center">
                                    <input type="date" name="search_date" id="searchDate" class="form-control w-auto" value="{{ request()->get('search_date', date('Y-m-d')) }}" />

                                    <!-- Nút tìm kiếm -->
                                    <button type="submit" class="btn btn-primary btn-sm">Tìm kiếm</button>
                                </form>
                            </div>
                            <table id="example2" class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>STT</th>
                                        <th>Tên bàn</th>
                                        <th>Sức chứa</th>
                                        <th>Trạng thái</th>
                                        <th>Ngày có sẵn</th>
                                        <th>Thời gian có sẵn</th>
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

                                                <!-- Trạng thái bàn -->
                                                <td>
                                                    @switch($table->status)
                                                        @case('active')
                                                            <span class="badge badge-success">Bàn trống</span>
                                                            @break
                                                        @case('inactive')
                                                            <span class="badge badge-secondary">Đã được đặt</span>
                                                            @break
                                                        @default
                                                            {{ $table->status }}
                                                    @endswitch
                                                </td>

                                                <!-- Ngày có sẵn -->
                                                <td>
                                                    @if($table->available_date)
                                                        {{ \Carbon\Carbon::parse($table->available_date)->format('d/m/Y') }}
                                                    @else
                                                        Không có dữ liệu
                                                    @endif
                                                </td>

                                                <!-- Thời gian có sẵn -->
                                                <td>
                                                    @if($table->available_from && $table->available_until)
                                                        Từ {{ \Carbon\Carbon::parse($table->available_from)->format('H:i') }} đến {{ \Carbon\Carbon::parse($table->available_until)->format('H:i') }}
                                                    @else
                                                        Không có dữ liệu
                                                    @endif
                                                </td>

                                                <td>{{ $table->created_at->format('d/m/Y') }}</td>
                                                <td>
                                                    <a href="{{ route('admin.banAn.show', $table->id) }}" class="btn btn-info btn-sm">Chi tiết</a>

                                                    <a href="{{ route('admin.banAn.edit', $table->id) }}" class="btn btn-warning btn-sm">Chỉnh sửa</a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="8">Không có bàn ăn nào.</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>

                            <!-- Phân trang -->
                            <div class="mt-3">
                                {{ $tables->links('pagination::bootstrap-5') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
