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
                                <h3>{{ $commentsWithText }}</h3>
                                <p>Bình luận có nội dung</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-warning">
                            <div class="inner">
                                <h3>{{ $totalComments - $commentsWithText }}</h3>
                                <p>Chỉ đánh giá sao</p>
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
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($comments as $c)
                                    <tr>
                                        <td>
                                            <div>{{ $c->user->name ?? 'Ẩn danh' }}</div>
                                            @if($c->user && $c->user->phone)
                                                <small class="text-muted">{{ $c->user->phone }}</small>
                                            @else
                                                <small class="text-muted">Chưa có SĐT</small>
                                            @endif
                                        </td>
                                        <td>
                                            @if($c->comment)
                                                {{ $c->comment }}
                                            @else
                                                <span class="text-muted">Không có nội dung</span>
                                            @endif
                                        </td>
                                        <td>
                                            @for($i = 1; $i <= 5; $i++)
                                                @if($i <= $c->rating)
                                                    <span class="text-warning">⭐</span>
                                                @else
                                                    <span class="text-muted">☆</span>
                                                @endif
                                            @endfor
                                            <span class="ml-1">({{ $c->rating }}/5)</span>
                                        </td>
                                        <td>{{ $c->created_at->format('d/m/Y H:i') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">
                                            Không có dữ liệu
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        
                        <div class="card-footer">
                            {{ $comments->links() }}
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Biểu đồ bình luận theo ngày</h3>
                            </div>
                            <div class="card-body">
                                <canvas id="commentChart" height="100"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Thống kê theo sao</h3>
                            </div>
                            <div class="card-body">
                                @for($i = 5; $i >= 1; $i--)
                                    <div class="mb-2">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span>
                                                @for($j = 1; $j <= 5; $j++)
                                                    @if($j <= $i)
                                                        <span class="text-warning">⭐</span>
                                                    @else
                                                        <span class="text-muted">☆</span>
                                                    @endif
                                                @endfor
                                            </span>
                                            <strong>{{ $ratingStats[$i] ?? 0 }}</strong>
                                        </div>
                                        @if(isset($ratingStats[$i]) && $totalComments > 0)
                                            <div class="progress" style="height: 8px;">
                                                <div class="progress-bar bg-warning" role="progressbar" 
                                                     style="width: {{ ($ratingStats[$i] / $totalComments) * 100 }}%"></div>
                                            </div>
                                        @endif
                                    </div>
                                @endfor
                            </div>
                        </div>
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
                        label: 'Số bình luận',
                        data: @json($chartTotal),
                        borderColor: 'rgb(75, 192, 192)',
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        borderWidth: 2,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: true
                        }
                    },
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

