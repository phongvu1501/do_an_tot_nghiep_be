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
                                        <th>Ngày có sẵn</th>
                                        <th>Thời gian có sẵn</th>
                                        <th>Ngày tạo</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>{{ $table->table_number }}</td>
                                        <td>{{ $table->capacity }}</td>
                                        <td>
                                            @switch($table->status)
                                                @case('active')
                                                    <span class="badge badge-success">Bàn còn trống</span>
                                                    @break
                                                @case('inactive')
                                                    <span class="badge badge-secondary">Bàn đã đặt</span>
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
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <th>Tên bàn</th>
                                    <th>Sức chứa</th>
                                    <th>Trạng thái</th>
                                    <th>Ngày có sẵn</th>
                                    <th>Thời gian có sẵn</th>
                                    <th>Ngày tạo</th>
                                </tfoot>
                            </table>
                        </div>
                        <div class="row mb-3 ">
                            <div class="col-12 ml-3">
                                <a href="{{route('admin.banAn.index')}}" class="btn btn-secondary">Quay lại</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
