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
                                    <input type="text" name="name" id="name"
                                        value="{{ old('name', $menu->name) }}"
                                        class="form-control @error('name') is-invalid @enderror" maxlength="100" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Danh mục</label>
                                    <select name="category_id" class="form-control" required>
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
                                    <textarea name="description" id="description" class="form-control" maxlength="1000">{{ old('description', $menu->description) }}</textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Giá</label>
                                    <input type="text" name="price" id="price"
                                        class="form-control @error('price') is-invalid @enderror"
                                        value="{{ old('price', $menu->price) }}" maxlength="11" inputmode="decimal"
                                        required>

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
                                    <select name="status" class="form-control">
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
