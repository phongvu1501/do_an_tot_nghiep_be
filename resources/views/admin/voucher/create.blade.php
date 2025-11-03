@extends('admin.layouts.main')

@section('noidung')
<div class="content-wrapper">
    <!-- Content Header -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Tạo mới voucher</h1>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-8 offset-md-2">
                    <div class="card">
                        {{-- <div class="card-header bg-primary text-white">
                            <h3 class="card-title mb-0">Tạo mới voucher</h3>
                        </div> --}}
                        <div class="card-body">
                            <form action="{{ route('admin.voucher.store') }}" method="POST">
                                @csrf

                                {{-- Mã voucher --}}
                                <div class="form-group">
                                    <label for="code">Mã voucher <span class="text-danger">*</span></label>
                                    <input type="text" name="code" id="code"
                                        class="form-control @error('code') is-invalid @enderror"
                                        placeholder="Nhập mã voucher" value="{{ old('code') }}">
                                    @error('code')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                {{-- Loại giảm giá --}}
                                <div class="form-group">
                                    <label for="discount_type">Loại giảm giá <span class="text-danger">*</span></label>
                                    <select name="discount_type" id="discount_type"
                                        class="form-control @error('discount_type') is-invalid @enderror">
                                        <option value="">-- Chọn loại giảm giá --</option>
                                        <option value="percent" {{ old('discount_type') == 'percent' ? 'selected' : '' }}>Giảm theo phần trăm (%)</option>
                                        <option value="fixed" {{ old('discount_type') == 'fixed' ? 'selected' : '' }}>Giảm theo số tiền cố định</option>
                                    </select>
                                    @error('discount_type')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                {{-- Giá trị giảm --}}
                                <div class="form-group">
                                    <label for="discount_value">Giá trị giảm <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" name="discount_value" id="discount_value"
                                        class="form-control @error('discount_value') is-invalid @enderror"
                                        placeholder="Nhập giá trị giảm" value="{{ old('discount_value') }}">
                                    @error('discount_value')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                {{-- Số lần sử dụng tối đa --}}
                                <div class="form-group">
                                    <label for="max_uses">Số lần sử dụng tối đa</label>
                                    <input type="number" name="max_uses" id="max_uses"
                                        class="form-control @error('max_uses') is-invalid @enderror"
                                        placeholder="Nhập số lần sử dụng tối đa" value="{{ old('max_uses') }}">
                                    @error('max_uses')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                {{-- Giá trị đơn hàng tối thiểu --}}
                                <div class="form-group">
                                    <label for="min_order_value">Giá trị đơn hàng tối thiểu</label>
                                    <input type="number" step="0.01" name="min_order_value" id="min_order_value"
                                        class="form-control @error('min_order_value') is-invalid @enderror"
                                        placeholder="Nhập giá trị tối thiểu" value="{{ old('min_order_value') }}">
                                    @error('min_order_value')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                  {{-- Giá trị đơn hàng áp dụng --}}
                                <div class="form-group">
                                    <label for="order_value_allowed">Giá trị đơn hàng áp dụng</label>
                                    <input type="number" step="0.01" name="order_value_allowed" id="order_value_allowed"
                                        class="form-control @error('order_value_allowed') is-invalid @enderror"
                                        placeholder="Nhập giá trị áp dụng" value="{{ old('order_value_allowed') }}">
                                    @error('order_value_allowed')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                {{-- Ngày bắt đầu --}}
                                <div class="form-group">
                                    <label for="start_date">Ngày bắt đầu <span class="text-danger">*</span></label>
                                    <input type="date" name="start_date" id="start_date"
                                        class="form-control @error('start_date') is-invalid @enderror"
                                        value="{{ old('start_date') }}">
                                    @error('start_date')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                {{-- Ngày kết thúc --}}
                                <div class="form-group">
                                    <label for="end_date">Ngày kết thúc <span class="text-danger">*</span></label>
                                    <input type="date" name="end_date" id="end_date"
                                        class="form-control @error('end_date') is-invalid @enderror"
                                        value="{{ old('end_date') }}">
                                    @error('end_date')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                {{-- Trạng thái --}}
                                <div class="form-group">
                                    <label for="status">Trạng thái <span class="text-danger">*</span></label>
                                    <select name="status" id="status"
                                        class="form-control @error('status') is-invalid @enderror">
                                        <option value="">-- Chọn trạng thái --</option>
                                        <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Hoạt động</option>
                                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Tạm dừng</option>
                                    </select>
                                    @error('status')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                {{-- Nút hành động --}}
                                <div class="form-group mt-4">
                                    <button type="submit" class="btn btn-success">Tạo voucher</button>
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
