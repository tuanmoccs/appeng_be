@extends('admin.layouts.app')

@section('title', 'Quản lý Bài học')
@section('page-title', 'Quản lý Bài học')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4>Danh sách Bài học</h4>
    <a href="{{ route('admin.lessons.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Thêm Bài học
    </a>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Thứ tự</th>
                        <th>Tiêu đề</th>
                        <th>Cấp độ</th>
                        <th>Thời gian</th>
                        <th>Từ vựng</th>
                        <th>Quiz</th>
                        <th>Ngày tạo</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($lessons as $lesson)
                    <tr>
                        <td>{{ $lesson->id }}</td>
                        <td>
                            <span class="badge bg-secondary">{{ $lesson->order }}</span>
                        </td>
                        <td>{{ $lesson->title }}</td>
                        <td>
                            <span class="badge bg-{{ $lesson->level == 'beginner' ? 'success' : ($lesson->level == 'intermediate' ? 'warning' : 'danger') }}">
                                {{ ucfirst($lesson->level) }}
                            </span>
                        </td>
                        <td>{{ $lesson->duration }} phút</td>
                        <td>{{ $lesson->words()->count() }}</td>
                        <td>{{ $lesson->quizzes()->count() }}</td>
                        <td>{{ $lesson->created_at->format('d/m/Y') }}</td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('admin.lessons.show', $lesson) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.lessons.edit', $lesson) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.lessons.destroy', $lesson) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" 
                                            onclick="return confirm('Bạn có chắc chắn muốn xóa bài học này? Tất cả từ vựng và quiz liên quan sẽ bị ảnh hưởng!')">
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

        {{ $lessons->links() }}
    </div>
</div>
@endsection
