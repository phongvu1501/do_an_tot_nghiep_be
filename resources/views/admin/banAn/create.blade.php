@extends('admin.layouts.main')

@section('noidung')
    <div class="content-wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">{{ $title }}</h3>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.banAn.store') }}" method="post">
                                @csrf
                                <div class="form-group">
                                    <label for="name">Tên bàn</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                        id="name" name="name" placeholder="Nhập tên bàn (ví dụ: Bàn 1)"
                                        value="{{ old('name') }}">
                                    @error('name')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="limit_number">Số lượng người tối đa</label>
                                    <input type="number" class="form-control @error('limit_number') is-invalid @enderror"
                                        id="limit_number" name="limit_number" placeholder="Nhập số lượng người"
                                        value="{{ old('limit_number', 8) }}">
                                    @error('limit_number')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                    <small class="form-text text-muted">Mặc định: 8 người</small>
                                </div>

                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">Thêm bàn</button>
                                    <a href="{{ route('admin.banAn.index') }}" class="btn btn-secondary">Quay lại</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
