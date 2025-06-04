@extends('admin.layouts.app')

@section('title', 'Tạo Bài Test')
@section('page-title', 'Tạo Bài Test')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5>Thông tin Bài Test</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.tests.store') }}" method="POST">
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
                        <label for="description" class="form-label">Mô tả</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" name="description" rows="3">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="type" class="form-label">Loại Test <span class="text-danger">*</span></label>
                                <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                                    <option value="">Chọn loại test</option>
                                    <option value="placement" {{ old('type') == 'placement' ? 'selected' : '' }}>Placement Test</option>
                                    <option value="achievement" {{ old('type') == 'achievement' ? 'selected' : '' }}>Achievement Test</option>
                                    <option value="practice" {{ old('type') == 'practice' ? 'selected' : '' }}>Practice Test</option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="total_questions" class="form-label">Tổng số câu hỏi <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('total_questions') is-invalid @enderror" 
                                       id="total_questions" name="total_questions" value="{{ old('total_questions', 50) }}" 
                                       min="10" max="200" required>
                                @error('total_questions')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="time_limit" class="form-label">Thời gian (phút)</label>
                                <input type="number" class="form-control @error('time_limit') is-invalid @enderror" 
                                       id="time_limit" name="time_limit" value="{{ old('time_limit') }}" min="5">
                                <small class="form-text text-muted">Để trống nếu không giới hạn thời gian</small>
                                @error('time_limit')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="passing_score" class="form-label">Điểm đạt (%) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('passing_score') is-invalid @enderror" 
                                       id="passing_score" name="passing_score" value="{{ old('passing_score', 70) }}" 
                                       min="0" max="100" required>
                                @error('passing_score')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" 
                                   {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                Kích hoạt bài test
                            </label>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.tests.index') }}" class="btn btn-secondary">Hủy</a>
                        <button type="submit" class="btn btn-primary">Tạo Bài Test</button>
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
                    <li><strong>Placement Test:</strong> Đánh giá trình độ ban đầu</li>
                    <li><strong>Achievement Test:</strong> Kiểm tra sau khi học</li>
                    <li><strong>Practice Test:</strong> Luyện tập</li>
                </ul>
                
                <hr>
                
                <p><small class="text-muted">
                    Sau khi tạo bài test, bạn có thể thêm câu hỏi vào bài test.
                </small></p>
            </div>
        </div>
    </div>
</div>
@endsection
