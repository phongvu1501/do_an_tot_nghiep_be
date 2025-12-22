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

                            @if (session('error'))
                                <div class="alert alert-dismissible fade show shadow-sm mb-3" role="alert"
                                    style="
                                            background-color: #fdecea;
                                            border: 1px solid #f5c2c7;
                                            color: #842029;
                                            border-radius: 10px;
                                            padding: 10px 14px;
                                            font-size: 14px;
                                        ">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-exclamation-circle mr-2" style="color:#d00000;"></i>
                                        <span class="flex-grow-1">
                                            {{ session('error') }}
                                        </span>
                                        <button type="button" class="close ml-2" data-dismiss="alert" aria-label="Close"
                                            style="color:#842029; outline:none;">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                </div>
                            @endif


                            <form action="{{ route('admin.datBan.store') }}" method="POST" id="createReservationForm">
                                @csrf
                                <div class="row">
                                    <!-- Thông tin khách hàng -->
                                    <div class="col-md-6">
                                        <h5 class="mb-3"><i class="fas fa-user"></i> Thông tin khách hàng</h5>

                                        <div class="form-group">
                                            <label for="customer_phone">Số điện thoại <span
                                                    class="text-danger">*</span></label>
                                            <input type="number"
                                                class="form-control @error('customer_phone') is-invalid @enderror"
                                                id="customer_phone" name="customer_phone"
                                                value="{{ old('customer_phone') }}" placeholder="Nhập số điện thoại"
                                                min="0" required>
                                            <small class="form-text text-muted" id="phoneHelp">Nhập số điện thoại để tìm
                                                thông tin khách hàng</small>
                                            <div id="phoneLoading" class="spinner-border spinner-border-sm d-none"
                                                role="status">
                                                <span class="sr-only">Loading...</span>
                                            </div>
                                            @error('customer_phone')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="customer_name">Tên khách hàng <span
                                                    class="text-danger">*</span></label>
                                            <input type="text"
                                                class="form-control @error('customer_name') is-invalid @enderror"
                                                id="customer_name" name="customer_name" value="{{ old('customer_name') }}"
                                                placeholder="Nhập tên khách hàng" required>
                                            @error('customer_name')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="form-group" id="userEmailGroup">
                                            <label for="customer_email">Email <span class="text-danger">*</span></label>
                                            <input type="email"
                                                class="form-control @error('customer_email') is-invalid @enderror"
                                                id="customer_email" name="customer_email"
                                                value="{{ old('customer_email') }}" placeholder="Nhập email" required>
                                            <small class="form-text text-muted" id="emailHelp">Email để tạo tài khoản mới
                                                (nếu chưa có tài khoản)</small>
                                            @error('customer_email')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <input type="hidden" id="user_id" name="user_id" value="{{ old('user_id') }}">
                                        <div id="existingReservationAlert" class="alert alert-warning"
                                            style="display: none;">
                                            <i class="fas fa-exclamation-triangle"></i>
                                            <strong>Cảnh báo:</strong> <span id="existingReservationMessage"></span>
                                        </div>

                                        <div class="form-group">
                                            <label for="num_people">Số lượng người <span
                                                    class="text-danger">*</span></label>
                                            <input type="number"
                                                class="form-control @error('num_people') is-invalid @enderror"
                                                id="num_people" name="num_people" value="{{ old('num_people', 1) }}"
                                                min="1" placeholder="Nhập số người" required>
                                            @error('num_people')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="note">Ghi chú</label>
                                            <textarea class="form-control @error('note') is-invalid @enderror" id="note" name="note" rows="3"
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
                                            <label for="reservation_date">Ngày đặt bàn <span
                                                    class="text-danger">*</span></label>
                                            <input type="date"
                                                class="form-control @error('reservation_date') is-invalid @enderror"
                                                id="reservation_date" name="reservation_date"
                                                value="{{ old('reservation_date', date('Y-m-d')) }}"
                                                min="{{ date('Y-m-d') }}"
                                                onchange="updateAvailableTables(); checkExistingReservation(); checkDepositRequired(); updateShiftOptions();"
                                                required>
                                            @error('reservation_date')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="shift">Ca <span class="text-danger">*</span></label>
                                            <select class="form-control @error('shift') is-invalid @enderror"
                                                id="shift" name="shift"
                                                onchange="updateAvailableTables(); checkExistingReservation();" required>
                                                <option value="">-- Chọn ca --</option>
                                                <option value="morning" {{ old('shift') == 'morning' ? 'selected' : '' }}>
                                                    Sáng (8-13h)</option>
                                                <option value="afternoon"
                                                    {{ old('shift') == 'afternoon' ? 'selected' : '' }}>Trưa (13-18h)
                                                </option>
                                                <option value="evening" {{ old('shift') == 'evening' ? 'selected' : '' }}>
                                                    Tối (18-23h)</option>
                                            </select>
                                            @error('shift')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label class="font-weight-bold">Chọn bàn <span
                                                    class="text-danger">*</span></label>
                                            <div id="errorMessage" class="alert alert-danger" style="display: none;">
                                                <i class="fas fa-exclamation-triangle"></i> <strong>Vui lòng chọn ít nhất 1
                                                    bàn!</strong>
                                            </div>
                                            <div class="border p-3 bg-light" style="max-height: 300px; overflow-y: auto;">
                                                <div id="tablesContainer">
                                                    @foreach ($allTables as $table)
                                                        <div class="custom-control custom-checkbox mb-2">
                                                            <input type="checkbox"
                                                                class="custom-control-input table-checkbox"
                                                                id="table{{ $table->id }}" name="table_ids[]"
                                                                value="{{ $table->id }}"
                                                                data-table-type="{{ $table->type }}"
                                                                {{ is_array(old('table_ids')) && in_array($table->id, old('table_ids')) ? 'checked' : '' }}>
                                                            <label class="custom-control-label"
                                                                for="table{{ $table->id }}">
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

                                        <!-- Cần cọc -->
                                        <div class="form-group mt-3">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input" id="require_deposit"
                                                    name="require_deposit" value="1"
                                                    {{ old('require_deposit') ? 'checked' : '' }}>
                                                <label class="custom-control-label" for="require_deposit">
                                                    <strong>Cần đặt cọc</strong>
                                                </label>
                                            </div>
                                            <small class="form-text text-muted" id="depositInfo">
                                                Nếu tích: Cọc món ăn (nếu có) + Cọc bàn (nếu ngày lễ hoặc VIP)
                                            </small>
                                        </div>
                                    </div>
                                </div>

                                <!-- Thêm món ăn -->
                                <div class="row mt-3">
                                    <div class="col-12">
                                        <div class="card">
                                            <div class="card-header d-flex justify-content-between align-items-center">
                                                <h5 class="mb-0"><i class="fas fa-utensils"></i> Món ăn đã chọn</h5>
                                                <button type="button" class="btn btn-sm btn-primary" data-toggle="modal"
                                                    data-target="#selectMenuModal">
                                                    <i class="fas fa-plus"></i> Chọn món
                                                </button>
                                            </div>
                                            <div class="card-body">
                                                <div id="selectedMenusContainer">
                                                    <div class="empty-state">
                                                        <i class="fas fa-utensils"></i>
                                                        <p class="mb-0">Chưa có món nào được chọn</p>
                                                        <small>Nhấn nút "Chọn món" ở trên để thêm món vào đơn</small>
                                                    </div>
                                                </div>

                                                <div class="mt-3 p-3 bg-light rounded">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <strong>Tạm tính:</strong> <span id="subtotal">0</span> VND
                                                        </div>
                                                        <div class="col-md-6">
                                                            <strong>VAT (8%):</strong> <span id="vat">0</span> VND
                                                        </div>
                                                        <div class="col-md-12 mt-2">
                                                            <strong>Tổng tiền món:</strong> <span id="totalPrice">0</span>
                                                            VND
                                                        </div>
                                                        <div class="col-md-12 mt-2 border-top pt-2">
                                                            <strong>Cọc món ăn:</strong> <span id="menuDeposit">0</span>
                                                            VND
                                                        </div>
                                                        <div class="col-md-12 mt-2">
                                                            <strong>Cọc bàn:</strong> <span id="tableDeposit">0</span> VND
                                                        </div>
                                                        <div class="col-md-12 mt-2 border-top pt-2">
                                                            <strong class="text-danger">Tổng tiền cọc:</strong> <span
                                                                id="totalDeposit"
                                                                class="text-danger font-weight-bold">0</span> VND
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Modal chọn món -->
                                <div class="modal fade" id="selectMenuModal" tabindex="-1" role="dialog"
                                    aria-labelledby="selectMenuModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-lg" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="selectMenuModalLabel">
                                                    <i class="fas fa-utensils"></i> Chọn món ăn
                                                </h5>
                                                <button type="button" class="close" data-dismiss="modal"
                                                    aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row" style="height: 500px;">
                                                    <!-- Danh sách danh mục bên trái -->
                                                    <div class="col-md-3 border-right"
                                                        style="overflow-y: auto; max-height: 500px;">
                                                        <h6 class="mb-3">Danh mục</h6>
                                                        <div class="list-group" id="categoryList">
                                                            <a href="javascript:void(0)"
                                                                class="list-group-item list-group-item-action active"
                                                                data-category-id="all" onclick="selectCategory('all')">
                                                                Tất cả
                                                            </a>
                                                            @foreach ($categories as $category)
                                                                <a href="javascript:void(0)"
                                                                    class="list-group-item list-group-item-action"
                                                                    data-category-id="{{ $category->id }}"
                                                                    onclick="selectCategory({{ $category->id }})">
                                                                    {{ $category->name }}
                                                                </a>
                                                            @endforeach
                                                        </div>
                                                    </div>

                                                    <!-- Danh sách món bên phải -->
                                                    <div class="col-md-9" style="overflow-y: auto; max-height: 500px;">
                                                        <div class="mb-3">
                                                            <input type="text" class="form-control" id="menuSearch"
                                                                placeholder="Tìm kiếm món..." onkeyup="filterMenus()">
                                                        </div>
                                                        <div id="menuList" class="row">
                                                            @foreach ($menus as $menu)
                                                                <div class="col-md-6 mb-3 menu-item-card"
                                                                    data-category-id="{{ $menu->category_id }}"
                                                                    data-menu-name="{{ strtolower($menu->name) }}">
                                                                    <div class="card h-100 menu-card"
                                                                        onclick="addMenuToOrder({{ $menu->id }}, '{{ addslashes($menu->name) }}', {{ $menu->price }})"
                                                                        style="cursor: pointer;">
                                                                        <div
                                                                            class="card-body d-flex justify-content-between align-items-center">
                                                                            <div>
                                                                                <h6 class="card-title mb-1">
                                                                                    {{ $menu->name }}</h6>
                                                                                @if ($menu->category)
                                                                                    <small
                                                                                        class="text-muted">{{ $menu->category->name }}</small>
                                                                                @endif
                                                                            </div>
                                                                            <div class="text-right">
                                                                                <p class="card-text text-primary font-weight-bold mb-0"
                                                                                    style="font-size: 18px;">
                                                                                    {{ number_format($menu->price, 0, ',', '.') }}
                                                                                    <small>VND</small>
                                                                                </p>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                        <div id="noMenuMessage" class="text-center text-muted py-5"
                                                            style="display: none;">
                                                            Không tìm thấy món nào
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-dismiss="modal">Đóng</button>
                                            </div>
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

