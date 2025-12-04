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
                                    <label for="type">Loại bàn</label>
                                    <select class="form-control @error('type') is-invalid @enderror" id="type" name="type">
                                        <option value="">-- Chọn loại bàn --</option>
                                        <option value="normal" {{ old('type') == 'normal' ? 'selected' : '' }}>Bàn thường</option>
                                        <option value="vip" {{ old('type') == 'vip' ? 'selected' : '' }}>Phòng VIP</option>
                                    </select>
                                    @error('type')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="limit_number">Số lượng người tối đa</label>
                                    <input type="number" class="form-control @error('limit_number') is-invalid @enderror"
                                        id="limit_number" name="limit_number" placeholder="Nhập số lượng người"
                                        value="{{ old('limit_number') }}" readonly>
                                    @error('limit_number')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
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

            // Set initial value if type is already selected
            if (typeSelect.value) {
                typeSelect.dispatchEvent(new Event('change'));
            }
        });
    </script>
@endsection
