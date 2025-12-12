@extends('admin.layouts.app')

@section('title', 'Chi tiết Bài đọc')
@section('page-title', 'Chi tiết Bài đọc')

@section('content')
<div class="row mb-3">
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center">
            <a href="{{ route('admin.tests.passages.index', $test) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Quay lại
            </a>
            <div>
                <a href="{{ route('admin.tests.passages.create-question', [$test, $passage]) }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Thêm câu hỏi
                </a>
                <a href="{{ route('admin.tests.passages.edit', [$test, $passage]) }}" class="btn btn-warning">
                    <i class="fas fa-edit"></i> Sửa bài đọc
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5>{{ $passage->title ?: 'Bài đọc #' . $passage->id }}</h5>
            </div>
            <div class="card-body">
                <div class="passage-content" style="white-space: pre-wrap; line-height: 1.8;">
                    {{ $passage->content }}
                </div>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <h5>Câu hỏi ({{ $passage->questions->count() }} câu)</h5>
            </div>
            <div class="card-body">
                @if($passage->questions->count() > 0)
                    @foreach($passage->questions as $question)
                        <div class="card mb-3">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <h6>Câu {{ $question->order }}: {{ $question->question }}</h6>
                                        
                                        <div class="mt-2">
                                            @foreach($question->options as $key => $option)
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" 
                                                           disabled {{ $option === $question->correct_answer ? 'checked' : '' }}>
                                                    <label class="form-check-label {{ $option === $question->correct_answer ? 'text-success fw-bold' : '' }}">
                                                        {{ chr(65 + $key) }}. {{ $option }}
                                                        @if($option === $question->correct_answer)
                                                            <i class="fas fa-check text-success"></i>
                                                        @endif
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                        
                                        <div class="mt-2">
                                            <span class="badge bg-{{ $question->difficulty === 'easy' ? 'success' : ($question->difficulty === 'medium' ? 'warning' : 'danger') }}">
                                                {{ ucfirst($question->difficulty) }}
                                            </span>
                                        </div>
                                    </div>
                                    
                                    <div class="btn-group-vertical ms-2">
                                        <a href="{{ route('admin.tests.questions.edit', [$test, $question]) }}" 
                                           class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.tests.questions.destroy', [$test, $question]) }}" 
                                              method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger"
                                                    onclick="return confirm('Xóa câu hỏi này?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-question-circle fa-3x text-muted mb-3"></i>
                        <h6>Chưa có câu hỏi nào</h6>
                        <a href="{{ route('admin.tests.passages.create-question', [$test, $passage]) }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Thêm câu hỏi
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h6>Thông tin</h6>
            </div>
            <div class="card-body">
                <p><strong>Test:</strong> {{ $test->title }}</p>
                <p><strong>Thứ tự:</strong> {{ $passage->order }}</p>
                <p><strong>Số câu hỏi:</strong> {{ $passage->questions->count() }}</p>
                <p><strong>Độ dài:</strong> {{ str_word_count($passage->content) }} từ</p>
                <p><strong>Ngày tạo:</strong> {{ $passage->created_at->format('d/m/Y H:i') }}</p>
            </div>
        </div>
    </div>
</div>
@endsection