@push('styles')
    <style>
        .menu-card {
            transition: all 0.3s ease;
            border: 2px solid #e9ecef;
            height: 100%;
        }

        .menu-card:hover {
            border-color: #007bff;
            box-shadow: 0 4px 12px rgba(0, 123, 255, 0.25);
            transform: translateY(-3px);
        }

        .menu-card.border-success {
            border-color: #28a745 !important;
            box-shadow: 0 4px 12px rgba(40, 167, 69, 0.4);
            animation: pulse 0.5s ease;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.02);
            }

            100% {
                transform: scale(1);
            }
        }

        .selected-menu-item {
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
            border: 1px solid #dee2e6;
            border-radius: 8px;
            transition: all 0.2s ease;
            padding: 15px !important;
        }

        .selected-menu-item:hover {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            transform: translateX(5px);
        }

        .selected-menu-item .menu-name {
            font-size: 16px;
            font-weight: 600;
            color: #212529;
            margin-bottom: 5px;
        }

        .selected-menu-item .menu-price-unit {
            font-size: 14px;
            color: #6c757d;
        }

        .selected-menu-item .menu-total-display {
            font-size: 18px;
            font-weight: 700;
            color: #007bff;
        }

        .selected-menu-item .btn-danger {
            border-radius: 6px;
            padding: 6px 12px;
            font-size: 13px;
        }

        #categoryList .list-group-item {
            cursor: pointer;
            border-radius: 6px !important;
            margin-bottom: 5px;
            transition: all 0.2s ease;
            border: 1px solid #dee2e6;
        }

        #categoryList .list-group-item:hover {
            background-color: #e9ecef;
            border-color: #adb5bd;
            transform: translateX(5px);
        }

        #categoryList .list-group-item.active {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            border-color: #0056b3;
            color: white;
            font-weight: 600;
            box-shadow: 0 2px 6px rgba(0, 123, 255, 0.3);
        }

        .menu-item-card .card {
            border-radius: 8px;
            overflow: hidden;
        }

        .menu-item-card .card-body {
            padding: 15px;
        }

        .menu-item-card .card-title {
            font-size: 15px;
            font-weight: 600;
            color: #212529;
            margin-bottom: 8px;
        }

        .menu-item-card .card-text {
            font-size: 16px;
            margin: 0;
        }

        .input-group-sm .btn {
            border-radius: 4px;
            padding: 4px 10px;
        }

        .input-group-sm input {
            border-radius: 4px;
        }

        #menuSearch {
            border-radius: 8px;
            border: 2px solid #dee2e6;
            padding: 10px 15px;
        }

        #menuSearch:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }

        .modal-content {
            border-radius: 12px;
            border: none;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
        }

        .modal-header {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            color: white;
            border-radius: 12px 12px 0 0;
            padding: 20px;
        }

        .modal-header .close {
            color: white;
            opacity: 0.9;
            text-shadow: none;
        }

        .modal-header .close:hover {
            opacity: 1;
        }

        .modal-body {
            padding: 20px;
        }

        .card-header {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-bottom: 2px solid #dee2e6;
        }

        .empty-state {
            padding: 40px 20px;
            text-align: center;
            color: #6c757d;
        }

        .empty-state i {
            font-size: 48px;
            margin-bottom: 15px;
            opacity: 0.5;
        }
    </style>
