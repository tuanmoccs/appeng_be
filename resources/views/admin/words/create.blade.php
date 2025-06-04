@extends('admin.layouts.app')

@section('title', 'Thêm Từ vựng')
@section('page-title', 'Thêm Từ vựng')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5>Thông tin Từ vựng</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.words.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="word" class="form-label">Từ <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('word') is-invalid @enderror" 
                               id="word" name="word" value="{{ old('word') }}" required>
                        @error('word')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="translation" class="form-label">Nghĩa <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('translation') is-invalid @enderror" 
                               id="translation" name="translation" value="{{ old('translation') }}" required>
                        @error('translation')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="pronunciation" class="form-label">Phiên âm</label>
                        <input type="text" class="form-control @error('pronunciation') is-invalid @enderror" 
                               id="pronunciation" name="pronunciation" value="{{ old('pronunciation') }}">
                        @error('pronunciation')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="example_sentence" class="form-label">Câu ví dụ</label>
                        <textarea class="form-control @error('example_sentence') is-invalid @enderror" 
                                  id="example_sentence" name="example_sentence" rows="2">{{ old('example_sentence') }}</textarea>
                        @error('example_sentence')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="lesson_id" class="form-label">Bài học</label>
                        <select class="form-select @error('lesson_id') is-invalid @enderror" id="lesson_id" name="lesson_id">
                            <option value="">Không thuộc bài học nào</option>
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

                    <div class="mb-3">
                        <label for="image_url" class="form-label">URL Hình ảnh</label>
                        <input type="text" class="form-control @error('image_url') is-invalid @enderror" 
                               id="image_url" name="image_url" value="{{ old('image_url') }}">
                        @error('image_url')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="audio_url" class="form-label">URL Âm thanh</label>
                        <input type="text" class="form-control @error('audio_url') is-invalid @enderror" 
                               id="audio_url" name="audio_url" value="{{ old('audio_url') }}">
                        @error('audio_url')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.words.index') }}" class="btn btn-secondary">Hủy</a>
                        <button type="submit" class="btn btn-primary">Thêm Từ vựng</button>
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
                    <li><strong>Từ:</strong> Từ vựng tiếng Anh</li>
                    <li><strong>Nghĩa:</strong> Nghĩa tiếng Việt</li>
                    <li><strong>Phiên âm:</strong> Phiên âm quốc tế IPA</li>
                    <li><strong>Câu ví dụ:</strong> Câu ví dụ sử dụng từ vựng</li>
                </ul>
                
                <hr>
                
                <p><small class="text-muted">
                    Bạn có thể thêm URL hình ảnh và âm thanh để minh họa từ vựng.
                </small></p>
            </div>
        </div>
    </div>
</div>
@endsection
