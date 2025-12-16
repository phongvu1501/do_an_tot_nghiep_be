@extends('admin.layouts.main')

@section('noidung')
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Tạo mới tiêu chí Tier</h1>
                    </div>
                </div>
            </div>
        </section>

        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-8 offset-md-2">
                        <div class="card">
                            <div class="card-body">
                                <form action="{{ route('admin.tiers.store') }}" method="POST">
                                    @csrf

                                    <!-- Name -->
                                    <div class="form-group">
                                        <label for="name">Tên Tier <span class="text-danger">*</span></label>
                                        <input type="text" name="name" id="name"
                                            class="form-control @error('name') is-invalid @enderror"
                                            placeholder="Nhập tên Tier" value="{{ old('name') }}">
                                        @error('name')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- Points required -->
                                    <div class="form-group">
                                        <label for="points_required">Điểm yêu cầu <span class="text-danger">*</span></label>
                                        <input type="number" name="points_required" id="points_required"
                                            class="form-control @error('points_required') is-invalid @enderror"
                                            value="{{ old('points_required') }}">
                                        @error('points_required')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- Discount percent -->
                                    <div class="form-group">
                                        <label for="discount_percent">Phần trăm giảm (%) <span
                                                class="text-danger">*</span></label>
                                        <input type="number" step="0.01" name="discount_percent" id="discount_percent"
                                            class="form-control @error('discount_percent') is-invalid @enderror"
                                            value="{{ old('discount_percent') }}">
                                        @error('discount_percent')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- Max discount value -->
                                    <div class="form-group">
                                        <label for="max_discount_value">Giá trị giảm tối đa <span
                                                class="text-danger">*</span></label>
                                        <input type="number" step="0.01" name="max_discount_value"
                                            id="max_discount_value"
                                            class="form-control @error('max_discount_value') is-invalid @enderror"
                                            value="{{ old('max_discount_value') }}">
                                        @error('max_discount_value')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- Min order value -->
                                    <div class="form-group">
                                        <label for="min_order_value">Giá trị đơn hàng tối thiểu <span
                                                class="text-danger">*</span></label>
                                        <input type="number" step="0.01" name="min_order_value" id="min_order_value"
                                            class="form-control @error('min_order_value') is-invalid @enderror"
                                            value="{{ old('min_order_value') }}">
                                        @error('min_order_value')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <!-- Order value allowed -->
                                    {{-- <div class="form-group">
                                        <label for="order_value_allowed">Giá trị đơn hàng áp dụng</label>
                                        <input type="number" step="0.01" name="order_value_allowed"
                                            id="order_value_allowed"
                                            class="form-control @error('order_value_allowed') is-invalid @enderror"
                                            value="{{ old('order_value_allowed') }}">
                                        @error('order_value_allowed')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div> --}}


                                    <!-- Status -->
                                    <div class="form-group">
                                        <label for="is_active">Trạng thái <span class="text-danger">*</span></label>
                                        <select name="is_active" id="is_active"
                                            class="form-control @error('is_active') is-invalid @enderror">
                                            <option value="1" {{ old('is_active') == 1 ? 'selected' : '' }}>Hoạt động
                                            </option>
                                            <option value="0" {{ old('is_active') == 0 ? 'selected' : '' }}>Tạm dừng
                                            </option>
                                        </select>
                                        @error('is_active')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- Buttons -->
                                    <div class="form-group mt-4">
                                        <button type="submit" class="btn btn-success">Tạo Tier</button>
                                        <a href="{{ route('admin.tiers.index') }}" class="btn btn-secondary">Quay lại</a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
