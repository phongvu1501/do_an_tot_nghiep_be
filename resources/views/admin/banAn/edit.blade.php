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
                                    <label for="type">Loại bàn</label>
                                    <select class="form-control @error('type') is-invalid @enderror" id="type" name="type">
                                        <option value="">-- Chọn loại bàn --</option>
                                        <option value="normal" {{ old('type', $banAn->type) == 'normal' ? 'selected' : '' }}>Bàn thường</option>
                                        <option value="vip" {{ old('type', $banAn->type) == 'vip' ? 'selected' : '' }}>Phòng VIP</option>
                                    </select>
                                    @error('type')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="limit_number">Số lượng người tối đa</label>
                                    <input type="number" class="form-control @error('limit_number') is-invalid @enderror"
                                        id="limit_number" name="limit_number" placeholder="Nhập số lượng người"
                                        value="{{ old('limit_number', $banAn->limit_number) }}" readonly>
                                    @error('limit_number')
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const typeSelect = document.getElementById('type');
            const limitNumberInput = document.getElementById('limit_number');

            typeSelect.addEventListener('change', function() {
                if (this.value === 'vip') {
                    limitNumberInput.value = 30;
                } else if (this.value === 'normal') {
                    limitNumberInput.value = 8;
                } else {
                    limitNumberInput.value = '';
                }
            });

            // Set initial value on page load
            if (typeSelect.value) {
                typeSelect.dispatchEvent(new Event('change'));
            }
        });
    </script>
@endsection
