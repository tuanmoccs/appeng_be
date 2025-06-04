@extends('admin.layouts.app')

@section('title', 'Tạo Quiz')
@section('page-title', 'Tạo Quiz')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5>Thông tin Quiz</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.quizzes.store') }}" method="POST">
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

                    <div class="mb-3">
                        <label for="lesson_id" class="form-label">Bài học <span class="text-danger">*</span></label>
                        <select class="form-select @error('lesson_id') is-invalid @enderror" id="lesson_id" name="lesson_id" required>
                            <option value="">Chọn bài học</option>
                            @foreach($lessons as $lesson)
                                <option value="{{ $lesson->id }}" {{ old('lesson_id') == $lesson->id ? 'selected' : '' }}>
                                    {{ $lesson->title }}
                                </option>
                            @endforeach
                        </select>
                        @error('lesson_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.quizzes.index') }}" class="btn btn-secondary">Hủy</a>
                        <button type="submit" class="btn btn-primary">Tạo Quiz</button>
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
                <p>Sau khi tạo quiz, bạn sẽ được chuyển đến trang thêm câu hỏi.</p>
                
                <hr>
                
                <p><small class="text-muted">
                    Quiz cần có ít nhất 1 câu hỏi để có thể sử dụng.
                </small></p>
            </div>
        </div>
    </div>
</div>
@endsection
