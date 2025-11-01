@extends('admin.layouts.main')

@section('noidung')
    <div class="content-wrapper">
        <div class="container-fluid">
            <div class="col-12 d-flex justify-content-between align-items-center">
                <h1><b>Chỉnh sửa món ăn</b></h1>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="container mt-4">
                            <form action="{{ route('admin.menus.update', $menu->id) }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <div class="mb-3">
                                    <label class="form-label">Tên món ăn</label>
                                    <input type="text" name="name" value="{{ $menu->name }}" class="form-control"
                                        required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Danh mục</label>
                                    <select name="category_id" class="form-select" required>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}"
                                                {{ $menu->category_id == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Mô tả</label>
                                    <textarea name="description" class="form-control">{{ $menu->description }}</textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Giá</label>
                                    <input type="number" name="price" class="form-control" value="{{ $menu->price }}"
                                        step="1000" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Ảnh hiện tại</label><br>
                                    @if ($menu->image)
                                        <img src="{{ asset('storage/' . $menu->image) }}" alt="image" width="100">
                                    @else
                                        <span>Không có ảnh</span>
                                    @endif
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Thay ảnh mới</label>
                                    <input type="file" name="image" class="form-control">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Trạng thái</label>
                                    <select name="status" class="form-select">
                                        <option value="1" {{ $menu->status ? 'selected' : '' }}>Hiển thị</option>
                                        <option value="0" {{ !$menu->status ? 'selected' : '' }}>Ẩn</option>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-primary">Cập nhật</button>
                                <a href="{{ route('admin.menus.index') }}" class="btn btn-secondary">Hủy</a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
