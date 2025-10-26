@extends('admin.layouts.main')

@section('noidung')
    <div class="content-wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">{{ $title ?? 'Chỉnh sửa Bàn ăn' }}</h3>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.banAn.update', $banAn->id) }}" method="post">
                                @csrf
                                @method('PUT')

                                <div class="form-group">
                                    <label for="name">Tên bàn</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                        id="name" name="name" placeholder="Nhập tên bàn"
                                       value="{{ old('name', $banAn->name) }}">
                                    @error('name')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="limit_number">Số lượng người tối đa</label>
                                    <input type="number" class="form-control @error('limit_number') is-invalid @enderror"
                                        id="limit_number" name="limit_number" placeholder="Nhập số lượng người"
                                        value="{{ old('limit_number', $banAn->limit_number) }}">
                                    @error('limit_number')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="status">Trạng thái</label>
                                    <select class="form-control @error('status') is-invalid @enderror" id="status"
                                        name="status">
                                        @php
                                            $statuses = [
                                                'active' => 'Hoạt động',
                                                'inactive' => 'Tạm dừng',
                                            ];
                                        @endphp
                                        @foreach ($statuses as $value => $label)
                                            <option value="{{ $value }}" 
                                                {{ old('status', $banAn->status) == $value ? 'selected' : '' }}>
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
