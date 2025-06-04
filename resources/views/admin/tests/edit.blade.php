@extends('admin.layouts.app')

@section('title', 'Chỉnh sửa Bài Test')
@section('page-title', 'Chỉnh sửa Bài Test')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5>Thông tin Bài Test</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.tests.update', $test) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label for="title" class="form-label">Tiêu đề <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror" 
                               id="title" name="title" value="{{ old('title', $test->title) }}" required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Mô tả</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" name="description" rows="3">{{ old('description', $test->description) }}</textarea>
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
                                    <option value="placement" {{ old('type', $test->type) == 'placement' ? 'selected' : '' }}>Placement Test</option>
                                    <option value="achievement" {{ old('type', $test->type) == 'achievement' ? 'selected' : '' }}>Achievement Test</option>
                                    <option value="practice" {{ old('type', $test->type) == 'practice' ? 'selected' : '' }}>Practice Test</option>
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
                                       id="total_questions" name="total_questions" value="{{ old('total_questions', $test->total_questions) }}" 
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
                                       id="time_limit" name="time_limit" value="{{ old('time_limit', $test->time_limit) }}" min="5">
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
                                       id="passing_score" name="passing_score" value="{{ old('passing_score', $test->passing_score) }}" 
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
                                   {{ old('is_active', $test->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                Kích hoạt bài test
                            </label>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.tests.index') }}" class="btn btn-secondary">Hủy</a>
                        <button type="submit" class="btn btn-primary">Cập nhật Bài Test</button>
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
                <p><strong>ID:</strong> {{ $test->id }}</p>
                <p><strong>Số câu hỏi hiện tại:</strong> {{ $test->questions()->count() }}</p>
                <p><strong>Ngày tạo:</strong> {{ $test->created_at->format('d/m/Y H:i') }}</p>
                <p><strong>Cập nhật lần cuối:</strong> {{ $test->updated_at->format('d/m/Y H:i') }}</p>
                
                <hr>
                
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.tests.questions', $test) }}" class="btn btn-outline-primary">
                        <i class="fas fa-question"></i> Quản lý câu hỏi
                    </a>
                    <a href="{{ route('admin.tests.show', $test) }}" class="btn btn-outline-info">
                        <i class="fas fa-eye"></i> Xem chi tiết
                    </a>
                </div>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <h6>Hành động nguy hiểm</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.tests.destroy', $test) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger w-100" 
                            onclick="return confirm('Bạn có chắc chắn muốn xóa bài test này? Tất cả câu hỏi và kết quả sẽ bị xóa!')">
                        <i class="fas fa-trash"></i> Xóa bài test này
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
