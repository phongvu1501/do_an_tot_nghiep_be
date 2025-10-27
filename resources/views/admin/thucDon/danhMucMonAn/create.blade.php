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
                                    <form action="{{ route('admin.thucDon.danhMucMonAn.store') }}" method="post">
                                        @csrf
                                        <div class="form-group">
                                            <label for="name">Tên danh mục món ăn</label>
                                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                                id="name" name="name" placeholder="Nhập tên danh mục món ăn"
                                                value="{{ old('name') }}">
                                            @error('name')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                            @if (session('error_name'))
                                                <span class="text-danger">{{ session('error_name') }}</span>
                                            @endif
                                        </div>
                                        <div class="form-group">
                                            <label for="description">Mô tả</label>
                                            <input type="text"
                                                class="form-control @error('description') is-invalid @enderror"
                                                id="description" name="description" placeholder="Nhập Mô tả"
                                                value="{{ old('description') }}">
                                            @error('description')
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
                                                @endphp
                                                @foreach ($statuses as $value => $label)
                                                    <option value="{{ $value }}"
                                                        {{ old('status') == $value ? 'selected' : '' }}>
                                                        {{ $label }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('status')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <button type="submit" class="btn btn-primary">Thêm danh mục món ăn</button>
                                            <a href="{{ route('admin.thucDon.danhMucMonAn.index') }}"
                                                class="btn btn-secondary">Quay lại</a>
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
