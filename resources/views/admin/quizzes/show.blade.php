@extends('admin.layouts.app')

@section('title', 'Chi tiết Quiz')
@section('page-title', 'Chi tiết Quiz')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4>Chi tiết Quiz: {{ $quiz->title }}</h4>
    <div>
        <a href="{{ route('admin.quizzes.edit', $quiz) }}" class="btn btn-primary">
            <i class="fas fa-edit"></i> Chỉnh sửa
        </a>
        <a href="{{ route('admin.quizzes.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Quay lại
        </a>
    </div>
</div>

<!-- Thông tin cơ bản -->
<div class="row mb-4">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-info-circle"></i> Thông tin Quiz</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>ID:</strong> {{ $quiz->id }}</p>
                        <p><strong>Tiêu đề:</strong> {{ $quiz->title }}</p>
                        <p><strong>Bài học:</strong> {{ $quiz->lesson ? $quiz->lesson->title : 'N/A' }}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Số câu hỏi:</strong> {{ $quiz->questions ? $quiz->questions->count() : 0 }}</p>
                        <p><strong>Ngày tạo:</strong> {{ $quiz->created_at->format('d/m/Y H:i') }}</p>
                        <p><strong>Cập nhật lần cuối:</strong> {{ $quiz->updated_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
                @if($quiz->description)
                <div class="mt-3">
                    <strong>Mô tả:</strong>
                    <p class="mt-2">{{ $quiz->description }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-chart-bar"></i> Thống kê</h5>
            </div>
            <div class="card-body">
                <div class="text-center">
                    <div class="mb-3">
                        <h3 class="text-primary">{{ $totalAttempts }}</h3>
                        <small class="text-muted">Lượt làm bài</small>
                    </div>
                    <div class="mb-3">
                        <h3 class="text-success">{{ $avgScore ? number_format($avgScore, 1) : '0' }}%</h3>
                        <small class="text-muted">Điểm trung bình</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Danh sách câu hỏi -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5><i class="fas fa-question-circle"></i> Danh sách câu hỏi</h5>
        <a href="{{ route('admin.quizzes.questions', $quiz) }}" class="btn btn-sm btn-primary">
            <i class="fas fa-cog"></i> Quản lý câu hỏi
        </a>
    </div>
    <div class="card-body">
        @if($quiz->questions && $quiz->questions->count() > 0)
        <div class="accordion" id="questionsAccordion">
            @foreach($quiz->questions as $index => $question)
            <div class="accordion-item">
                <h2 class="accordion-header" id="heading{{ $question->id }}">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" 
                            data-bs-target="#collapse{{ $question->id }}" aria-expanded="false" 
                            aria-controls="collapse{{ $question->id }}">
                        <strong>Câu {{ $index + 1 }}:</strong> &nbsp; {{ Str::limit($question->question, 80) }}
                    </button>
                </h2>
                <div id="collapse{{ $question->id }}" class="accordion-collapse collapse" 
                     aria-labelledby="heading{{ $question->id }}" data-bs-parent="#questionsAccordion">
                    <div class="accordion-body">
                        <div class="mb-3">
                            <strong>Câu hỏi:</strong>
                            <p>{{ $question->question }}</p>
                        </div>
                        
                        <div class="mb-3">
                            <strong>Các lựa chọn:</strong>
                            <ul class="list-group list-group-flush">
                                @if($question->options && is_array($question->options))
                                    @foreach($question->options as $option)
                                    <li class="list-group-item d-flex justify-content-between align-items-center {{ $option == $question->correct_answer ? 'list-group-item-success' : '' }}">
                                        {{ $option }}
                                        @if($option == $question->correct_answer)
                                            <span class="badge bg-success">Đáp án đúng</span>
                                        @endif
                                    </li>
                                    @endforeach
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-4">
            <i class="fas fa-question-circle fa-3x text-muted mb-3"></i>
            <p class="text-muted">Chưa có câu hỏi nào được thêm vào quiz này.</p>
            <a href="{{ route('admin.quizzes.questions', $quiz) }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Thêm câu hỏi đầu tiên
            </a>
        </div>
        @endif
    </div>
</div>
@endsection