@extends('admin.layouts.app')

@section('title', 'Chi tiết Bài học')
@section('page-title', 'Chi tiết Bài học')

@section('content')
<div class="row mb-3">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <h3>{{ $lesson->title }}</h3>
                        <p class="text-muted">{{ $lesson->description }}</p>
                        
                        <div class="row mt-3">
                            <div class="col-md-3">
                                <strong>Cấp độ:</strong><br>
                                <span class="badge bg-{{ $lesson->level == 'beginner' ? 'success' : ($lesson->level == 'intermediate' ? 'warning' : 'danger') }} fs-6">
                                    {{ ucfirst($lesson->level) }}
                                </span>
                            </div>
                            <div class="col-md-3">
                                <strong>Thời gian:</strong><br>
                                {{ $lesson->duration }} phút
                            </div>
                            <div class="col-md-3">
                                <strong>Thứ tự:</strong><br>
                                <span class="badge bg-secondary fs-6">{{ $lesson->order }}</span>
                            </div>
                            <div class="col-md-3">
                                <strong>Ngày tạo:</strong><br>
                                {{ $lesson->created_at->format('d/m/Y') }}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 text-end">
                        <div class="btn-group" role="group">
                            <a href="{{ route('admin.lessons.edit', $lesson) }}" class="btn btn-primary">
                                <i class="fas fa-edit"></i> Chỉnh sửa
                            </a>
                            <form action="{{ route('admin.lessons.destroy', $lesson) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger" 
                                        onclick="return confirm('Bạn có chắc chắn muốn xóa bài học này?')">
                                    <i class="fas fa-trash"></i> Xóa
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-3 mb-3">
        <div class="card text-white bg-primary">
            <div class="card-body text-center">
                <h3>{{ $lesson->words()->count() }}</h3>
                <p class="mb-0">Từ vựng</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card text-white bg-success">
            <div class="card-body text-center">
                <h3>{{ $lesson->quizzes()->count() }}</h3>
                <p class="mb-0">Quiz</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card text-white bg-info">
            <div class="card-body text-center">
                <h3>{{ $lesson->progress()->where('is_completed', true)->count() }}</h3>
                <p class="mb-0">Đã hoàn thành</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card text-white bg-warning">
            <div class="card-body text-center">
                @php
                    $avgProgress = $lesson->progress()->avg('progress_percentage') ?? 0;
                @endphp
                <h3>{{ round($avgProgress) }}%</h3>
                <p class="mb-0">Tiến độ TB</p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8 mb-3">
        <div class="card">
            <div class="card-header">
                <h5>Nội dung Bài học</h5>
            </div>
            <div class="card-body">
                @if($lesson->content && isset($lesson->content['sections']))
                    @foreach($lesson->content['sections'] as $index => $section)
                        <div class="mb-4">
                            <h6 class="text-primary">
                                <i class="fas fa-book-open"></i> 
                                {{ $section['title'] ?? 'Section ' . ($index + 1) }}
                            </h6>
                            
                            @if(isset($section['items']) && is_array($section['items']))
                                <div class="row">
                                    @foreach($section['items'] as $item)
                                        <div class="col-md-6 mb-3">
                                            <div class="card border-left-primary">
                                                <div class="card-body p-3">
                                                    <h6 class="card-title mb-1">
                                                        {{ $item['word'] ?? 'N/A' }}
                                                    </h6>
                                                    <p class="card-text mb-1">
                                                        <strong>Nghĩa:</strong> {{ $item['meaning'] ?? 'N/A' }}
                                                    </p>
                                                    @if(isset($item['example']))
                                                        <p class="card-text text-muted small mb-0">
                                                            <em>"{{ $item['example'] }}"</em>
                                                        </p>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-muted">Không có nội dung trong section này.</p>
                            @endif
                        </div>
                        
                        @if(!$loop->last)
                            <hr>
                        @endif
                    @endforeach
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                        <h6>Không có nội dung</h6>
                        <p class="text-muted">Bài học này chưa có nội dung hoặc nội dung không hợp lệ.</p>
                        <a href="{{ route('admin.lessons.edit', $lesson) }}" class="btn btn-primary">
                            <i class="fas fa-edit"></i> Chỉnh sửa nội dung
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-3">
        <div class="card">
            <div class="card-header">
                <h5>Từ vựng liên quan</h5>
            </div>
            <div class="card-body">
                @if($lesson->words()->count() > 0)
                    <div class="list-group">
                        @foreach($lesson->words()->take(10)->get() as $word)
                            <div class="list-group-item">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1">{{ $word->word }}</h6>
                                </div>
                                <p class="mb-1">{{ $word->translation }}</p>
                                @if($word->pronunciation)
                                    <small class="text-muted">{{ $word->pronunciation }}</small>
                                @endif
                            </div>
                        @endforeach
                    </div>
                    
                    @if($lesson->words()->count() > 10)
                        <div class="mt-3 text-center">
                            <a href="{{ route('admin.words.index', ['lesson_id' => $lesson->id]) }}" class="btn btn-outline-primary">
                                Xem tất cả {{ $lesson->words()->count() }} từ vựng
                            </a>
                        </div>
                    @endif
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-spell-check fa-3x text-muted mb-3"></i>
                        <h6>Chưa có từ vựng</h6>
                        <p class="text-muted">Chưa có từ vựng nào được gán cho bài học này.</p>
                        <a href="{{ route('admin.words.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Thêm từ vựng
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <h5>Quiz liên quan</h5>
            </div>
            <div class="card-body">
                @if($lesson->quizzes()->count() > 0)
                    <div class="list-group">
                        @foreach($lesson->quizzes as $quiz)
                            <div class="list-group-item">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1">{{ $quiz->title }}</h6>
                                    <small>{{ $quiz->questions()->count() }} câu hỏi</small>
                                </div>
                                <p class="mb-1">{{ $quiz->description ?: 'Không có mô tả' }}</p>
                                <div class="mt-2">
                                    <a href="{{ route('admin.quizzes.show', $quiz) }}" class="btn btn-sm btn-outline-primary">
                                        Xem chi tiết
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-question-circle fa-3x text-muted mb-3"></i>
                        <h6>Chưa có quiz</h6>
                        <p class="text-muted">Chưa có quiz nào được tạo cho bài học này.</p>
                        <a href="{{ route('admin.quizzes.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Tạo quiz
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <h5>Tiến độ học tập</h5>
            </div>
            <div class="card-body">
                @if($lesson->progress()->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Người dùng</th>
                                    <th>Tiến độ</th>
                                    <th>Trạng thái</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($lesson->progress()->with('user')->latest()->take(5)->get() as $progress)
                                    <tr>
                                        <td>{{ $progress->user->name }}</td>
                                        <td>{{ $progress->progress_percentage }}%</td>
                                        <td>
                                            <span class="badge bg-{{ $progress->is_completed ? 'success' : 'warning' }}">
                                                {{ $progress->is_completed ? 'Hoàn thành' : 'Đang học' }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-chart-line fa-3x text-muted mb-3"></i>
                        <h6>Chưa có tiến độ</h6>
                        <p class="text-muted">Chưa có người dùng nào học bài này.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5>Thông tin chi tiết</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table">
                            <tr>
                                <th>ID:</th>
                                <td>{{ $lesson->id }}</td>
                            </tr>
                            <tr>
                                <th>Tiêu đề:</th>
                                <td>{{ $lesson->title }}</td>
                            </tr>
                            <tr>
                                <th>Cấp độ:</th>
                                <td>{{ ucfirst($lesson->level) }}</td>
                            </tr>
                            <tr>
                                <th>Thời gian:</th>
                                <td>{{ $lesson->duration }} phút</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table">
                            <tr>
                                <th>Thứ tự:</th>
                                <td>{{ $lesson->order }}</td>
                            </tr>
                            <tr>
                                <th>Ngày tạo:</th>
                                <td>{{ $lesson->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                            <tr>
                                <th>Cập nhật lần cuối:</th>
                                <td>{{ $lesson->updated_at->format('d/m/Y H:i') }}</td>
                            </tr>
                            <tr>
                                <th>Số sections:</th>
                                <td>{{ isset($lesson->content['sections']) ? count($lesson->content['sections']) : 0 }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<style>
.border-left-primary {
    border-left: 4px solid #007bff !important;
}
</style>
@endsection
