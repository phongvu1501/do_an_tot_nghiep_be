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
                                    <h3 class="card-title">{{ $title ?? 'Chỉnh sửa Bàn ăn' }}</h3>
                                </div>
                                <!-- /.card-header -->
                                <div class="card-body">
                                    <form action="{{ route('admin.banAn.update', $banAn->id) }}" method="post">
                                        @csrf
                                        @method('PUT')

                                        <div class="form-group">
                                            <label for="table_number">Tên bàn</label>
                                            <input type="text"
                                                class="form-control @error('table_number') is-invalid @enderror"
                                                id="table_number" name="table_number" placeholder="Nhập tên bàn ăn"
                                                value="{{ old('table_number', $banAn->table_number) }}">
                                            @error('table_number')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                            @if (session('error_table_number'))
                                                <span class="text-danger">{{ session('error_table_number') }}</span>
                                            @endif
                                        </div>

                                        <div class="form-group">
                                            <label for="capacity">Số lượng người</label>
                                            <input type="number"
                                                class="form-control @error('capacity') is-invalid @enderror" id="capacity"
                                                name="capacity" placeholder="Nhập số lượng người"
                                                value="{{ old('capacity', $banAn->capacity) }}">
                                            @error('capacity')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="status">Trạng thái</label>
                                            <select class="form-control @error('status') is-invalid @enderror"
                                                id="status" name="status">
                                                <option value="">Chọn trạng thái</option>
                                                @php
                                                    $statuses = [
                                                        'active' => 'Hoạt động',
                                                        'inactive' => 'Tạm dừng',
                                                    ];

                                                    $currentStatus = old('status', $banAn->status);
                                                @endphp

                                                @foreach ($statuses as $value => $label)
                                                    <option value="{{ $value }}"
                                                        {{ $currentStatus == $value ? 'selected' : '' }}>
                                                        {{ $label }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('status')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <button type="submit" class="btn btn-primary">Cập nhật</button>
                                            <a href="{{ route('admin.banAn.index') }}" class="btn btn-secondary">Quay
                                                lại</a>
                                        </div>
                                    </form>
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
@endsection
