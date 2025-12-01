@extends('admin.layouts.main')

@section('noidung')
    <div class="content-wrapper">
        <div class="container-fluid">
            <div class="col-12 d-flex justify-content-between align-items-center">
                <h1><b>Thêm mới món ăn</b></h1>
            </div>
            <div class="card mt-4">
                <div class="card-body">
                    <form action="{{ route('admin.menus.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="form-group mb-3">
                            <label for="name">Tên món ăn</label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}"
                                class="form-control @error('name') is-invalid @enderror" placeholder="Nhập tên món ăn"
                                maxlength="100" required>
                            @error('name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="category_id">Danh mục</label>
                            <select name="category_id" id="category_id"
                                class="form-control @error('category_id') is-invalid @enderror">
                                <option value="">-- Chọn danh mục --</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}"
                                        {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="price">Giá (VNĐ)</label>
                            <input type="text" name="price" id="price" value="{{ old('price') }}"
                                class="form-control @error('price') is-invalid @enderror"
                                placeholder="Ví dụ: 120000 hoặc 19999.99" maxlength="11" inputmode="decimal" required>
                            @error('price')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="image">Ảnh món ăn</label>
                            <input type="file" name="image" id="image"
                                class="form-control @error('image') is-invalid @enderror">
                            @error('image')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="status">Trạng thái</label>
                            <select name="status" id="status" class="form-control">
                                <option value="1" {{ old('status') == 1 ? 'selected' : '' }}>Hoạt động</option>
                                <option value="0" {{ old('status') == 0 ? 'selected' : '' }}>Ẩn</option>
                            </select>
                        </div>

                        <div class="form-group mb-3">
                            <label for="description">Mô tả</label>
                            <textarea name="description" id="description" rows="3"
                                class="form-control @error('description') is-invalid @enderror" placeholder="Nhập mô tả món ăn" maxlength="1000">{{ old('description') }}</textarea>
                            @error('description')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-success">Lưu</button>
                        <a href="{{ route('admin.menus.index') }}" class="btn btn-secondary">Quay lại</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const priceInput = document.getElementById('price');
            if (priceInput) {
                priceInput.addEventListener('input', function() {
                    let v = this.value;

                    // loại bỏ ký tự không phải số hoặc dấu chấm
                    v = v.replace(/[^0-9.]/g, '');

                    // giữ chỉ dấu chấm đầu tiên, phần nguyên tối đa 8 chữ số, phần thập phân tối đa 2 chữ số
                    const firstDot = v.indexOf('.');
                    if (firstDot !== -1) {
                        let intPart = v.slice(0, firstDot).slice(0, 8);
                        let decPart = v.slice(firstDot + 1).replace(/\./g, '').slice(0, 2);
                        v = intPart + '.' + decPart;
                    } else {
                        v = v.slice(0, 8);
                    }

                    // nếu bắt đầu bằng nhiều số 0, giữ 0 hoặc loại bỏ tiền tố 0 không cần thiết (tuỳ bạn)
                    // loại bỏ dấu chấm ở cuối nếu vượt maxlength
                    if (v.length > 11) v = v.slice(0, 11);

                    this.value = v;
                });

                priceInput.addEventListener('paste', function(e) {
                    const paste = (e.clipboardData || window.clipboardData).getData('text');
                    if (!/^\d{1,8}(\.\d{1,2})?$/.test(paste)) {
                        e.preventDefault();
                    }
                });
            }

            const nameInput = document.getElementById('name');
            if (nameInput) {
                nameInput.addEventListener('input', () => {
                    nameInput.value = nameInput.value.slice(0, 100);
                });
            }
            const descInput = document.getElementById('description');
            if (descInput) {
                descInput.addEventListener('input', () => {
                    descInput.value = descInput.value.slice(0, 1000);
                });
            }
        });
    </script>
@endsection
