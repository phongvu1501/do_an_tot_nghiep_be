@extends('admin.layouts.main')

@section('noidung')
    <div class="content-wrapper">

        <div class="content-header">
            <div class="container-fluid">
                <h1 class="m-0">Thống kê Bình luận</h1>

                <form action="{{ route('admin.binhluanStatistics') }}" method="GET" class="mt-3">
                    <div class="btn-group">
                        @foreach (['today' => 'Hôm nay', 'this_week' => 'Tuần này', 'this_month' => 'Tháng này', 'this_year' => 'Năm nay'] as $key => $label)
                            <button type="submit" name="filter" value="{{ $key }}"
                                class="btn btn-sm {{ $filterType == $key ? 'btn-primary' : 'btn-outline-primary' }}">
                                {{ $label }}
                            </button>
                        @endforeach
                    </div>

                    <div class="form-inline mt-2">
                        <input type="date" name="from" value="{{ $from->format('Y-m-d') }}"
                            class="form-control form-control-sm mr-2">
                        <input type="date" name="to" value="{{ $to->format('Y-m-d') }}"
                            class="form-control form-control-sm mr-2">
                        <button class="btn btn-sm btn-secondary" name="filter" value="custom">
                            Áp dụng
                        </button>
                    </div>

                    <small class="text-muted mt-1 d-block">
                        {{ $filterLabel }}
                    </small>
                </form>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">

                <div class="row">
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-info">
                            <div class="inner">
                                <h3>{{ $totalComments }}</h3>
                                <p>Tổng bình luận</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-success">
                            <div class="inner">
                                <h3>{{ $approvedComments }}</h3>
                                <p>Đã duyệt</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-warning">
                            <div class="inner">
                                <h3>{{ $pendingComments }}</h3>
                                <p>Chờ duyệt</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-danger">
                            <div class="inner">
                                <h3>{{ number_format($avgRating, 1) }} ⭐</h3>
                                <p>Đánh giá TB</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Danh sách bình luận</h3>
                    </div>
                    <div class="card-body table-responsive p-0">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Người dùng</th>
                                    <th>Nội dung</th>
                                    <th>Số sao</th>
                                    <th>Ngày</th>
                                    <th>Trạng thái</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($comments as $c)
                                    <tr>
                                        <td>{{ $c->user->name ?? 'Ẩn danh' }}</td>
                                        <td>{{ $c->comment }}</td>
                                        <td>{{ $c->rating }} ⭐</td>
                                        <td>{{ $c->created_at->format('d/m/Y') }}</td>
                                        <td>
                                            @if ($c->status == 1)
                                                <span class="badge badge-success">Đã duyệt</span>
                                            @else
                                                <span class="badge badge-warning">Chờ duyệt</span>
                                            @endif
                                        </td>
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
                </div>

                <div class="card mt-3">
                    <div class="card-header">
                        <h3 class="card-title">Biểu đồ bình luận theo ngày</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="commentChart" height="100"></canvas>
                    </div>
                </div>

            </div>
        </section>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            new Chart(document.getElementById('commentChart'), {
                type: 'line',
                data: {
                    labels: @json($chartLabels),
                    datasets: [{
                            label: 'Tổng',
                            data: @json($chartTotal),
                            borderWidth: 2
                        },
                        {
                            label: 'Đã duyệt',
                            data: @json($chartApproved),
                            borderWidth: 2
                        },
                        {
                            label: 'Chờ duyệt',
                            data: @json($chartPending),
                            borderWidth: 2
                        }
                    ]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    }
                }
            });
        </script>
    @endpush
@endsection
