@extends('admin.layouts.main')

@section('noidung')
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <h1>Thêm voucher mới</h1>
            </div>
        </section>

        <section class="content">
            <div class="container-fluid">
                <div class="row justify-content-center">
                    <div class="col-lg-8 col-md-10">
                        <div class="card shadow-sm">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0">Thông tin cơ bản</h5>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('admin.vouchers.voucher.store') }}" method="POST">
                                    @csrf

                                    {{-- Người dùng & Tier --}}
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label for="user_id" class="form-label">Người dùng (tuỳ chọn)</label>
                                            <select name="user_id" id="user_id"
                                                class="form-control @error('user_id') is-invalid @enderror">
                                                <option value="">-- Không gán cho user nào --</option>
                                                @foreach ($users as $user)
                                                    <option value="{{ $user->id }}"
                                                        {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                                        {{ $user->name }} ({{ $user->email }})
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('user_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-6">
                                            <label for="tier_id" class="form-label">Tier (tuỳ chọn)</label>
                                            <select name="tier_id" id="tier_id"
                                                class="form-control @error('tier_id') is-invalid @enderror">
                                                <option value="">-- Chọn tier --</option>
                                                @foreach ($tiers as $tier)
                                                    <option value="{{ $tier->id }}"
                                                        {{ old('tier_id') == $tier->id ? 'selected' : '' }}>
                                                        {{ $tier->name }} - {{ $tier->discount_percent }}%
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('tier_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- Mã voucher --}}
                                    <div class="mb-3">
                                        <label for="code" class="form-label">Mã voucher <span
                                                class="text-danger">*</span></label>
                                        <input type="text" name="code" id="code"
                                            class="form-control @error('code') is-invalid @enderror"
                                            value="{{ old('code') }}" placeholder="Nhập mã voucher">
                                        @error('code')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- Loại & Giá trị giảm --}}
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label for="discount_type" class="form-label">Loại giảm giá <span
                                                    class="text-danger">*</span></label>
                                            <select name="discount_type" id="discount_type"
                                                class="form-control @error('discount_type') is-invalid @enderror">
                                                <option value="">-- Chọn loại giảm giá --</option>
                                                <option value="percent"
                                                    {{ old('discount_type') == 'percent' ? 'selected' : '' }}>Giảm theo %
                                                </option>
                                                <option value="fixed"
                                                    {{ old('discount_type') == 'fixed' ? 'selected' : '' }}>Giảm theo tiền
                                                </option>
                                            </select>
                                            @error('discount_type')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-6">
                                            <label for="discount_value" class="form-label">Giá trị giảm <span
                                                    class="text-danger">*</span></label>
                                            <input type="number" step="0.01" name="discount_value" id="discount_value"
                                                class="form-control @error('discount_value') is-invalid @enderror"
                                                value="{{ old('discount_value') }}" placeholder="Nhập giá trị giảm">
                                            @error('discount_value')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- Giới hạn & đơn hàng --}}
                                    <div class="row mb-3">
                                        <div class="col-md-4">
                                            <label for="max_uses" class="form-label">Số lần sử dụng tối đa</label>
                                            <input type="number" name="max_uses" id="max_uses"
                                                class="form-control @error('max_uses') is-invalid @enderror"
                                                value="{{ old('max_uses') }}">
                                            @error('max_uses')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-4">
                                            <label for="min_order_value" class="form-label">Giá trị đơn hàng tối
                                                thiểu</label>
                                            <input type="number" step="0.01" name="min_order_value" id="min_order_value"
                                                class="form-control @error('min_order_value') is-invalid @enderror"
                                                value="{{ old('min_order_value') }}">
                                            @error('min_order_value')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-4">
                                            <label for="order_value_allowed" class="form-label">Giá trị đơn hàng áp
                                                dụng</label>
                                            <input type="number" step="0.01" name="order_value_allowed"
                                                id="order_value_allowed"
                                                class="form-control @error('order_value_allowed') is-invalid @enderror"
                                                value="{{ old('order_value_allowed') }}">
                                            @error('order_value_allowed')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- Ngày bắt đầu & kết thúc --}}
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label for="start_date" class="form-label">Ngày bắt đầu <span
                                                    class="text-danger">*</span></label>
                                            <input type="date" name="start_date" id="start_date"
                                                class="form-control @error('start_date') is-invalid @enderror"
                                                value="{{ old('start_date') }}">
                                            @error('start_date')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label for="end_date" class="form-label">Ngày kết thúc <span
                                                    class="text-danger">*</span></label>
                                            <input type="date" name="end_date" id="end_date"
                                                class="form-control @error('end_date') is-invalid @enderror"
                                                value="{{ old('end_date') }}">
                                            @error('end_date')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- Trạng thái --}}
                                    <div class="mb-3">
                                        <label for="status" class="form-label">Trạng thái <span
                                                class="text-danger">*</span></label>
                                        <select name="status" id="status"
                                            class="form-control @error('status') is-invalid @enderror">
                                            <option value="">-- Chọn trạng thái --</option>
                                            <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Hoạt
                                                động</option>
                                            <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>
                                                Tạm dừng</option>
                                        </select>
                                        @error('status')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="d-flex justify-content-between mt-4">
                                        <button type="submit" class="btn btn-success">Tạo voucher</button>
                                        <a href="{{ route('admin.vouchers.voucher.index') }}"
                                            class="btn btn-secondary">Quay lại</a>
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
