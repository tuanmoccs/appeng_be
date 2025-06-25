@extends('admin.layouts.app')

@section('title', 'Quản lý Câu hỏi')
@section('page-title', 'Quản lý Câu hỏi - ' . $quiz->title)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4>Câu hỏi cho Quiz: {{ $quiz->title }}</h4>
        <p class="text-muted mb-0">Bài học: {{ $quiz->lesson->title }}</p>
    </div>
    <a href="{{ route('admin.quizzes.questions.create', $quiz) }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Thêm Câu hỏi
    </a>
</div>

<div class="row mb-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="text-center">
                            <h5 class="text-primary">{{ $questions->total() }}</h5>
                            <p class="mb-0">Tổng câu hỏi</p>
                        </div>
                    </div>
                    <div class="col-md-9">
                        <p><strong>Mô tả:</strong> {{ $quiz->description ?: 'Không có mô tả' }}</p>
                        <div class="mt-2">
                            <a href="{{ route('admin.quizzes.edit', $quiz) }}" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-edit"></i> Chỉnh sửa Quiz
                            </a>
                            <a href="{{ route('admin.quizzes.index') }}" class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-arrow-left"></i> Quay lại danh sách
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        @if($questions->count() > 0)
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th width="5%">ID</th>
                            <th width="40%">Câu hỏi</th>
                            <th width="25%">Lựa chọn</th>
                            <th width="15%">Đáp án đúng</th>
                            <th width="15%">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($questions as $question)
                        <tr>
                            <td>{{ $question->id }}</td>
                            <td>{{ Str::limit($question->question, 100) }}</td>
                            <td>
                                @php
                                    $options = is_array($question->options) ? $question->options : json_decode($question->options, true);
                                @endphp

                                @if($options)
                                    @foreach($options as $index => $option)
                                        <span class="badge bg-secondary me-1">
                                            {{ chr(65 + $index) }}. {{ \Illuminate\Support\Str::limit($option, 20) }}
                                        </span>
                                    @endforeach
                                @else
                                    <span class="text-danger">Không có lựa chọn hợp lệ</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-success">{{ $question->correct_answer }}</span>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.quizzes.questions.edit', [$quiz, $question]) }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.quizzes.questions.destroy', [$quiz, $question]) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" 
                                                onclick="return confirm('Bạn có chắc chắn muốn xóa câu hỏi này?')">
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

            {{ $questions->links() }}
        @else
            <div class="text-center py-5">
                <i class="fas fa-question-circle fa-3x text-muted mb-3"></i>
                <h5>Chưa có câu hỏi nào</h5>
                <p class="text-muted">Hãy thêm câu hỏi đầu tiên cho quiz này.</p>
                <a href="{{ route('admin.quizzes.questions.create', $quiz) }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Thêm Câu hỏi
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
