@extends('admin.layouts.app')

@section('title', 'Chi tiết Từ vựng')
@section('page-title', 'Chi tiết Từ vựng')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5>{{ $word->word }}</h5>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6>Thông tin cơ bản</h6>
                        <table class="table">
                            <tr>
                                <th>Từ:</th>
                                <td>{{ $word->word }}</td>
                            </tr>
                            <tr>
                                <th>Nghĩa:</th>
                                <td>{{ $word->translation }}</td>
                            </tr>
                            <tr>
                                <th>Phiên âm:</th>
                                <td>{{ $word->pronunciation ?: 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Bài học:</th>
                                <td>{{ $word->lesson ? $word->lesson->title : 'Không thuộc bài học nào' }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        @if($word->image_url)
                            <div class="text-center mb-3">
                                <img src="{{ $word->image_url }}" alt="{{ $word->word }}" class="img-fluid img-thumbnail" style="max-height: 200px;">
                            </div>
                        @endif
                        
                        @if($word->audio_url)
                            <div class="text-center">
                                <audio controls class="w-100">
                                    <source src="{{ $word->audio_url }}" type="audio/mpeg">
                                    Your browser does not support the audio element.
                                </audio>
                            </div>
                        @endif
                    </div>
                </div>
                
                @if($word->example_sentence)
                    <div class="mb-4">
                        <h6>Câu ví dụ:</h6>
                        <div class="card">
                            <div class="card-body">
                                <p class="mb-0">{{ $word->example_sentence }}</p>
                            </div>
                        </div>
                    </div>
                @endif
                
                <div class="d-flex justify-content-between">
                    <a href="{{ route('admin.words.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Quay lại
                    </a>
                    <div>
                        <a href="{{ route('admin.words.edit', $word) }}" class="btn btn-primary">
                            <i class="fas fa-edit"></i> Chỉnh sửa
                        </a>
                        <form action="{{ route('admin.words.destroy', $word) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" 
                                    onclick="return confirm('Bạn có chắc chắn muốn xóa từ vựng này?')">
                                <i class="fas fa-trash"></i> Xóa
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h6>Thông tin bổ sung</h6>
            </div>
            <div class="card-body">
                <p><strong>ID:</strong> {{ $word->id }}</p>
                <p><strong>Ngày tạo:</strong> {{ $word->created_at->format('d/m/Y H:i') }}</p>
                <p><strong>Cập nhật lần cuối:</strong> {{ $word->updated_at->format('d/m/Y H:i') }}</p>
            </div>
        </div>
    </div>
</div>
@endsection
