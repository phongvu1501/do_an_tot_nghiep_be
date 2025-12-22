@extends('admin.layouts.main')

@section('noidung')
<div class="content-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">

                <div class="card">

                    {{-- Header --}}
                    <div class="card-header bg-white">
                        <h3 class="mb-0 font-weight-bold">Quản lý bình luận</h3>
                    </div>

                    {{-- Body --}}
                    <div class="card-body">

                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show">
                                {{ session('success') }}
                                <button type="button" class="close" data-dismiss="alert">
                                    <span>&times;</span>
                                </button>
                            </div>
                        @endif

                        <table class="table table-bordered table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th width="5%">ID</th>
                                    <th>Người dùng</th>
                                    <th>Reservation</th>
                                    <th width="10%">Rating</th>
                                    <th>Nội dung</th>
                                    <th width="15%">Ngày tạo</th>
                                    <th width="10%">Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($reviews as $review)
                                    <tr>
                                        <td>{{ $review->id }}</td>
                                        <td>{{ $review->user->name ?? 'N/A' }}</td>
                                        <td>#{{ $review->reservation_id }}</td>
                                        <td>{{ $review->rating }} ⭐</td>
                                        <td>{{ $review->comment }}</td>
                                        <td>{{ $review->created_at->format('d/m/Y H:i') }}</td>
                                        <td>
                                            <form method="POST"
                                                  action="{{ route('admin.reviews.destroy', $review) }}"
                                                  onsubmit="return confirm('Bạn có chắc muốn xóa bình luận này?')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-danger btn-sm">
                                                    Xóa
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">
                                            Chưa có bình luận nào
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>

                        <div class="mt-3">
                            {{ $reviews->links() }}
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
