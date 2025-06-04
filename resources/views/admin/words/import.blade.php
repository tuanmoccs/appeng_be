@extends('admin.layouts.app')

@section('title', 'Import Từ vựng')
@section('page-title', 'Import Từ vựng')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5>Import Từ vựng từ File</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.words.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="file" class="form-label">Chọn File <span class="text-danger">*</span></label>
                        <input type="file" class="form-control @error('file') is-invalid @enderror" 
                               id="file" name="file" required>
                        <div class="form-text">Hỗ trợ định dạng: CSV, Excel (.xlsx, .xls)</div>
                        @error('file')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="lesson_id" class="form-label">Bài học (tùy chọn)</label>
                        <select class="form-select @error('lesson_id') is-invalid @enderror" id="lesson_id" name="lesson_id">
                            <option value="">Không gán vào bài học nào</option>
                            @foreach($lessons as $lesson)
                                <option value="{{ $lesson->id }}">{{ $lesson->title }}</option>
                            @endforeach
                        </select>
                        <div class="form-text">Nếu chọn, tất cả từ vựng sẽ được gán vào bài học này</div>
                        @error('lesson_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.words.index') }}" class="btn btn-secondary">Hủy</a>
                        <button type="submit" class="btn btn-primary">Import</button>
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
                <p>File import cần có các cột sau:</p>
                <ul>
                    <li><strong>word</strong>: Từ vựng (bắt buộc)</li>
                    <li><strong>translation</strong>: Nghĩa (bắt buộc)</li>
                    <li><strong>pronunciation</strong>: Phiên âm (tùy chọn)</li>
                    <li><strong>example_sentence</strong>: Câu ví dụ (tùy chọn)</li>
                    <li><strong>image_url</strong>: URL hình ảnh (tùy chọn)</li>
                    <li><strong>audio_url</strong>: URL âm thanh (tùy chọn)</li>
                </ul>
                
                <hr>
                
                <p><a href="#" class="btn btn-sm btn-outline-secondary">Tải mẫu file import</a></p>
            </div>
        </div>
    </div>
</div>
@endsection
