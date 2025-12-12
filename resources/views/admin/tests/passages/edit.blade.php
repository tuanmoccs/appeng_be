@extends('admin.layouts.app')

@section('title', 'Sửa Bài đọc')
@section('page-title', 'Sửa Bài đọc')

@section('content')
<div class="row mb-3">
    <div class="col-md-12">
        <a href="{{ route('admin.tests.passages.index', $test) }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Quay lại
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5>Chỉnh sửa Bài đọc</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.tests.passages.update', [$test, $passage]) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label for="title" class="form-label">Tiêu đề (tùy chọn)</label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror" 
                               id="title" name="title" value="{{ old('title', $passage->title) }}"
                               placeholder="Nhập tiêu đề bài đọc (nếu có)">
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="content" class="form-label">Nội dung bài đọc <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('content') is-invalid @enderror" 
                                  id="content" name="content" rows="12" required
                                  placeholder="Nhập nội dung bài đọc (tối thiểu 50 ký tự)">{{ old('content', $passage->content) }}</textarea>
                        @error('content')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            <span id="word-count">0</span> từ | <span id="char-count">0</span> ký tự
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="order" class="form-label">Thứ tự <span class="text-danger">*</span></label>
                        <input type="number" class="form-control @error('order') is-invalid @enderror" 
                               id="order" name="order" value="{{ old('order', $passage->order) }}" 
                               min="1" required style="max-width: 150px;">
                        @error('order')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.tests.passages.index', $test) }}" class="btn btn-secondary">Hủy</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Cập nhật bài đọc
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card mb-3">
            <div class="card-header">
                <h6>Thông tin Test</h6>
            </div>
            <div class="card-body">
                <p><strong>Tiêu đề:</strong> {{ $test->title }}</p>
                <p><strong>Loại:</strong> {{ ucfirst($test->type) }}</p>
                <p><strong>Số bài đọc:</strong> {{ $test->passages()->count() }}</p>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h6>Thông tin bài đọc</h6>
            </div>
            <div class="card-body">
                <p><strong>Số câu hỏi:</strong> {{ $passage->questions()->count() }}</p>
                <p><strong>Ngày tạo:</strong> {{ $passage->created_at->format('d/m/Y H:i') }}</p>
                <p><strong>Cập nhật lần cuối:</strong> {{ $passage->updated_at->format('d/m/Y H:i') }}</p>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const contentTextarea = document.getElementById('content');
    const wordCountSpan = document.getElementById('word-count');
    const charCountSpan = document.getElementById('char-count');
    
    function updateCounts() {
        const text = contentTextarea.value;
        const words = text.trim() ? text.trim().split(/\s+/).length : 0;
        const chars = text.length;
        
        wordCountSpan.textContent = words;
        charCountSpan.textContent = chars;
    }
    
    contentTextarea.addEventListener('input', updateCounts);
    updateCounts(); // Initial count
});
</script>
@endsection
@endsection
