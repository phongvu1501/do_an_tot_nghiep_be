@extends('admin.layouts.main')

@section('title', 'Danh mục món ăn')

@section('noidung')
    <div class="content-wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="container mt-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h1>Danh mục món ăn</h1>
                                <a href="{{ route('admin.menu_categories.create') }}" class="btn btn-primary">
                                    + Thêm danh mục
                                </a>
                            </div>

                            {{-- Hiển thị thông báo --}}
                            @if (session('success'))
                                <div class="alert alert-success">{{ session('success') }}</div>
                            @endif

                            <table class="table table-bordered table-striped">
                                <thead class="table-light">
                                    <tr>
                                        <th width="5%">ID</th>
                                        <th width="20%">Tên danh mục</th>
                                        <th>Mô tả</th>
                                        <th width="20%">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($categories as $category)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $category->name }}</td>
                                            <td>{{ $category->description ?? '—' }}</td>
                                            <td>
                                                <a href="{{ route('admin.menu_categories.edit', $category->id) }}"
                                                    class="btn btn-sm btn-warning">Sửa</a>
                                                <form action="{{ route('admin.menu_categories.destroy', $category->id) }}"
                                                    method="POST" class="d-inline"
                                                    onsubmit="return confirm('Bạn có chắc muốn xóa danh mục này?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-sm btn-danger" type="submit">Xóa</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center">Chưa có danh mục nào.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
