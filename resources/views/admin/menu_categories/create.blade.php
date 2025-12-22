@extends('admin.layouts.main')

@section('title', 'Thêm danh mục món ăn')

@section('noidung')
    <div class="content-wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">

                        {{-- Header --}}
                        <div class="card-header bg-white">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <h3 class="mb-0 font-weight-bold">Thêm danh mục món ăn</h3>
                                </div>
                                <div class="col-md-6 text-right">
                                    <a href="{{ route('admin.menu_categories.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left"></i> Quay lại
                                    </a>
                                </div>
                            </div>
                        </div>

                        {{-- Body --}}
                        <div class="card-body">
                            <form action="{{ route('admin.menu_categories.store') }}" method="POST">
                                @csrf

                                <div class="form-group">
                                    <label class="font-weight-bold">Tên danh mục</label>
                                    <input type="text" name="name" class="form-control" value="{{ old('name') }}"
                                        required>

                                    @error('name')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label class="font-weight-bold">Mô tả</label>
                                    <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                                </div>

                                <div class="mt-4">
                                    <button type="submit" class="btn btn-success mr-2">
                                        <i class="fas fa-save"></i> Thêm
                                    </button>
                                    <a href="{{ route('admin.menu_categories.index') }}" class="btn btn-secondary">
                                        Hủy
                                    </a>
                                </div>
                            </form>
                        </div>

                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
