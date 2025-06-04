@extends('admin.layouts.app')

@section('title', 'Tạo Bài học')
@section('page-title', 'Tạo Bài học')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5>Thông tin Bài học</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.lessons.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="title" class="form-label">Tiêu đề <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror" 
                               id="title" name="title" value="{{ old('title') }}" required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Mô tả <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" name="description" rows="3" required>{{ old('description') }}</textarea>
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
                                    <option value="beginner" {{ old('level') == 'beginner' ? 'selected' : '' }}>Beginner</option>
                                    <option value="intermediate" {{ old('level') == 'intermediate' ? 'selected' : '' }}>Intermediate</option>
                                    <option value="advanced" {{ old('level') == 'advanced' ? 'selected' : '' }}>Advanced</option>
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
                                       id="duration" name="duration" value="{{ old('duration') }}" min="1" required>
                                @error('duration')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="order" class="form-label">Thứ tự <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('order') is-invalid @enderror" 
                                       id="order" name="order" value="{{ old('order', 1) }}" min="1" required>
                                @error('order')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="content" class="form-label">Nội dung (JSON) <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('content') is-invalid @enderror" 
                                  id="content" name="content" rows="10" required>{{ old('content', '{"sections": [{"title": "Section 1", "items": [{"word": "example", "meaning": "ví dụ", "example": "This is an example."}]}]}') }}</textarea>
                        <div class="form-text">Nhập nội dung bài học dưới dạng JSON. Xem hướng dẫn bên phải.</div>
                        @error('content')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.lessons.index') }}" class="btn btn-secondary">Hủy</a>
                        <button type="submit" class="btn btn-primary">Tạo Bài học</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h6>Hướng dẫn JSON Content</h6>
            </div>
            <div class="card-body">
                <p><strong>Cấu trúc JSON:</strong></p>
                <pre><code>{
  "sections": [
    {
      "title": "Tên section",
      "items": [
        {
          "word": "từ vựng",
          "meaning": "nghĩa",
          "example": "câu ví dụ"
        }
      ]
    }
  ]
}</code></pre>
                
                <hr>
                
                <p><strong>Lưu ý:</strong></p>
                <ul class="list-unstyled">
                    <li>• JSON phải hợp lệ</li>
                    <li>• Mỗi section có title và items</li>
                    <li>• Mỗi item có word, meaning, example</li>
                    <li>• Có thể có nhiều sections</li>
                </ul>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <h6>Thông tin</h6>
            </div>
            <div class="card-body">
                <p><strong>Cấp độ:</strong></p>
                <ul class="list-unstyled">
                    <li><span class="badge bg-success">Beginner</span> - Người mới bắt đầu</li>
                    <li><span class="badge bg-warning">Intermediate</span> - Trung cấp</li>
                    <li><span class="badge bg-danger">Advanced</span> - Nâng cao</li>
                </ul>
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
});
</script>
@endsection
@endsection
