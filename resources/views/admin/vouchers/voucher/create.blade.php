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
                                            <label for="user_ids" class="form-label">
                                                Đối tượng áp dụng <span class="text-danger">*</span>
                                                <input type="checkbox" id="apply_all_checkbox" class="ml-2" style="width: 18px; height: 18px; vertical-align: middle;">
                                                <span class="ml-1" style="font-weight: normal; font-size: 14px;">Tất cả mọi người</span>
                                            </label>
                                            
                                            <select name="user_ids[]" id="user_ids" multiple required
                                                class="form-control @error('user_ids') is-invalid @enderror">
                                                @foreach ($users as $user)
                                                    <option value="{{ $user->id }}"
                                                        {{ in_array($user->id, old('user_ids', [])) ? 'selected' : '' }}
                                                        data-phone="{{ $user->phone ?? '' }}">
                                                        {{ $user->name }} ({{ $user->phone ?: 'Chưa có SĐT' }})
                                                    </option>
                                                @endforeach
                                            </select>
                                            <small class="form-text text-muted"></small>
                                            @error('user_ids')
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
                                        <label for="code" class="form-label">Mã voucher</label>
                                        <input type="text" name="code" id="code"
                                            class="form-control @error('code') is-invalid @enderror"
                                            value="{{ old('code') }}">
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
                                        <div class="col-md-4" id="max_discount_container" style="display: none;">
                                            <label for="order_value_allowed" class="form-label">Giá trị giảm tối đa</label>
                                            <input type="number" step="0.01" name="order_value_allowed"
                                                id="order_value_allowed"
                                                class="form-control @error('order_value_allowed') is-invalid @enderror"
                                                value="{{ old('order_value_allowed') }}"
                                                placeholder="Nhập giá trị giảm tối đa (VNĐ)">
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

    <!-- Select2 CSS -->
    <link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}" rel="stylesheet" />
    
    <style>
        /* Giới hạn chiều cao dropdown Select2 và cho phép cuộn */
        .select2-container--bootstrap4 .select2-results__options {
            max-height: 300px;
            overflow-y: auto;
        }
        
        .select2-container--bootstrap4 .select2-results__option {
            padding: 8px 12px;
        }
    </style>

    @push('scripts')
    <!-- Select2 JS -->
    <script src="{{ asset('assets/plugins/select2/js/select2.full.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            var totalUsers = {{ $users->count() }};
            var allUserIds = [@foreach($users as $user){{ $user->id }}{{ !$loop->last ? ',' : '' }}@endforeach];
            
            $('#user_ids').select2({
                theme: 'bootstrap4',
                placeholder: 'Chọn đối tượng áp dụng',
                allowClear: false, // Không cho phép bỏ trống
                language: {
                    noResults: function() {
                        return "Không tìm thấy người dùng";
                    },
                    searching: function() {
                        return "Đang tìm kiếm...";
                    }
                },
                matcher: function(params, data) {
                    if (!params.term || $.trim(params.term) === '') {
                        return data;
                    }
                    
                    var term = params.term.toLowerCase().trim();
                    var text = data.text.toLowerCase();
                    
                    var phone = '';
                    if (data.element) {
                        phone = $(data.element).data('phone') || '';
                        if (!phone) {
                            var phoneMatch = text.match(/\(([^)]+)\)/);
                            if (phoneMatch && phoneMatch[1]) {
                                phone = phoneMatch[1].trim();
                            }
                        }
                    }
                    
                    phone = phone.replace('chưa có sđt', '').trim();
                    
                    var namePart = text.split('(')[0].trim();
                    var nameMatch = namePart.indexOf(term) > -1;
                    
                    var phoneMatch = phone && phone.indexOf(term) > -1;
                    
                    var fullTextMatch = text.indexOf(term) > -1;
                    
                    if (nameMatch || phoneMatch || fullTextMatch) {
                        return data;
                    }
                    
                    return null;
                }
            });

            // Hàm kiểm tra và cập nhật trạng thái checkbox
            function updateCheckboxState() {
                var currentValues = $('#user_ids').val() || [];
                var numericValues = currentValues.filter(function(id) {
                    return isNumeric(id);
                });
                
                // Kiểm tra xem có phải tất cả user được chọn không
                var allSelected = numericValues.length === totalUsers && 
                                 numericValues.length === allUserIds.length &&
                                 allUserIds.every(function(id) {
                                     return numericValues.indexOf(String(id)) > -1;
                                 });
                
                $('#apply_all_checkbox').prop('checked', allSelected);
            }

            $('#apply_all_checkbox').on('change', function() {
                if ($(this).is(':checked')) {
                    // alll user
                    $('#user_ids').val(allUserIds).trigger('change');
                } else {
                    // bo tich
                }
            });

            $('#user_ids').on('select2:select', function(e) {
                updateCheckboxState();
            });

            $('#user_ids').on('select2:unselect', function(e) {
                updateCheckboxState();
            });

            $('#user_ids').on('change', function() {
                updateCheckboxState();
            });
            
            updateCheckboxState();
            
            $('form').on('submit', function(e) {
                var selectedValues = $('#user_ids').val() || [];
                if (selectedValues.length === 0) {
                    e.preventDefault();
                    alert('Vui lòng chọn đối tượng áp dụng!');
                    $('#user_ids').focus();
                    return false;
                }
            });

            const discountTypeSelect = document.getElementById('discount_type');
            const maxDiscountContainer = document.getElementById('max_discount_container');
            const orderValueAllowedInput = document.getElementById('order_value_allowed');

            function toggleMaxDiscountField() {
                if (discountTypeSelect.value === 'percent') {
                    maxDiscountContainer.style.display = 'block';
                } else {
                    maxDiscountContainer.style.display = 'none';
                    orderValueAllowedInput.value = '';
                }
            }

            discountTypeSelect.addEventListener('change', toggleMaxDiscountField);
            
            toggleMaxDiscountField();
        });
    </script>
    @endpush
@endsection
