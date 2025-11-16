@extends('admin.layouts.main')

@section('noidung')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">{{ $dashboard }}</h1>
                </div>

                <form action="{{ route('admin.dashboard') }}" method="GET" class="form-inline">
                    <div class="form-group mb-2">
                        <label>Từ: </label>
                        <input type="date" name="from" value="{{ $from->format('Y-m-d') }}" class="form-control ml-2">
                    </div>
                    <div class="form-group mb-2 ml-3">
                        <label>Đến: </label>
                        <input type="date" name="to" value="{{ $to->format('Y-m-d') }}" class="form-control ml-2">
                    </div>
                    <button type="submit" class="btn btn-primary mb-2 ml-3">Filter</button>
                </form>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3>{{ $totalReservations }}</h3>
                            <p>Tổng đơn đặt</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-bag"></i>
                        </div>
                        <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3>{{ $totalCancelled }}</h3>
                            <p>Tổng đơn hủy</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-stats-bars"></i>
                        </div>
                        <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3>{{ $totalRevenue }}</h3>
                            <p>Doanh số</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-person-add"></i>
                        </div>
                        <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3>{{ $newUsers }}</h3>
                            <p>Tài khoản mới</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-pie-graph"></i>
                        </div>
                        <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
