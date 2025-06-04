@extends('admin.layouts.app')

@section('title', 'Chỉnh sửa Bài học')
@section('page-title', 'Chỉnh sửa Bài học')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5>Thông tin Bài học</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.lessons.update', $lesson) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label for="title" class="form-label">Tiêu đề <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror" 
                               id="title" name="title" value="{{ old('title', $lesson->title) }}" required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Mô tả <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" name="description" rows="3" required>{{ old('description', $lesson->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="level" class="form-label">Cấp độ <span class="text-danger">*</span></label>
                                <select class="form-select @error('level') is-invalid @enderror" id="level" name="level" required>
                                    <option value="">Chọn cấp độ</option>
                                    <option value="beginner" {{ old('level', $lesson->level) == 'beginner' ? 'selected' : '' }}>Beginner</option>
                                    <option value="intermediate" {{ old('level', $lesson->level) == 'intermediate' ? 'selected' : '' }}>Intermediate</option>
                                    <option value="advanced" {{ old('level', $lesson->level) == 'advanced' ? 'selected' : '' }}>Advanced</option>
                                </select>
                                @error('level')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="duration" class="form-label">Thời gian (phút) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('duration') is-invalid @enderror" 
                                       id="duration" name="duration" value="{{ old('duration', $lesson->duration) }}" min="1" required>
                                @error('duration')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="order" class="form-label">Thứ tự <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('order') is-invalid @enderror" 
                                       id="order" name="order" value="{{ old('order', $lesson->order) }}" min="1" required>
                                @error('order')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="content" class="form-label">Nội dung (JSON) <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('content') is-invalid @enderror" 
                                  id="content" name="content" rows="15" required>{{ old('content', json_encode($lesson->content, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) }}</textarea>
                        <div class="form-text">Nhập nội dung bài học dưới dạng JSON. Xem hướng dẫn bên phải.</div>
                        @error('content')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.lessons.index') }}" class="btn btn-secondary">Hủy</a>
                        <button type="submit" class="btn btn-primary">Cập nhật</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h6>Thông tin</h6>
            </div>
            <div class="card-body">
                <p><strong>ID:</strong> {{ $lesson->id }}</p>
                <p><strong>Từ vựng liên quan:</strong> {{ $lesson->words()->count() }}</p>
                <p><strong>Quiz liên quan:</strong> {{ $lesson->quizzes()->count() }}</p>
                <p><strong>Ngày tạo:</strong> {{ $lesson->created_at->format('d/m/Y H:i') }}</p>
                <p><strong>Cập nhật lần cuối:</strong> {{ $lesson->updated_at->format('d/m/Y H:i') }}</p>
                
                <hr>
                
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.lessons.show', $lesson) }}" class="btn btn-outline-info">
                        <i class="fas fa-eye"></i> Xem chi tiết
                    </a>
                    <a href="{{ route('admin.words.index', ['lesson_id' => $lesson->id]) }}" class="btn btn-outline-primary">
                        <i class="fas fa-spell-check"></i> Quản lý từ vựng
                    </a>
                    <a href="{{ route('admin.quizzes.index', ['lesson_id' => $lesson->id]) }}" class="btn btn-outline-warning">
                        <i class="fas fa-question-circle"></i> Quản lý quiz
                    </a>
                </div>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <h6>Hướng dẫn JSON</h6>
            </div>
            <div class="card-body">
                <p><strong>Cấu trúc cơ bản:</strong></p>
                <pre><code>{
  "sections": [
    {
      "title": "Section name",
      "items": [
        {
          "word": "vocabulary",
          "meaning": "từ vựng",
          "example": "Example sentence"
        }
      ]
    }
  ]
}</code></pre>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <h6>Hành động nguy hiểm</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.lessons.destroy', $lesson) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger w-100" 
                            onclick="return confirm('Bạn có chắc chắn muốn xóa bài học này? Tất cả từ vựng và quiz liên quan sẽ bị ảnh hưởng!')">
                        <i class="fas fa-trash"></i> Xóa bài học này
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Validate JSON on input
    const contentTextarea = document.getElementById('content');
    
    contentTextarea.addEventListener('blur', function() {
        try {
            JSON.parse(this.value);
            this.classList.remove('is-invalid');
            this.classList.add('is-valid');
        } catch (e) {
            this.classList.remove('is-valid');
            this.classList.add('is-invalid');
        }
    });
    
    // Format JSON button
    const formatBtn = document.createElement('button');
    formatBtn.type = 'button';
    formatBtn.className = 'btn btn-sm btn-outline-secondary mt-2';
    formatBtn.innerHTML = '<i class="fas fa-code"></i> Format JSON';
    formatBtn.onclick = function() {
        try {
            const parsed = JSON.parse(contentTextarea.value);
            contentTextarea.value = JSON.stringify(parsed, null, 2);
            contentTextarea.classList.remove('is-invalid');
            contentTextarea.classList.add('is-valid');
        } catch (e) {
            alert('JSON không hợp lệ!');
        }
    };
    
    contentTextarea.parentNode.appendChild(formatBtn);
});
</script>
@endsection
@endsection
