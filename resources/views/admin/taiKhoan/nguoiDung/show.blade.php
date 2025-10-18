@extends('admin.layouts.main')

@section('noidung')
    <div class="content-wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Chi tiết : {{ $tenBan }} </h3>
                        </div>
                        <div class="card-body">
                            <table id="example2" class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Tên bàn</th>
                                        <th>Sức chứa</th>
                                        <th>Trạng thái</th>
                                        <th>Ngày tạo</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
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

                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>Tên bàn</th>
                                        <th>Sức chứa</th>
                                        <th>Trạng thái</th>
                                        <th>Ngày tạo</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <div class="row mb-3 ">
                            <div class="col-12 ml-3">
                                <a href="{{ route('admin.banAn.index') }}" class="btn btn-secondary">Quay lại</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
