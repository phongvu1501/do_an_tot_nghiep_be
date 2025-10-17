@extends('admin.layouts.main')

@section('title', 'Chỉnh sửa danh mục món ăn')

@section('noidung')
    <div class="content-wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="container mt-4">
                            <h1>Chỉnh sửa danh mục món ăn</h1>
                            <form action="{{ route('admin.menu_categories.update', $menuCategory->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="mb-3">
                                    <label class="form-label">Tên danh mục</label>
                                    <input type="text" name="name" class="form-control"
                                        value="{{ old('name', $menuCategory->name) }}" required>
                                    @error('name')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Mô tả</label>
                                    <textarea name="description" class="form-control" rows="3">{{ old('description', $menuCategory->description) }}</textarea>
                                </div>
                                <button type="submit" class="btn btn-primary">Cập nhật</button>
                                <a href="{{ route('admin.menu_categories.index') }}" class="btn btn-secondary">Quay lại</a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
