@extends('admin.layouts.main')

@section('noidung')
    <div class="content-wrapper">
        <!-- Content Header -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>{{ $title }}</h1>
                    </div>
                </div>
            </div>
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">{{ $title ?? 'Chỉnh sửa Voucher' }}</h3>
                            </div>

                            <div class="card-body">
                                <form action="{{ route('admin.voucher.update', $voucher->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')

                                    <!-- Mã voucher -->
                                    <div class="form-group">
                                        <label for="code">Mã voucher</label>
                                        <input type="text" id="code" name="code"
                                            class="form-control @error('code') is-invalid @enderror"
                                            placeholder="Nhập mã voucher" value="{{ old('code', $voucher->code) }}">
                                        @error('code')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- Loại giảm giá -->
                                    <div class="form-group">
                                        <label for="discount_type">Loại giảm giá</label>
                                        <select id="discount_type" name="discount_type"
                                            class="form-control @error('discount_type') is-invalid @enderror">
                                            <option value="">-- Chọn loại giảm giá --</option>
                                            <option value="percent"
                                                {{ old('discount_type', $voucher->discount_type) == 'percent' ? 'selected' : '' }}>
                                                Giảm theo %</option>
                                            <option value="amount"
                                                {{ old('discount_type', $voucher->discount_type) == 'amount' ? 'selected' : '' }}>
                                                Giảm theo tiền</option>
                                        </select>
                                        @error('discount_type')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- Giá trị giảm -->
                                    <div class="form-group">
                                        <label for="discount_value">Giá trị giảm</label>
                                        <input type="number" step="0.01" id="discount_value" name="discount_value"
                                            class="form-control @error('discount_value') is-invalid @enderror"
                                            placeholder="Nhập giá trị giảm"
                                            value="{{ old('discount_value', $voucher->discount_value) }}">
                                        @error('discount_value')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- Số lần sử dụng tối đa -->
                                    <div class="form-group">
                                        <label for="max_uses">Số lần sử dụng tối đa</label>
                                        <input type="number" id="max_uses" name="max_uses"
                                            class="form-control @error('max_uses') is-invalid @enderror"
                                            placeholder="Nhập số lần sử dụng"
                                            value="{{ old('max_uses', $voucher->max_uses) }}">
                                        @error('max_uses')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- Giá trị đơn hàng tối thiểu -->
                                    <div class="form-group">
                                        <label for="min_order_value">Giá trị đơn hàng tối thiểu</label>
                                        <input type="number" step="0.01" id="min_order_value" name="min_order_value"
                                            class="form-control @error('min_order_value') is-invalid @enderror"
                                            placeholder="Nhập giá trị tối thiểu"
                                            value="{{ old('min_order_value', $voucher->min_order_value) }}">
                                        @error('min_order_value')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- Ngày bắt đầu -->
                                    <div class="form-group">
                                        <label for="start_date">Ngày bắt đầu</label>
                                        <input type="date" id="start_date" name="start_date"
                                            class="form-control @error('start_date') is-invalid @enderror"
                                            value="{{ old('start_date', \Carbon\Carbon::parse($voucher->start_date)->format('Y-m-d')) }}">
                                        @error('start_date')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- Ngày kết thúc -->
                                    <div class="form-group">
                                        <label for="end_date">Ngày kết thúc</label>
                                        <input type="date" id="end_date" name="end_date"
                                            class="form-control @error('end_date') is-invalid @enderror"
                                            value="{{ old('end_date', \Carbon\Carbon::parse($voucher->end_date)->format('Y-m-d')) }}">
                                        @error('end_date')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- Trạng thái -->
                                    <div class="form-group">
                                        <label for="status">Trạng thái</label>
                                        <select id="status" name="status"
                                            class="form-control @error('status') is-invalid @enderror">
                                            <option value="">-- Chọn trạng thái --</option>
                                            <option value="active"
                                                {{ old('status', $voucher->status) == 'active' ? 'selected' : '' }}>Hoạt
                                                động</option>
                                            <option value="inactive"
                                                {{ old('status', $voucher->status) == 'inactive' ? 'selected' : '' }}>Tạm
                                                dừng</option>
                                        </select>
                                        @error('status')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- Nút -->
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary">Cập nhật</button>
                                        <a href="{{ route('admin.voucher.index') }}" class="btn btn-secondary">Quay lại</a>
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
