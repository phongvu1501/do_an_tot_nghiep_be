@extends('admin.layouts.main')

@section('noidung')
    <div class="content-wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">{{ $banAn }}</h3>
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
                            
                            @if (session('error'))
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    {{ session('error') }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif
                            
                            <div class="d-flex justify-content-between mb-3">
                                <a href="{{ route('admin.banAn.create') }}" class="btn btn-success btn-sm">Thêm mới bàn</a>
                            </div>
                            
                            <table id="example2" class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>STT</th>
                                        <th>Tên bàn</th>
                                        <th>Số lượng người</th>
                                        <th>Trạng thái</th>
                                        <th>Ngày tạo</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if (isset($tables) && count($tables) > 0)
                                        @foreach ($tables as $index => $table)
                                            <tr>
                                                <td>{{ $tables->firstItem() + $index }}</td>
                                                <td><strong>{{ $table->name }}</strong></td>
                                                <td>{{ $table->limit_number }} người</td>

                                                <!-- Trạng thái bàn -->
                                                <td>
                                                    @switch($table->status)
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

                                                <td>{{ $table->created_at->format('d/m/Y H:i') }}</td>
                                                <td>
                                                    <a href="{{ route('admin.banAn.edit', $table->id) }}" class="btn btn-warning btn-sm">Sửa</a>
                                                    
                                                    @if($table->status == 'active')
                                                        <form action="{{ route('admin.banAn.disable', $table->id) }}" method="POST" style="display:inline-block;">
                                                            @csrf
                                                            @method('PUT')
                                                            <button type="submit" class="btn btn-secondary btn-sm" 
                                                                onclick="return confirm('Bạn có chắc muốn tạm dừng bàn này?')">
                                                                Tạm dừng
                                                            </button>
                                                        </form>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="6" class="text-center">Chưa có bàn ăn nào.</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>

                            <!-- Pagination -->
                            <div class="mt-3">
                                {{ $tables->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
