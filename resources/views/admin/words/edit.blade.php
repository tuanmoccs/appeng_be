@extends('admin.layouts.app')

@section('title', 'Chỉnh sửa Từ vựng')
@section('page-title', 'Chỉnh sửa Từ vựng')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5>Thông tin Từ vựng</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.words.update', $word) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label for="word" class="form-label">Từ <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('word') is-invalid @enderror" 
                               id="word" name="word" value="{{ old('word', $word->word) }}" required>
                        @error('word')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="translation" class="form-label">Nghĩa <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('translation') is-invalid @enderror" 
                               id="translation" name="translation" value="{{ old('translation', $word->translation) }}" required>
                        @error('translation')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="pronunciation" class="form-label">Phiên âm</label>
                        <input type="text" class="form-control @error('pronunciation') is-invalid @enderror" 
                               id="pronunciation" name="pronunciation" value="{{ old('pronunciation', $word->pronunciation) }}">
                        @error('pronunciation')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="example_sentence" class="form-label">Câu ví dụ</label>
                        <textarea class="form-control @error('example_sentence') is-invalid @enderror" 
                                  id="example_sentence" name="example_sentence" rows="2">{{ old('example_sentence', $word->example_sentence) }}</textarea>
                        @error('example_sentence')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="lesson_id" class="form-label">Bài học</label>
                        <select class="form-select @error('lesson_id') is-invalid @enderror" id="lesson_id" name="lesson_id">
                            <option value="">Không thuộc bài học nào</option>
                            @foreach($lessons as $lesson)
                                <option value="{{ $lesson->id }}" {{ old('lesson_id', $word->lesson_id) == $lesson->id ? 'selected' : '' }}>
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
                               id="image_url" name="image_url" value="{{ old('image_url', $word->image_url) }}">
                        @error('image_url')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        
                        @if($word->image_url)
                            <div class="mt-2">
                                <img src="{{ $word->image_url }}" alt="{{ $word->word }}" class="img-thumbnail" style="max-height: 100px;">
                            </div>
                        @endif
                    </div>

                    <div class="mb-3">
                        <label for="audio_url" class="form-label">URL Âm thanh</label>
                        <input type="text" class="form-control @error('audio_url') is-invalid @enderror" 
                               id="audio_url" name="audio_url" value="{{ old('audio_url', $word->audio_url) }}">
                        @error('audio_url')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        
                        @if($word->audio_url)
                            <div class="mt-2">
                                <audio controls>
                                    <source src="{{ $word->audio_url }}" type="audio/mpeg">
                                    Your browser does not support the audio element.
                                </audio>
                            </div>
                        @endif
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.words.index') }}" class="btn btn-secondary">Hủy</a>
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
                <p><strong>ID:</strong> {{ $word->id }}</p>
                <p><strong>Ngày tạo:</strong> {{ $word->created_at->format('d/m/Y H:i') }}</p>
                <p><strong>Cập nhật lần cuối:</strong> {{ $word->updated_at->format('d/m/Y H:i') }}</p>
                
                <hr>
                
                <div class="d-grid">
                    <form action="{{ route('admin.words.destroy', $word) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" 
                                onclick="return confirm('Bạn có chắc chắn muốn xóa từ vựng này?')">
                            <i class="fas fa-trash"></i> Xóa từ vựng này
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