@endpush

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
                        customerEmail.readOnly = true;
                        customerEmail.classList.add('bg-light');
                        customerEmail.required = true;
                        document.getElementById('emailHelp').textContent = 'Email từ tài khoản';
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
                        customerEmail.readOnly = false;
                        customerEmail.classList.remove('bg-light');
                        customerEmail.required = true;
                        document.getElementById('emailHelp').textContent =
                            'Email để tạo tài khoản mới (nếu chưa có tài khoản)';
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
                            existingReservationMessage.innerHTML =
                                `${data.message}<br><small>Mã đơn: #${data.reservation.id} - Trạng thái: ${data.reservation.status_text}</small>`;
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
                errorMessage.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
                return false;
            }

            errorMessage.style.display = 'none';
            return true;
        });

        // Cập nhật các option ca dựa trên ngày đặt
        function updateShiftOptions() {
            const dateInput = document.getElementById('reservation_date');
            const shiftSelect = document.getElementById('shift');
            
            if (!dateInput || !shiftSelect) return;
            
            const selectedDate = new Date(dateInput.value);
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            selectedDate.setHours(0, 0, 0, 0);
            
            const isToday = selectedDate.getTime() === today.getTime();
            
            // Lấy giờ hiện tại
            const now = new Date();
            const currentHour = now.getHours();
            
            // Lấy tất cả các option
            const morningOption = shiftSelect.querySelector('option[value="morning"]');
            const afternoonOption = shiftSelect.querySelector('option[value="afternoon"]');
            const eveningOption = shiftSelect.querySelector('option[value="evening"]');
            
            // Reset tất cả các option
            if (morningOption) {
                morningOption.disabled = false;
            }
            if (afternoonOption) {
                afternoonOption.disabled = false;
            }
            if (eveningOption) {
                eveningOption.disabled = false;
            }
            
            if (isToday) {
                // Nếu là hôm nay, disable các ca đã qua
                if (currentHour >= 13 && morningOption) {
                    morningOption.disabled = true;
                }
                
                if (currentHour >= 18 && afternoonOption) {
                    afternoonOption.disabled = true;
                }
                
                if (currentHour >= 23 && eveningOption) {
                    eveningOption.disabled = true;
                }
                
                // Nếu ca đang chọn bị disable, reset về rỗng
                if (shiftSelect.value && shiftSelect.options[shiftSelect.selectedIndex].disabled) {
                    shiftSelect.value = '';
                }
            }
        }

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
                        data-table-type="${table.type || 'normal'}"
                        ${isBusy ? 'disabled' : ''}
                    >
                    <label class="custom-control-label" for="table${table.id}">
                        ${table.name} 
                        ${isBusy ? '<span class="badge badge-danger badge-sm">Đang bận</span>' : '<span class="badge badge-success badge-sm">Rỗi</span>'}
                    </label>
                `;

                        container.appendChild(div);

                        // Thêm event listener cho checkbox mới để tính lại cọc
                        const checkbox = div.querySelector('.table-checkbox');
                        if (checkbox && !isBusy) {
                            checkbox.addEventListener('change', checkDepositRequired);
                        }
                    });
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }

        // Kiểm tra ngày có cần cọc không và tính tiền cọc
        function checkDepositRequired() {
            const date = document.getElementById('reservation_date').value;
            const requireDeposit = document.getElementById('require_deposit');
            if (!requireDeposit) return;

            const isChecked = requireDeposit.checked;
            const depositInfo = document.getElementById('depositInfo');

            // Kiểm tra có VIP không - lấy từ data attribute
            const selectedTableCheckboxes = Array.from(document.querySelectorAll('.table-checkbox:checked'));
            const vipTables = selectedTableCheckboxes.filter(cb => {
                const tableType = cb.getAttribute('data-table-type');
                return tableType === 'vip';
            });
            const normalTables = selectedTableCheckboxes.filter(cb => {
                const tableType = cb.getAttribute('data-table-type');
                return tableType === 'normal' || !tableType;
            });
            const hasVip = vipTables.length > 0;
            const hasNormalTables = normalTables.length > 0;
            const vipTableCount = vipTables.length;
            const normalTableCount = normalTables.length;

            if (!isChecked) {
                depositInfo.innerHTML = '<span class="text-muted">Không cần cọc gì cả</span>';
                calculateDeposit(null, false, false, 0, 0);
                return;
            }

            if (!date) {
                depositInfo.innerHTML = '<span class="text-info">Vui lòng chọn ngày đặt bàn</span>';
                const minTablesForDeposit = {{ $minTablesForDeposit ?? 2 }};
                const holidayData = {
                    min_tables_for_deposit: minTablesForDeposit
                };
                calculateDeposit(holidayData, hasVip, hasNormalTables, vipTableCount, normalTableCount);
                return;
            }

            // Gọi API kiểm tra ngày lễ
            fetch(`/api/deposit-required-dates/check?date=${date}`)
                .then(response => response.json())
                .then(data => {
                    let infoText = '';
                    const depositNormalTables = {{ $depositNormalTables ?? 500000 }};
                    const minTablesForDeposit = {{ $minTablesForDeposit ?? 2 }};

                    if (hasVip) {
                        infoText =
                            '<span class="text-warning"><i class="fas fa-exclamation-triangle"></i> Phòng VIP: Luôn cần cọc bàn</span>';
                    } else if (data.requires_deposit && hasNormalTables) {
                        // Ngày lễ + bàn thường: Cần cọc bàn thường
                        const depositAmount = data.deposit_amount || depositNormalTables;
                        infoText =
                            `<span class="text-warning"><i class="fas fa-exclamation-triangle"></i> Ngày lễ: Cần cọc bàn thường (${new Intl.NumberFormat('vi-VN').format(depositAmount)} VND)</span>`;
                    } else if (data.requires_deposit && !hasNormalTables) {
                        infoText =
                            '<span class="text-info"><i class="fas fa-info-circle"></i> Ngày lễ nhưng chưa chọn bàn thường</span>';
                    } else if (!data.requires_deposit && hasNormalTables && normalTableCount >= minTablesForDeposit) {
                        // Ngày thường + số bàn >= số bàn cấu hình: Cần cọc bàn
                        infoText =
                            `<span class="text-warning"><i class="fas fa-exclamation-triangle"></i> Ngày thường: Từ ${minTablesForDeposit} bàn trở lên cần cọc bàn (${new Intl.NumberFormat('vi-VN').format(depositNormalTables)} VND/bàn)</span>`;
                    } else {
                        infoText =
                            '<span class="text-info"><i class="fas fa-info-circle"></i> Ngày thường: Chỉ cọc món ăn (nếu có)</span>';
                    }
                    depositInfo.innerHTML = infoText;

                    // Truyền data vào calculateDeposit với deposit_amount và minTablesForDeposit
                    const holidayData = {
                        requires_deposit: data.requires_deposit,
                        deposit_amount: data.deposit_amount || (data.requires_deposit ? depositNormalTables : null),
                        min_tables_for_deposit: minTablesForDeposit
                    };
                    calculateDeposit(holidayData, hasVip, hasNormalTables, vipTableCount, normalTableCount);
                })
                .catch(error => {
                    console.error('Error:', error);
                    const minTablesForDeposit = {{ $minTablesForDeposit ?? 2 }};
                    const depositNormalTables = {{ $depositNormalTables ?? 500000 }};
                    let infoText = '';
                    if (hasVip) {
                        infoText =
                            '<span class="text-warning"><i class="fas fa-exclamation-triangle"></i> Phòng VIP: Luôn cần cọc bàn</span>';
                    } else if (hasNormalTables && normalTableCount >= minTablesForDeposit) {
                        infoText =
                            `<span class="text-warning"><i class="fas fa-exclamation-triangle"></i> Ngày thường: Từ ${minTablesForDeposit} bàn trở lên cần cọc bàn (${new Intl.NumberFormat('vi-VN').format(depositNormalTables)} VND/bàn)</span>`;
                    } else {
                        infoText =
                            '<span class="text-info">Nếu tích: Cọc món ăn (nếu có) + Cọc bàn (nếu ngày lễ hoặc VIP hoặc từ ' +
                            minTablesForDeposit + ' bàn trở lên)</span>';
                    }
                    depositInfo.innerHTML = infoText;
                    const holidayData = {
                        min_tables_for_deposit: minTablesForDeposit
                    };
                    calculateDeposit(holidayData, hasVip, hasNormalTables, vipTableCount, normalTableCount);
                });
        }

        // Tính toán tiền cọc
        function calculateDeposit(holidayData = null, hasVip = false, hasNormalTables = false, vipTableCount = 0,
            normalTableCount = 0) {
            const requireDepositEl = document.getElementById('require_deposit');
            if (!requireDepositEl) return;

            const requireDeposit = requireDepositEl.checked;

            if (!requireDeposit) {
                document.getElementById('menuDeposit').textContent = '0';
                document.getElementById('tableDeposit').textContent = '0';
                document.getElementById('totalDeposit').textContent = '0';
                return;
            }

            // Tính cọc món ăn (100% tổng tiền món nếu có)
            const totalPriceEl = document.getElementById('totalPrice');
            if (!totalPriceEl) return;

            const totalPrice = parseFloat(totalPriceEl.textContent.replace(/[^\d]/g, '')) || 0;
            const menuDeposit = totalPrice;

            // Tính cọc bàn (nhân với số lượng bàn)
            let tableDeposit = 0;
            const depositVipRooms = {{ $depositVipRooms ?? 1000000 }};
            const depositNormalTables = {{ $depositNormalTables ?? 500000 }};
            const minTablesForDeposit = holidayData?.min_tables_for_deposit || {{ $minTablesForDeposit ?? 2 }};

            if (hasVip && vipTableCount > 0) {
                // VIP: Luôn cần cọc, nhân với số lượng bàn VIP
                tableDeposit = depositVipRooms * vipTableCount;
            } else if (holidayData && holidayData.requires_deposit && hasNormalTables && normalTableCount > 0) {
                // Ngày lễ + bàn thường: Cần cọc bàn thường, nhân với số lượng bàn thường
                const holidayDepositAmount = holidayData.deposit_amount ? parseFloat(holidayData.deposit_amount) : null;
                const depositPerTable = holidayDepositAmount || depositNormalTables;
                tableDeposit = depositPerTable * normalTableCount;
            } else if (!holidayData?.requires_deposit && hasNormalTables && normalTableCount >= minTablesForDeposit) {
                // Ngày thường + số bàn >= số bàn cấu hình: Cần cọc bàn thường, nhân với số lượng bàn thường
                tableDeposit = depositNormalTables * normalTableCount;
            }
            // Ngày thường + số bàn < số bàn cấu hình: Không cần cọc bàn (chỉ cọc món nếu có)

            const totalDeposit = menuDeposit + tableDeposit;

            // Cập nhật hiển thị
            const menuDepositEl = document.getElementById('menuDeposit');
            const tableDepositEl = document.getElementById('tableDeposit');
            const totalDepositEl = document.getElementById('totalDeposit');

            if (menuDepositEl) menuDepositEl.textContent = new Intl.NumberFormat('vi-VN').format(menuDeposit);
            if (tableDepositEl) tableDepositEl.textContent = new Intl.NumberFormat('vi-VN').format(tableDeposit);
            if (totalDepositEl) totalDepositEl.textContent = new Intl.NumberFormat('vi-VN').format(totalDeposit);
        }

        // Cập nhật thông tin cọc khi thay đổi ngày, bàn hoặc checkbox cần cọc
        document.addEventListener('DOMContentLoaded', function() {
            // Khởi tạo các option ca khi tải trang
            updateShiftOptions();
            
            // Event listener cho ngày
            const dateInput = document.getElementById('reservation_date');
            if (dateInput) {
                dateInput.addEventListener('change', checkDepositRequired);
            }

            // Event listener cho checkbox cần cọc
            const requireDepositCheckbox = document.getElementById('require_deposit');
            if (requireDepositCheckbox) {
                requireDepositCheckbox.addEventListener('change', checkDepositRequired);
            }

            // Event listener cho checkbox bàn ban đầu
            document.querySelectorAll('.table-checkbox').forEach(checkbox => {
                checkbox.addEventListener('change', checkDepositRequired);
            });

            // Khởi tạo kiểm tra cọc khi tải trang
            checkDepositRequired();
            calculateTotal();
        });

        // Thêm event listener cho tất cả checkbox bàn (cả ban đầu và động)
        document.addEventListener('change', function(e) {
            if (e.target.classList.contains('table-checkbox')) {
                checkDepositRequired();
            }
        });

        // Quản lý món ăn đã chọn
        let selectedMenus = [];
        let menuIndex = 0;

        // Chọn danh mục trong modal
        function selectCategory(categoryId) {
            // Cập nhật active state
            document.querySelectorAll('#categoryList .list-group-item').forEach(item => {
                item.classList.remove('active');
                if (item.dataset.categoryId == categoryId || (categoryId === 'all' && item.dataset.categoryId ===
                        'all')) {
                    item.classList.add('active');
                }
            });

            // Lọc món theo danh mục
            const menuCards = document.querySelectorAll('.menu-item-card');
            menuCards.forEach(card => {
                if (categoryId === 'all' || card.dataset.categoryId == categoryId) {
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }
            });

            // Áp dụng filter tìm kiếm nếu có
            filterMenus();
        }

        // Tìm kiếm món
        function filterMenus() {
            const searchTerm = document.getElementById('menuSearch').value.toLowerCase();
            const activeCategory = document.querySelector('#categoryList .list-group-item.active');
            const categoryId = activeCategory ? activeCategory.dataset.categoryId : 'all';

            const menuCards = document.querySelectorAll('.menu-item-card');
            let visibleCount = 0;

            menuCards.forEach(card => {
                const menuName = card.dataset.menuName || '';
                const cardCategoryId = card.dataset.categoryId;

                const matchesCategory = categoryId === 'all' || cardCategoryId == categoryId;
                const matchesSearch = menuName.includes(searchTerm);

                if (matchesCategory && matchesSearch) {
                    card.style.display = '';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });

            // Hiển thị thông báo nếu không tìm thấy
            document.getElementById('noMenuMessage').style.display = visibleCount === 0 ? 'block' : 'none';
        }

        // Thêm món vào đơn hàng
        function addMenuToOrder(menuId, menuName, menuPrice) {
            // Kiểm tra món đã được thêm chưa
            const existingMenu = selectedMenus.find(m => m.menu_id == menuId);

            if (existingMenu) {
                // Nếu đã có, tăng số lượng
                existingMenu.quantity++;
                updateSelectedMenuDisplay(existingMenu);
            } else {
                // Nếu chưa có, thêm mới
                const newMenu = {
                    index: menuIndex++,
                    menu_id: menuId,
                    name: menuName,
                    price: menuPrice,
                    quantity: 1
                };
                selectedMenus.push(newMenu);
                addSelectedMenuToContainer(newMenu);
            }

            calculateTotal();

            // Hiệu ứng visual feedback
            const card = event.target.closest('.menu-card');
            card.classList.add('border-success');
            setTimeout(() => {
                card.classList.remove('border-success');
            }, 500);
        }

        // Thêm món vào container hiển thị
        function addSelectedMenuToContainer(menu) {
            const container = document.getElementById('selectedMenusContainer');

            // Xóa thông báo "Chưa có món" nếu có
            if (container.querySelector('.empty-state')) {
                container.innerHTML = '';
            }

            const menuDiv = document.createElement('div');
            menuDiv.className = 'selected-menu-item mb-3';
            menuDiv.id = `selected-menu-${menu.index}`;
            menuDiv.innerHTML = `
        <div class="row align-items-center">
            <div class="col-md-4">
                <div class="menu-name">${menu.name}</div>
                <div class="menu-price-unit">${new Intl.NumberFormat('vi-VN').format(menu.price)} VND</div>
            </div>
            <div class="col-md-3">
                <div class="input-group input-group-sm">
                    <div class="input-group-prepend">
                        <button class="btn btn-outline-secondary" type="button" onclick="decreaseQuantity(${menu.index})">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                    <input type="number" class="form-control text-center menu-quantity-input" 
                           value="${menu.quantity}" min="1" 
                           onchange="updateQuantity(${menu.index}, this.value)"
                           data-menu-index="${menu.index}">
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary" type="button" onclick="increaseQuantity(${menu.index})">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="col-md-3 text-right">
                <div class="menu-total-display">${new Intl.NumberFormat('vi-VN').format(menu.price * menu.quantity)} VND</div>
            </div>
            <div class="col-md-2 text-right">
                <button type="button" class="btn btn-danger btn-sm" onclick="removeMenu(${menu.index})">
                    <i class="fas fa-trash"></i> Xóa
                </button>
            </div>
        </div>
        <input type="hidden" name="menus[${menu.index}][menu_id]" value="${menu.menu_id}">
        <input type="hidden" name="menus[${menu.index}][quantity]" value="${menu.quantity}" class="menu-quantity-hidden" data-menu-index="${menu.index}">
    `;

            container.appendChild(menuDiv);

            // Animation khi thêm món mới
            menuDiv.style.opacity = '0';
            menuDiv.style.transform = 'translateY(-10px)';
            setTimeout(() => {
                menuDiv.style.transition = 'all 0.3s ease';
                menuDiv.style.opacity = '1';
                menuDiv.style.transform = 'translateY(0)';
            }, 10);
        }

        // Cập nhật hiển thị món đã chọn
        function updateSelectedMenuDisplay(menu) {
            const menuDiv = document.getElementById(`selected-menu-${menu.index}`);
            if (menuDiv) {
                const quantityInput = menuDiv.querySelector('.menu-quantity-input');
                const quantityHidden = menuDiv.querySelector('.menu-quantity-hidden');
                const totalDisplay = menuDiv.querySelector('.menu-total-display');

                if (quantityInput) quantityInput.value = menu.quantity;
                if (quantityHidden) quantityHidden.value = menu.quantity;
                if (totalDisplay) totalDisplay.textContent = new Intl.NumberFormat('vi-VN').format(menu.price * menu
                    .quantity) + ' VND';
            }
        }

        // Tăng số lượng
        function increaseQuantity(index) {
            const menu = selectedMenus.find(m => m.index === index);
            if (menu) {
                menu.quantity++;
                updateSelectedMenuDisplay(menu);
                calculateTotal();
            }
        }

        // Giảm số lượng
        function decreaseQuantity(index) {
            const menu = selectedMenus.find(m => m.index === index);
            if (menu && menu.quantity > 1) {
                menu.quantity--;
                updateSelectedMenuDisplay(menu);
                calculateTotal();
            }
        }

        // Cập nhật số lượng
        function updateQuantity(index, quantity) {
            const menu = selectedMenus.find(m => m.index === index);
            if (menu && quantity > 0) {
                menu.quantity = parseInt(quantity);
                updateSelectedMenuDisplay(menu);
                calculateTotal();
            }
        }

        // Xóa món khỏi danh sách
        function removeMenu(index) {
            selectedMenus = selectedMenus.filter(m => m.index !== index);
            const menuDiv = document.getElementById(`selected-menu-${index}`);
            if (menuDiv) {
                menuDiv.remove();
            }

            // Hiển thị thông báo nếu không còn món nào
            const container = document.getElementById('selectedMenusContainer');
            if (container.children.length === 0) {
                container.innerHTML = `
            <div class="empty-state">
                <i class="fas fa-utensils"></i>
                <p class="mb-0">Chưa có món nào được chọn</p>
                <small>Nhấn nút "Chọn món" ở trên để thêm món vào đơn</small>
            </div>
        `;
            }

            calculateTotal();
        }

        // Tính tổng tiền
        function calculateTotal() {
            let subtotal = 0;
            selectedMenus.forEach(menu => {
                subtotal += menu.price * menu.quantity;
            });

            const vat = subtotal * 0.08;
            const totalPrice = subtotal + vat;

            document.getElementById('subtotal').textContent = new Intl.NumberFormat('vi-VN').format(subtotal);
            document.getElementById('vat').textContent = new Intl.NumberFormat('vi-VN').format(vat);
            document.getElementById('totalPrice').textContent = new Intl.NumberFormat('vi-VN').format(totalPrice);

            // Cập nhật lại tiền cọc sau khi tính tổng tiền món
            checkDepositRequired();
        }
    </script>
@endpush
