@extends('admin.layouts.app')

@section('title', 'Thêm Bài đọc')
@section('page-title', 'Thêm Bài đọc cho: ' . $test->title)

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5>Thông tin Bài đọc</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.tests.passages.store', $test) }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="title" class="form-label">Tiêu đề (Không bắt buộc)</label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror" 
                               id="title" name="title" value="{{ old('title') }}"
                               placeholder="VD: Đoạn văn về biến đổi khí hậu">
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="content" class="form-label">Nội dung bài đọc <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('content') is-invalid @enderror" 
                                  id="content" name="content" rows="12" required>{{ old('content') }}</textarea>
                        <small class="form-text text-muted">Tối thiểu 50 ký tự</small>
                        @error('content')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="order" class="form-label">Thứ tự <span class="text-danger">*</span></label>
                        <input type="number" class="form-control @error('order') is-invalid @enderror" 
                               id="order" name="order" value="{{ old('order', $nextOrder) }}" 
                               min="1" required>
                        @error('order')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.tests.passages.index', $test) }}" class="btn btn-secondary">Hủy</a>
                        <button type="submit" class="btn btn-primary">Tạo Bài đọc</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h6>Hướng dẫn</h6>
            </div>
            <div class="card-body">
                <ul class="list-unstyled">
                    <li>• Nội dung bài đọc nên từ 200-500 từ</li>
                    <li>• Bài đọc nên có chủ đề rõ ràng</li>
                    <li>• Sau khi tạo bài đọc, thêm câu hỏi liên quan</li>
                    <li>• Mỗi bài đọc nên có 3-5 câu hỏi</li>
                    <li>• Thứ tự quyết định vị trí hiển thị trong test</li>
                </ul>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <h6>Thông tin Test</h6>
            </div>
            <div class="card-body">
                <p><strong>Tiêu đề:</strong> {{ $test->title }}</p>
                <p><strong>Tổng câu hỏi:</strong> {{ $test->total_questions }}</p>
                <p><strong>Bài đọc hiện tại:</strong> {{ $test->passages()->count() }}</p>
            </div>
        </div>
    </div>
</div>
@endsection