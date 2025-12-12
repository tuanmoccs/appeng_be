@extends('admin.layouts.app')

@section('title', 'Quản lý Bài đọc')
@section('page-title', 'Quản lý Bài đọc: ' . $test->title)

@section('content')
<div class="row mb-3">
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <a href="{{ route('admin.tests.show', $test) }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Quay lại Test
                </a>
            </div>
            <div>
                <a href="{{ route('admin.tests.passages.create', $test) }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Thêm Bài đọc
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5>Danh sách Bài đọc</h5>
            </div>
            <div class="card-body">
                @if($passages->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>STT</th>
                                    <th>Tiêu đề</th>
                                    <th>Nội dung</th>
                                    <th>Số câu hỏi</th>
                                    <th>Thứ tự</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($passages as $passage)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $passage->title ?: 'Không có tiêu đề' }}</td>
                                        <td>{{ Str::limit($passage->content, 100) }}</td>
                                        <td>
                                            <span class="badge bg-info">{{ $passage->questions_count }} câu</span>
                                        </td>
                                        <td>{{ $passage->order }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.tests.passages.show', [$test, $passage]) }}" 
                                                   class="btn btn-sm btn-info" title="Xem chi tiết">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.tests.passages.edit', [$test, $passage]) }}" 
                                                   class="btn btn-sm btn-warning" title="Sửa">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('admin.tests.passages.destroy', [$test, $passage]) }}" 
                                                      method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" 
                                                            onclick="return confirm('Xóa bài đọc này và tất cả câu hỏi liên quan?')"
                                                            title="Xóa">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $passages->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-book-open fa-3x text-muted mb-3"></i>
                        <h5>Chưa có bài đọc nào</h5>
                        <p class="text-muted">Hãy thêm bài đọc đầu tiên cho test này.</p>
                        <a href="{{ route('admin.tests.passages.create', $test) }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Thêm Bài đọc
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection