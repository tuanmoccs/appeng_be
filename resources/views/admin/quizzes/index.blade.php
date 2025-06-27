@extends('admin.layouts.app')

@section('title', 'Quản lý Quiz')
@section('page-title', 'Quản lý Quiz')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4>Danh sách Quiz</h4>
    <a href="{{ route('admin.quizzes.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Thêm Quiz
    </a>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('admin.quizzes.index') }}" method="GET" class="row g-3">
            <div class="col-md-6">
                <label for="lesson_id" class="form-label">Lọc theo bài học</label>
                <select class="form-select" id="lesson_id" name="lesson_id">
                    <option value="">Tất cả bài học</option>
                    @foreach($lessons as $lesson)
                        <option value="{{ $lesson->id }}" {{ request('lesson_id') == $lesson->id ? 'selected' : '' }}>
                            {{ $lesson->title }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2">
                    <i class="fas fa-filter"></i> Lọc
                </button>
                <a href="{{ route('admin.quizzes.index') }}" class="btn btn-secondary">
                    <i class="fas fa-sync"></i> Reset
                </a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tiêu đề</th>
                        <th>Bài học</th>
                        <th>Số câu hỏi</th>
                        <th>Ngày tạo</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($quizzes as $quiz)
                    <tr>
                        <td>{{ $quiz->id }}</td>
                        <td>{{ $quiz->title }}</td>
                        <td>{{ $quiz->lesson ? $quiz->lesson->title : 'N/A' }}</td>
                        <td>{{ $quiz->questions_count }}</td>
                        <td>{{ $quiz->created_at->format('d/m/Y') }}</td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('admin.quizzes.show', $quiz) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.quizzes.questions', $quiz) }}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-question"></i>
                                </a>
                                {{-- <a href="{{ route('admin.quizzes.edit', $quiz) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-edit"></i>
                                </a> --}}
                                <form action="{{ route('admin.quizzes.destroy', $quiz) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" 
                                            onclick="return confirm('Bạn có chắc chắn muốn xóa quiz này?')">
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

        {{ $quizzes->appends(request()->query())->links('pagination::bootstrap-4', ['class' => 'pagination pagination-sm']) }}
    </div>
</div>
@endsection
