@extends('admin.layouts.main')

@section('noidung')
    <div class="content-wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h3 class="mb-0">Quản lý cọc</h3>
                                <div>
                                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addDepositDateModal">
                                        <i class="fas fa-plus"></i> Thêm ngày
                                    </button>
                                    <button type="button" class="btn btn-info" data-toggle="modal" data-target="#addRangeDepositDateModal">
                                        <i class="fas fa-calendar-alt"></i> Thêm khoảng ngày
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            @if (session('success'))
                                <div class="alert alert-success alert-dismissible fade show">
                                    {{ session('success') }}
                                    <button type="button" class="close" data-dismiss="alert">
                                        <span>&times;</span>
                                    </button>
                                </div>
                            @endif

                            @if (session('error'))
                                <div class="alert alert-danger alert-dismissible fade show">
                                    {{ session('error') }}
                                    <button type="button" class="close" data-dismiss="alert">
                                        <span>&times;</span>
                                    </button>
                                </div>
                            @endif

                            <div class="card mb-3 bg-light">
                                <div class="card-body">
                                    <h6 class="font-weight-bold mb-3">Cấu hình tiền cọc</h6>
                                    <form action="{{ route('admin.depositRequiredDate.updateDepositSettings') }}" method="POST" class="row align-items-end">
                                        @csrf
                                        @method('PUT')
                                        <div class="col-md-3">
                                            <label class="font-weight-bold">Tiền cọc bàn thường (VND) <span class="text-danger">*</span></label>
                                            <input type="number" name="deposit_normal_tables" class="form-control" 
                                                   value="{{ old('deposit_normal_tables', $deposit_normal_tables ?? 500000) }}" 
                                                   min="1" step="1" placeholder="Nhập số tiền cọc" required>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="font-weight-bold">Tiền cọc phòng VIP (VND) <span class="text-danger">*</span></label>
                                            <input type="number" name="deposit_vip_rooms" class="form-control" 
                                                   value="{{ old('deposit_vip_rooms', $deposit_vip_rooms ?? 1000000) }}" 
                                                   min="1" step="1" placeholder="Nhập số tiền cọc" required>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="font-weight-bold">Số bàn tối thiểu cần cọc (ngày thường) <span class="text-danger">*</span></label>
                                            <input type="number" name="min_tables_for_deposit" class="form-control" 
                                                   value="{{ old('min_tables_for_deposit', $min_tables_for_deposit ?? 2) }}" 
                                                   min="1" step="1" placeholder="Ví dụ: 2" required>
                                        </div>
                                        <div class="col-md-3">
                                            <button type="submit" class="btn btn-success w-100">
                                                <i class="fas fa-save"></i> Lưu cấu hình
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <div class="card mb-3 bg-light">
                                <div class="card-body">
                                    <form action="{{ route('admin.depositRequiredDate.index') }}" method="GET" class="row" id="filterForm">
                                        <div class="col-md-4">
                                            <label class="font-weight-bold">Ngày</label>
                                            <input type="date" name="date" class="form-control" value="{{ request('date') }}" onchange="document.getElementById('filterForm').submit()">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="font-weight-bold">Trạng thái</label>
                                            <select name="status" class="form-control" onchange="document.getElementById('filterForm').submit()">
                                                <option value="">Tất cả</option>
                                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Kích hoạt</option>
                                                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Vô hiệu</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3 d-flex align-items-end">
                                            <a href="{{ route('admin.depositRequiredDate.index') }}" class="btn btn-secondary">
                                                <i class="fas fa-redo"></i> Reset
                                            </a>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            @if($dates->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover">
                                        <thead class="bg-light">
                                            <tr>
                                                <th width="150">Ngày</th>
                                                <th>Mô tả</th>
                                                <th width="150" class="text-center">Số tiền cọc/bàn</th>
                                                <th width="120" class="text-center">Trạng thái</th>
                                                <th width="180" class="text-center">Thao tác</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($dates as $group)
                                                <tr>
                                                    <td>
                                                        @if($group['is_range'])
                                                            <strong>Từ {{ $group['start_date']->format('d/m/Y') }} đến {{ $group['end_date']->format('d/m/Y') }}</strong>
                                                            <br><small class="text-muted">({{ $group['dates']->count() }} ngày)</small>
                                                        @else
                                                            <strong>{{ $group['start_date']->format('d/m/Y') }}</strong>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($group['description'])
                                                            {{ $group['description'] }}
                                                        @else
                                                            <span class="text-muted">Không có mô tả</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        <strong class="text-primary">{{ number_format($deposit_normal_tables ?? 500000, 0, ',', '.') }} VND</strong>
                                                        <br><small class="text-muted">(Cấu hình chung)</small>
                                                    </td>
                                                    <td class="text-center">
                                                        @if($group['is_active'])
                                                            <span class="badge badge-success">
                                                                <i class="fas fa-check-circle"></i> Kích hoạt
                                                            </span>
                                                        @else
                                                            <span class="badge badge-secondary">
                                                                <i class="fas fa-times-circle"></i> Vô hiệu
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        @php
                                                            $date = $group['dates']->first();
                                                        @endphp
                                                        @if($group['is_range'])
                                                            <button type="button" class="btn btn-sm btn-warning" data-toggle="modal" data-target="#editRangeModal{{ $loop->index }}" title="Chỉnh sửa">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                            <form action="{{ route('admin.depositRequiredDate.toggleStatus', $date->id) }}" method="POST" style="display:inline;">
                                                                @csrf
                                                                @method('PUT')
                                                                @foreach($group['dates'] as $d)
                                                                    <input type="hidden" name="date_ids[]" value="{{ $d->id }}">
                                                                @endforeach
                                                                <button type="submit" class="btn btn-sm btn-{{ $date->is_active ? 'secondary' : 'success' }}" title="{{ $date->is_active ? 'Vô hiệu hóa tất cả' : 'Kích hoạt tất cả' }}" onclick="return confirm('Bạn có chắc muốn {{ $date->is_active ? 'vô hiệu hóa' : 'kích hoạt' }} {{ $group['dates']->count() }} ngày?')">
                                                                    <i class="fas fa-{{ $date->is_active ? 'toggle-on' : 'toggle-off' }}"></i>
                                                                </button>
                                                            </form>
                                                            <form action="{{ route('admin.depositRequiredDate.destroyRange') }}" method="POST" style="display:inline;" onsubmit="return confirm('Bạn có chắc muốn xóa {{ $group['dates']->count() }} ngày này?')">
                                                                @csrf
                                                                @method('DELETE')
                                                                @foreach($group['dates'] as $d)
                                                                    <input type="hidden" name="date_ids[]" value="{{ $d->id }}">
                                                                @endforeach
                                                                <button type="submit" class="btn btn-sm btn-danger" title="Xóa tất cả">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            </form>
                                                        @else
                                                            <button type="button" class="btn btn-sm btn-warning" data-toggle="modal" data-target="#editDateModal{{ $date->id }}" title="Chỉnh sửa">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                            <form action="{{ route('admin.depositRequiredDate.toggleStatus', $date->id) }}" method="POST" style="display:inline;">
                                                                @csrf
                                                                @method('PUT')
                                                                <button type="submit" class="btn btn-sm btn-{{ $date->is_active ? 'secondary' : 'success' }}" title="{{ $date->is_active ? 'Vô hiệu hóa' : 'Kích hoạt' }}">
                                                                    <i class="fas fa-{{ $date->is_active ? 'toggle-on' : 'toggle-off' }}"></i>
                                                                </button>
                                                            </form>
                                                            <form action="{{ route('admin.depositRequiredDate.destroy', $date->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Bạn có chắc muốn xóa ngày này?')">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-sm btn-danger" title="Xóa">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            </form>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <div>
                                        Hiển thị {{ $dates->firstItem() ?? 0 }} đến {{ $dates->lastItem() ?? 0 }} 
                                        trong tổng số {{ $dates->total() }} kết quả
                                    </div>
                                    <div>
                                        {{ $dates->links('pagination::bootstrap-4') }}
                                    </div>
                                </div>
                            @else
                                <div class="alert alert-warning mb-0">
                                    <i class="fas fa-info-circle"></i> Chưa có ngày nào yêu cầu đặt cọc.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Thêm ngày yêu cầu đặt cọc -->
    <div class="modal fade" id="addDepositDateModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="{{ route('admin.depositRequiredDate.store') }}" method="POST">
                    @csrf
                    <div class="modal-header bg-primary">
                        <h5 class="modal-title">
                            <i class="fas fa-calendar-plus"></i> Thêm ngày yêu cầu đặt cọc
                        </h5>
                        <button type="button" class="close text-white" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="font-weight-bold">Ngày <span class="text-danger">*</span></label>
                            <input type="date" name="date" class="form-control" value="{{ old('date', \Carbon\Carbon::tomorrow()->format('Y-m-d')) }}" min="{{ \Carbon\Carbon::today()->format('Y-m-d') }}" required>
                            @error('date')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold">Mô tả (tùy chọn)</label>
                            <input type="text" name="description" class="form-control" placeholder="Ví dụ: Ngày lễ, Cuối tuần..." value="{{ old('description') }}" maxlength="500">
                            @error('description')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> <strong>Lưu ý:</strong> Ngày lễ sẽ bắt buộc cọc dù chỉ 1 bàn.
                        </div>

                        <input type="hidden" name="is_active" value="1">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Lưu
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Thêm nhiều ngày (theo range) -->
    <div class="modal fade" id="addRangeDepositDateModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="{{ route('admin.depositRequiredDate.storeRange') }}" method="POST">
                    @csrf
                    <div class="modal-header bg-info">
                        <h5 class="modal-title">
                            <i class="fas fa-calendar-alt"></i> Thêm nhiều ngày (theo khoảng)
                        </h5>
                        <button type="button" class="close text-white" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold">Từ ngày <span class="text-danger">*</span></label>
                                    <input type="date" name="start_date" class="form-control" value="{{ old('start_date', \Carbon\Carbon::tomorrow()->format('Y-m-d')) }}" min="{{ \Carbon\Carbon::today()->format('Y-m-d') }}" required>
                                    @error('start_date')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold">Đến ngày <span class="text-danger">*</span></label>
                                    <input type="date" name="end_date" class="form-control" value="{{ old('end_date') }}" min="{{ \Carbon\Carbon::tomorrow()->format('Y-m-d') }}" required>
                                    @error('end_date')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold">Mô tả (tùy chọn)</label>
                            <input type="text" name="description" class="form-control" placeholder="Ví dụ: Tuần lễ vàng, Dịp lễ..." value="{{ old('description') }}" maxlength="500">
                        </div>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> <strong>Lưu ý:</strong> Ngày lễ sẽ bắt buộc cọc dù chỉ 1 bàn.
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="range_is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="range_is_active">
                                    Kích hoạt ngay
                                </label>
                            </div>
                        </div>

                     
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                        <button type="submit" class="btn btn-info">
                            <i class="fas fa-save"></i> Lưu
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Chỉnh sửa ngày đơn lẻ -->
    @foreach($dates as $group)
        @if(!$group['is_range'])
            @php
                $date = $group['dates']->first();
            @endphp
            <div class="modal fade" id="editDateModal{{ $date->id }}" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <form action="{{ route('admin.depositRequiredDate.update', $date->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="modal-header bg-warning">
                                <h5 class="modal-title">
                                    <i class="fas fa-edit"></i> Chỉnh sửa ngày yêu cầu đặt cọc
                                </h5>
                                <button type="button" class="close" data-dismiss="modal">
                                    <span>&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="form-group">
                                    <label class="font-weight-bold">Ngày <span class="text-danger">*</span></label>
                                    <input type="date" name="date" class="form-control" value="{{ old('date', $date->date->format('Y-m-d')) }}" min="{{ \Carbon\Carbon::today()->format('Y-m-d') }}" required>
                                    @error('date')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label class="font-weight-bold">Mô tả (tùy chọn)</label>
                                    <input type="text" name="description" class="form-control" placeholder="Ví dụ: Ngày lễ, Cuối tuần..." value="{{ old('description', $date->description) }}" maxlength="500">
                                    @error('description')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i> <strong>Lưu ý:</strong> Ngày lễ sẽ bắt buộc cọc dù chỉ 1 bàn.
                                </div>

                                <div class="form-group">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="edit_is_active{{ $date->id }}" name="is_active" value="1" {{ old('is_active', $date->is_active) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="edit_is_active{{ $date->id }}">
                                            Kích hoạt
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                                <button type="submit" class="btn btn-warning">
                                    <i class="fas fa-save"></i> Lưu thay đổi
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    @endforeach

    <!-- Modal Chỉnh sửa range ngày -->
    @foreach($dates as $index => $group)
        @if($group['is_range'])
            <div class="modal fade" id="editRangeModal{{ $index }}" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <form action="{{ route('admin.depositRequiredDate.updateRange') }}" method="POST">
                            @csrf
                            @method('PUT')
                            @foreach($group['dates'] as $d)
                                <input type="hidden" name="date_ids[]" value="{{ $d->id }}">
                            @endforeach
                            <div class="modal-header bg-warning">
                                <h5 class="modal-title">
                                    <i class="fas fa-edit"></i> Chỉnh sửa range ngày yêu cầu đặt cọc
                                </h5>
                                <button type="button" class="close" data-dismiss="modal">
                                    <span>&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="font-weight-bold">Từ ngày <span class="text-danger">*</span></label>
                                            <input type="date" name="start_date" class="form-control" value="{{ old('start_date', $group['start_date']->format('Y-m-d')) }}" min="{{ \Carbon\Carbon::today()->format('Y-m-d') }}" required>
                                            @error('start_date')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="font-weight-bold">Đến ngày <span class="text-danger">*</span></label>
                                            <input type="date" name="end_date" class="form-control" value="{{ old('end_date', $group['end_date']->format('Y-m-d')) }}" min="{{ \Carbon\Carbon::today()->format('Y-m-d') }}" required>
                                            @error('end_date')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="font-weight-bold">Mô tả (tùy chọn)</label>
                                    <input type="text" name="description" class="form-control" placeholder="Ví dụ: Tuần lễ vàng, Dịp lễ..." value="{{ old('description', $group['description']) }}" maxlength="500">
                                </div>

                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i> <strong>Lưu ý:</strong> Ngày lễ sẽ bắt buộc cọc dù chỉ 1 bàn.
                                </div>

                                <div class="form-group">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="edit_range_is_active{{ $index }}" name="is_active" value="1" {{ old('is_active', $group['is_active']) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="edit_range_is_active{{ $index }}">
                                            Kích hoạt
                                        </label>
                                    </div>
                                </div>

                               
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                                <button type="submit" class="btn btn-warning">
                                    <i class="fas fa-save"></i> Lưu thay đổi
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    @endforeach
@endsection

@push('scripts')
@endpush


