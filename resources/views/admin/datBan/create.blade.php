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

                                    <div class="form-group">
                                        <label for="customer_phone">Số điện thoại <span class="text-danger">*</span></label>
                                        <input type="tel" 
                                               class="form-control @error('customer_phone') is-invalid @enderror" 
                                               id="customer_phone" 
                                               name="customer_phone" 
                                               value="{{ old('customer_phone') }}"
                                               placeholder="Nhập số điện thoại"
                                               required>
                                        @error('customer_phone')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
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
                                               onchange="updateAvailableTables()"
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
                                                onchange="updateAvailableTables()"
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
// Validate form trước khi submit
document.getElementById('createReservationForm').addEventListener('submit', function(e) {
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

