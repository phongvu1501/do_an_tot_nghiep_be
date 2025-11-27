@extends('admin.layouts.main')

@section('noidung')
    <div class="content-wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h3 class="mb-0">Tạo đơn đặt bàn mới</h3>
                                <a href="{{ route('admin.datBan.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Quay lại
                                </a>
                            </div>
                        </div>
                        <div class="card-body">

                            @if(session('error'))
                                <div class="alert alert-danger alert-dismissible fade show">
                                    <i class="fas fa-exclamation-triangle"></i> {{ session('error') }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif

                            <form action="{{ route('admin.datBan.store') }}" method="POST" id="createReservationForm">
                                @csrf
                            <div class="row">
                                <!-- Thông tin khách hàng -->
                                <div class="col-md-6">
                                    <h5 class="mb-3"><i class="fas fa-user"></i> Thông tin khách hàng</h5>
                                    
                                    <div class="form-group">
                                        <label for="customer_phone">Số điện thoại <span class="text-danger">*</span></label>
                                        <input type="tel" 
                                               class="form-control @error('customer_phone') is-invalid @enderror" 
                                               id="customer_phone" 
                                               name="customer_phone" 
                                               value="{{ old('customer_phone') }}"
                                               placeholder="Nhập số điện thoại"
                                               required>
                                        <small class="form-text text-muted" id="phoneHelp">Nhập số điện thoại để tìm thông tin khách hàng</small>
                                        <div id="phoneLoading" class="spinner-border spinner-border-sm d-none" role="status">
                                            <span class="sr-only">Loading...</span>
                                        </div>
                                        @error('customer_phone')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="customer_name">Tên khách hàng <span class="text-danger">*</span></label>
                                        <input type="text" 
                                               class="form-control @error('customer_name') is-invalid @enderror" 
                                               id="customer_name" 
                                               name="customer_name" 
                                               value="{{ old('customer_name') }}"
                                               placeholder="Nhập tên khách hàng"
                                               required>
                                        @error('customer_name')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="form-group" id="userEmailGroup" style="display: none;">
                                        <label for="customer_email">Email</label>
                                        <input type="email" 
                                               class="form-control" 
                                               id="customer_email" 
                                               name="customer_email" 
                                               readonly>
                                        <small class="form-text text-muted">Email từ tài khoản</small>
                                    </div>

                                    <input type="hidden" id="user_id" name="user_id" value="{{ old('user_id') }}">
                                    <div id="existingReservationAlert" class="alert alert-warning" style="display: none;">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        <strong>Cảnh báo:</strong> <span id="existingReservationMessage"></span>
                                    </div>

                                    <div class="form-group">
                                        <label for="num_people">Số lượng người <span class="text-danger">*</span></label>
                                        <input type="number" 
                                               class="form-control @error('num_people') is-invalid @enderror" 
                                               id="num_people" 
                                               name="num_people" 
                                               value="{{ old('num_people', 1) }}"
                                               min="1"
                                               placeholder="Nhập số người"
                                               required>
                                        @error('num_people')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="note">Ghi chú</label>
                                        <textarea class="form-control @error('note') is-invalid @enderror" 
                                                  id="note" 
                                                  name="note" 
                                                  rows="3"
                                                  placeholder="Ghi chú thêm (nếu có)">{{ old('note') }}</textarea>
                                        @error('note')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Thông tin đặt bàn -->
                                <div class="col-md-6">
                                    <h5 class="mb-3"><i class="fas fa-calendar-alt"></i> Thông tin đặt bàn</h5>

                                    <div class="form-group">
                                        <label for="reservation_date">Ngày đặt bàn <span class="text-danger">*</span></label>
                                        <input type="date" 
                                               class="form-control @error('reservation_date') is-invalid @enderror" 
                                               id="reservation_date" 
                                               name="reservation_date" 
                                               value="{{ old('reservation_date', date('Y-m-d')) }}"
                                               min="{{ date('Y-m-d') }}"
                                               onchange="updateAvailableTables(); checkExistingReservation();"
                                               required>
                                        @error('reservation_date')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="shift">Ca <span class="text-danger">*</span></label>
                                        <select class="form-control @error('shift') is-invalid @enderror" 
                                                id="shift" 
                                                name="shift"
                                                onchange="updateAvailableTables(); checkExistingReservation();"
                                                required>
                                            <option value="">-- Chọn ca --</option>
                                            <option value="morning" {{ old('shift') == 'morning' ? 'selected' : '' }}>Sáng (6-10h)</option>
                                            <option value="afternoon" {{ old('shift') == 'afternoon' ? 'selected' : '' }}>Trưa (10-14h)</option>
                                            <option value="evening" {{ old('shift') == 'evening' ? 'selected' : '' }}>Chiều (14-18h)</option>
                                            <option value="night" {{ old('shift') == 'night' ? 'selected' : '' }}>Tối (18-22h)</option>
                                        </select>
                                        @error('shift')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label class="font-weight-bold">Chọn bàn <span class="text-danger">*</span></label>
                                        <div id="errorMessage" class="alert alert-danger" style="display: none;">
                                            <i class="fas fa-exclamation-triangle"></i> <strong>Vui lòng chọn ít nhất 1 bàn!</strong>
                                        </div>
                                        <div class="border p-3 bg-light" style="max-height: 300px; overflow-y: auto;">
                                            <div id="tablesContainer">
                                                @foreach($allTables as $table)
                                                    <div class="custom-control custom-checkbox mb-2">
                                                        <input 
                                                            type="checkbox" 
                                                            class="custom-control-input table-checkbox" 
                                                            id="table{{ $table->id }}" 
                                                            name="table_ids[]" 
                                                            value="{{ $table->id }}"
                                                            {{ (is_array(old('table_ids')) && in_array($table->id, old('table_ids'))) ? 'checked' : '' }}
                                                        >
                                                        <label class="custom-control-label" for="table{{ $table->id }}">
                                                            {{ $table->name }} 
                                                            <span class="badge badge-secondary badge-sm">Rỗi</span>
                                                        </label>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                       
                                        @error('table_ids')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                </div>
                                <div class="mt-3">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Tạo đơn đặt bàn
                                    </button>
                                    <a href="{{ route('admin.datBan.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-times"></i> Hủy
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
let currentUserId = null;
let hasExistingReservation = false;

// Tìm user theo số điện thoại
document.getElementById('customer_phone').addEventListener('blur', function() {
    const phone = this.value.trim();
    const phoneHelp = document.getElementById('phoneHelp');
    const phoneLoading = document.getElementById('phoneLoading');
    const customerName = document.getElementById('customer_name');
    const customerEmail = document.getElementById('customer_email');
    const userEmailGroup = document.getElementById('userEmailGroup');
    const userIdInput = document.getElementById('user_id');
    const existingReservationAlert = document.getElementById('existingReservationAlert');
    
    if (!phone || phone.length < 9) {
        return;
    }
    
    phoneLoading.classList.remove('d-none');
    phoneHelp.textContent = 'Đang tìm kiếm...';
    
    fetch(`/admin/dat-ban/check-user-by-phone?phone=${encodeURIComponent(phone)}`)
        .then(response => response.json())
        .then(data => {
            phoneLoading.classList.add('d-none');
            
            if (data.success && data.user) {
                // Tìm thấy user
                currentUserId = data.user.id;
                userIdInput.value = data.user.id;
                customerName.value = data.user.name;
                customerName.readOnly = true;
                customerName.classList.add('bg-light');
                customerEmail.value = data.user.email || '';
                userEmailGroup.style.display = data.user.email ? 'block' : 'none';
                phoneHelp.textContent = ''; // Xóa text "Đang tìm kiếm..."
                
                // Kiểm tra đặt bàn trùng nếu đã chọn ngày và ca
                checkExistingReservation();
            } else {
                // Không tìm thấy user
                currentUserId = null;
                userIdInput.value = '';
                customerName.value = '';
                customerName.readOnly = false;
                customerName.classList.remove('bg-light');
                customerEmail.value = '';
                userEmailGroup.style.display = 'none';
                phoneHelp.innerHTML = '<span class="text-info">Khách hàng chưa có tài khoản!</span>';
                existingReservationAlert.style.display = 'none';
                hasExistingReservation = false;
            }
        })
        .catch(error => {
            phoneLoading.classList.add('d-none');
            phoneHelp.textContent = 'Lỗi khi tìm kiếm. Vui lòng thử lại.';
            console.error('Error:', error);
        });
});

// Kiểm tra đặt bàn trùng
function checkExistingReservation() {
    const userId = document.getElementById('user_id').value;
    const date = document.getElementById('reservation_date').value;
    const shift = document.getElementById('shift').value;
    const existingReservationAlert = document.getElementById('existingReservationAlert');
    const existingReservationMessage = document.getElementById('existingReservationMessage');
    const submitButton = document.querySelector('button[type="submit"]');
    
    if (!userId || !date || !shift) {
        existingReservationAlert.style.display = 'none';
        hasExistingReservation = false;
        submitButton.disabled = false;
        return;
    }
    
    fetch(`/admin/dat-ban/check-existing-reservation?user_id=${userId}&reservation_date=${date}&shift=${shift}`)
        .then(response => response.json())
        .then(data => {
            if (data.has_reservation) {
                // Đã có đặt bàn
                hasExistingReservation = true;
                existingReservationMessage.textContent = data.message;
                if (data.reservation) {
                    existingReservationMessage.innerHTML = `${data.message}<br><small>Mã đơn: #${data.reservation.id} - Trạng thái: ${data.reservation.status_text}</small>`;
                }
                existingReservationAlert.style.display = 'block';
                submitButton.disabled = true;
                submitButton.classList.add('disabled');
            } else {
                // Chưa có đặt bàn
                hasExistingReservation = false;
                existingReservationAlert.style.display = 'none';
                submitButton.disabled = false;
                submitButton.classList.remove('disabled');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            hasExistingReservation = false;
            existingReservationAlert.style.display = 'none';
            submitButton.disabled = false;
            submitButton.classList.remove('disabled');
        });
}

// Validate form trước khi submit
document.getElementById('createReservationForm').addEventListener('submit', function(e) {
    if (hasExistingReservation) {
        e.preventDefault();
        alert('Không thể tạo đơn đặt bàn vì khách hàng đã có đặt bàn trong thời gian này!');
        return false;
    }
    
    var checkedCount = document.querySelectorAll('.table-checkbox:checked').length;
    var errorMessage = document.getElementById('errorMessage');
    
    if (checkedCount === 0) {
        e.preventDefault();
        errorMessage.style.display = 'block';
        errorMessage.scrollIntoView({ behavior: 'smooth', block: 'center' });
        return false;
    }
    
    errorMessage.style.display = 'none';
    return true;
});

// Cập nhật danh sách bàn trống khi chọn ngày/ca
function updateAvailableTables() {
    var date = document.getElementById('reservation_date').value;
    var shift = document.getElementById('shift').value;
    
    if (!date || !shift) {
        return;
    }
    
    // Gọi AJAX để lấy danh sách bàn trống
    fetch(`/admin/dat-ban/available-tables?date=${date}&shift=${shift}`)
        .then(response => response.json())
        .then(data => {
            var container = document.getElementById('tablesContainer');
            container.innerHTML = '';
            
            data.tables.forEach(table => {
                var isBusy = data.busyTableIds.includes(table.id);
                var div = document.createElement('div');
                div.className = 'custom-control custom-checkbox mb-2';
                
                div.innerHTML = `
                    <input 
                        type="checkbox" 
                        class="custom-control-input table-checkbox" 
                        id="table${table.id}" 
                        name="table_ids[]" 
                        value="${table.id}"
                        ${isBusy ? 'disabled' : ''}
                    >
                    <label class="custom-control-label" for="table${table.id}">
                        ${table.name} 
                        ${isBusy ? '<span class="badge badge-danger badge-sm">Đang bận</span>' : '<span class="badge badge-success badge-sm">Rỗi</span>'}
                    </label>
                `;
                
                container.appendChild(div);
            });
        })
        .catch(error => {
            console.error('Error:', error);
        });
}
</script>
@endpush

