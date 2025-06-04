@extends('admin.layouts.app')

@section('title', 'Quản lý Câu hỏi Test')
@section('page-title', 'Quản lý Câu hỏi - ' . $test->title)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4>Câu hỏi cho Test: {{ $test->title }}</h4>
        <p class="text-muted mb-0">
            Loại: {{ ucfirst($test->type) }} | 
            Hiện tại: {{ $questions->total() }}/{{ $test->total_questions }} câu hỏi
        </p>
    </div>
    <a href="{{ route('admin.tests.questions.create', $test) }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Thêm Câu hỏi
    </a>
</div>

<div class="row mb-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-2">
                        <div class="text-center">
                            <h5 class="text-primary">{{ $questions->total() }}</h5>
                            <p class="mb-0">Tổng câu hỏi</p>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="text-center">
                            <h5 class="text-success">{{ $test->questions()->where('difficulty', 'easy')->count() }}</h5>
                            <p class="mb-0">Dễ</p>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="text-center">
                            <h5 class="text-warning">{{ $test->questions()->where('difficulty', 'medium')->count() }}</h5>
                            <p class="mb-0">Trung bình</p>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="text-center">
                            <h5 class="text-danger">{{ $test->questions()->where('difficulty', 'hard')->count() }}</h5>
                            <p class="mb-0">Khó</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mt-2">
                            <a href="{{ route('admin.tests.edit', $test) }}" class="btn btn-sm btn-outline-primary me-2">
                                <i class="fas fa-edit"></i> Chỉnh sửa Test
                            </a>
                            <a href="{{ route('admin.tests.show', $test) }}" class="btn btn-sm btn-outline-info me-2">
                                <i class="fas fa-eye"></i> Xem chi tiết
                            </a>
                            <a href="{{ route('admin.tests.index') }}" class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-arrow-left"></i> Quay lại
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
                            <th width="5%">Thứ tự</th>
                            <th width="35%">Câu hỏi</th>
                            <th width="20%">Lựa chọn</th>
                            <th width="10%">Đáp án đúng</th>
                            <th width="10%">Độ khó</th>
                            <th width="15%">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($questions as $question)
                        <tr>
                            <td>{{ $question->id }}</td>
                            <td>{{ $question->order }}</td>
                            <td>{{ Str::limit($question->question, 80) }}</td>
                            <td>
                                @foreach($question->options as $index => $option)
                                    <span class="badge bg-secondary me-1">{{ chr(65 + $index) }}. {{ Str::limit($option, 15) }}</span>
                                @endforeach
                            </td>
                            <td>
                                <span class="badge bg-success">{{ $question->correct_answer }}</span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $question->difficulty == 'easy' ? 'success' : ($question->difficulty == 'medium' ? 'warning' : 'danger') }}">
                                    {{ ucfirst($question->difficulty) }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.tests.questions.edit', [$test, $question]) }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.tests.questions.destroy', [$test, $question]) }}" method="POST" class="d-inline">
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
                <p class="text-muted">Hãy thêm câu hỏi đầu tiên cho bài test này.</p>
                <a href="{{ route('admin.tests.questions.create', $test) }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Thêm Câu hỏi
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
