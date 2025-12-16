@extends('admin.layouts.main')

@section('noidung')
    <div class="content-wrapper">

        {{-- HEADER --}}
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Thống kê Menu</h1>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-12">
                        <form action="{{ route('admin.menuStatistics') }}" method="GET" id="filterForm">
                            <div class="btn-group" role="group">
                                <button type="submit" name="filter" value="today"
                                    class="btn btn-sm {{ ($filterType ?? 'this_month') == 'today' ? 'btn-primary' : 'btn-outline-primary' }}">
                                    Hôm nay
                                </button>
                                <button type="submit" name="filter" value="this_week"
                                    class="btn btn-sm {{ ($filterType ?? 'this_month') == 'this_week' ? 'btn-primary' : 'btn-outline-primary' }}">
                                    Tuần này
                                </button>
                                <button type="submit" name="filter" value="this_month"
                                    class="btn btn-sm {{ ($filterType ?? 'this_month') == 'this_month' ? 'btn-primary' : 'btn-outline-primary' }}">
                                    Tháng này
                                </button>
                                <button type="submit" name="filter" value="this_year"
                                    class="btn btn-sm {{ ($filterType ?? 'this_month') == 'this_year' ? 'btn-primary' : 'btn-outline-primary' }}">
                                    Năm nay
                                </button>
                            </div>

                            <div class="form-inline mt-2">
                                <label class="mr-2">Tùy chọn:</label>
                                <input type="date" name="from" value="{{ request('from', $from->format('Y-m-d')) }}"
                                    class="form-control form-control-sm mr-2" id="customFrom">
                                <label class="mr-2">đến</label>
                                <input type="date" name="to" value="{{ request('to', $to->format('Y-m-d')) }}"
                                    class="form-control form-control-sm mr-2" id="customTo">
                                <input type="hidden" name="filter" value="custom" id="customFilter">
                                <button type="submit" class="btn btn-sm btn-outline-secondary">
                                    <i class="fas fa-search"></i> Áp dụng
                                </button>
                            </div>

                            <div class="mt-2">
                                <small class="text-muted">
                                    <i class="fas fa-info-circle"></i>
                                    <strong>{{ $filterLabel ?? 'Tháng này' }}</strong>
                                    ({{ $from->format('d/m/Y') }} - {{ $to->format('d/m/Y') }})
                                </small>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- CONTENT --}}
        <section class="content">
            <div class="container-fluid">

                {{-- BOX THỐNG KÊ --}}
                <div class="row">
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-info">
                            <div class="inner">
                                <h3>{{ $totalMenus }}</h3>
                                <p>Tổng số món</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-utensils"></i>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-5 col-6">
                        <div class="small-box bg-success">
                            <div class="inner">
                                @if ($bestSelling)
                                    <h4 class="mb-1">{{ $bestSelling->name }}</h4>
                                    <p>Bán chạy nhất ({{ $bestSelling->total_qty }} lượt)</p>
                                @else
                                    <p>Chưa có dữ liệu</p>
                                @endif
                            </div>
                            <div class="icon">
                                <i class="fas fa-star"></i>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 col-12">
                        <div class="small-box bg-warning">
                            <div class="inner">
                                @if ($topCategory)
                                    <h4 class="mb-1">{{ $topCategory['name'] }}</h4>
                                    <p>Nhóm được chọn nhiều nhất</p>
                                @else
                                    <p>Chưa có dữ liệu</p>
                                @endif
                            </div>
                            <div class="icon">
                                <i class="fas fa-layer-group"></i>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- BẢNG --}}
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Doanh thu chi tiết theo món</h3>
                    </div>
                    <div class="card-body table-responsive p-0">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Tên món</th>
                                    <th>Danh mục</th>
                                    <th>Số lượng bán</th>
                                    <th>Doanh thu (VNĐ)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($menuStats as $index => $item)
                                    <tr>
                                        <td>{{ $menuStats->firstItem() + $index }}</td>
                                        <td>{{ $item->name }}</td>
                                        <td>{{ $item->category_name }}</td>
                                        <td>{{ $item->total_qty }}</td>
                                        <td>{{ number_format($item->revenue) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">
                                            Không có dữ liệu
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer clearfix">
                        {{ $menuStats->links() }}
                    </div>

                </div>

            </div>
        </section>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {

                const customFrom = document.getElementById('customFrom');
                const customTo = document.getElementById('customTo');
                const customFilter = document.getElementById('customFilter');

                if (!customFrom || !customTo || !customFilter) return;

                // Khi user chỉnh ngày thủ công → chuyển sang custom
                customFrom.addEventListener('change', () => customFilter.value = 'custom');
                customTo.addEventListener('change', () => customFilter.value = 'custom');

                // Khi bấm các nút filter nhanh
                document.querySelectorAll('button[name="filter"]').forEach(button => {
                    button.addEventListener('click', function() {

                        const filterType = this.value;

                        // Nếu là custom (nút Áp dụng) thì không làm gì
                        if (filterType === 'custom') return;

                        const today = new Date();
                        let fromDate, toDate;

                        switch (filterType) {
                            case 'today':
                                fromDate = new Date(today);
                                toDate = new Date(today);
                                break;

                            case 'this_week':
                                const day = today.getDay();
                                const diff = today.getDate() - day + (day === 0 ? -6 : 1);
                                fromDate = new Date(today.getFullYear(), today.getMonth(), diff);
                                toDate = new Date(fromDate);
                                toDate.setDate(fromDate.getDate() + 6);
                                break;

                            case 'this_month':
                                fromDate = new Date(today.getFullYear(), today.getMonth(), 1);
                                toDate = new Date(today.getFullYear(), today.getMonth() + 1, 0);
                                break;

                            case 'this_year':
                                fromDate = new Date(today.getFullYear(), 0, 1);
                                toDate = new Date(today.getFullYear(), 11, 31);
                                break;
                        }

                        const formatDate = (date) => {
                            const y = date.getFullYear();
                            const m = String(date.getMonth() + 1).padStart(2, '0');
                            const d = String(date.getDate()).padStart(2, '0');
                            return `${y}-${m}-${d}`;
                        };

                        if (fromDate && toDate) {
                            customFrom.value = formatDate(fromDate);
                            customTo.value = formatDate(toDate);
                        }

                    });
                });
            });
        </script>
    @endpush
@endsection
