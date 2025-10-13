@extends('admin.layouts.main')

@section('noidung')
    <div class="content-wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">{{ $banAn }}</h3>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <table id="example2" class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>STT</th>
                                        <th>Khối lớp</th>
                                        <th>Ngày tạo</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- @foreach ($khoiLops as $index => $khoilop)
                                        <tr>
                                            <td>{{ $loop->iteration + ($khoiLops->currentPage() - 1) * $khoiLops->perPage() }}
                                            </td>
                                            <td>{{ $khoilop->ten_khoi }}</td>
                                            <td>{{ $khoilop->created_at->format('d/m/Y') }}</td>
                                            <td>
                                                <a href="{{ route('admin.khoiLop.show', $khoilop->id) }}" class="btn btn-info btn-sm">Chi tiết</a>

                                            </td>
                                        </tr>
                                    @endforeach --}}
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>STT</th>
                                        <th>Khối lớp</th>
                                        <th>Ngày tạo</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </tfoot>
                            </table>

                            <!-- PHÂN TRANG -->
                            <div class="mt-3 d-flex justify-content-center">
                                {{-- {{ $khoiLops->links('pagination::bootstrap-4') }} --}}
                            </div>
                        </div>
                        <!-- /.card-body -->
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
