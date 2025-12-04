@extends('admin.layouts.main')

@section('noidung')
<div class="content-wrapper">
    <!-- Content Header -->
    <section class="content-header">
        <div class="container-fluid">
            <h1>{{ $title ?? 'Chỉnh sửa Voucher' }}</h1>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card shadow-sm">
                        <div class="card-header bg-primary text-white">
                            <h3 class="card-title mb-0">{{ $title ?? 'Chỉnh sửa Voucher' }}</h3>
                        </div>

                        <div class="card-body">
                            <form action="{{ route('admin.vouchers.voucher.update', $voucher->id) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <!-- Chọn User (có thể chọn nhiều) -->
                                <div class="form-group">
                                    <label for="user_ids">
                                        Đối tượng áp dụng <span class="text-danger">*</span>
                                        <input type="checkbox" id="apply_all_checkbox" class="ml-2" style="width: 18px; height: 18px; vertical-align: middle;" {{ $isForAllUsers ? 'checked' : '' }}>
                                        <span class="ml-1" style="font-weight: normal; font-size: 14px;">Tất cả mọi người</span>
                                    </label>
                                    
                                    <select id="user_ids" name="user_ids[]" multiple required
                                        class="form-control @error('user_ids') is-invalid @enderror">
                                        @foreach ($users as $user)
                                            <option value="{{ $user->id }}"
                                                {{ in_array($user->id, $selectedUserIds) ? 'selected' : '' }}
                                                data-phone="{{ $user->phone ?? '' }}">
                                                {{ $user->name }} ({{ $user->phone ?: 'Chưa có SĐT' }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="form-text text-muted"></small>
                                    @error('user_ids')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- Chọn Tier -->
                                <div class="form-group">
                                    <label for="tier_id">Tier (tuỳ chọn)</label>
                                    <select id="tier_id" name="tier_id"
                                        class="form-control @error('tier_id') is-invalid @enderror">
                                        <option value="">-- Không chọn --</option>
                                        @foreach ($tiers as $tier)
                                            <option value="{{ $tier->id }}"
                                                {{ old('tier_id', $voucher->tier_id) == $tier->id ? 'selected' : '' }}>
                                                {{ $tier->name }} - {{ $tier->discount_percent }}%
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('tier_id')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

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
                                            Giảm theo %
                                        </option>
                                        <option value="fixed"
                                            {{ old('discount_type', $voucher->discount_type) == 'fixed' ? 'selected' : '' }}>
                                            Giảm theo tiền
                                        </option>
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

                                <!-- Giá trị giảm tối đa (chỉ cho giảm theo %) -->
                                <div class="form-group" id="max_discount_container" style="display: none;">
                                    <label for="order_value_allowed">Giá trị giảm tối đa</label>
                                    <input type="number" step="0.01" id="order_value_allowed"
                                        name="order_value_allowed"
                                        class="form-control @error('order_value_allowed') is-invalid @enderror"
                                        placeholder="Nhập giá trị giảm tối đa (VNĐ)"
                                        value="{{ old('order_value_allowed', $voucher->order_value_allowed) }}">
                                    @error('order_value_allowed')
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
                                <div class="form-group mt-3">
                                    <button type="submit" class="btn btn-primary">Cập nhật</button>
                                    <a href="{{ route('admin.vouchers.voucher.index') }}" class="btn btn-secondary">Quay lại</a>
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
        
        // Khởi tạo Select2 cho dropdown người dùng (multiple select)
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

        // Xử lý khi checkbox được tích/bỏ tích
        $('#apply_all_checkbox').on('change', function() {
            if ($(this).is(':checked')) {
                // Chọn tất cả user
                $('#user_ids').val(allUserIds).trigger('change');
            } else {
                // Khi bỏ tích, chỉ cập nhật trạng thái (không bỏ chọn tất cả để tránh vi phạm validation)
                // Người dùng sẽ tự chọn user cụ thể từ Select2
                // Checkbox sẽ tự động bỏ tích khi chọn user cụ thể
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


        $('#user_ids').on('select2:unselect', function(e) {
            updateApplyAllInfo();
        });

        function updateApplyAllInfo() {
            var selectedCount = $('#user_ids').val() ? $('#user_ids').val().length : 0;
            var totalUsers = {{ $totalUsers }};
            
            if (selectedCount === 0 || selectedCount === totalUsers) {
                $('#apply_all_users_info').show();
            } else {
                $('#apply_all_users_info').hide();
            }
        }

        updateApplyAllInfo();

        const discountTypeSelect = document.getElementById('discount_type');
        const maxDiscountContainer = document.getElementById('max_discount_container');
        const orderValueAllowedInput = document.getElementById('order_value_allowed');

        function toggleMaxDiscountField() {
            if (discountTypeSelect.value === 'percent') {
                maxDiscountContainer.style.display = 'block';
            } else {
                maxDiscountContainer.style.display = 'none';
            }
        }

        discountTypeSelect.addEventListener('change', toggleMaxDiscountField);
        
        toggleMaxDiscountField();
    });
</script>
@endpush
@endsection
