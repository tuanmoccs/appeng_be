@extends('admin.layouts.app')

@section('title', 'Quản lý Từ vựng')
@section('page-title', 'Quản lý Từ vựng')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4>Danh sách Từ vựng</h4>
    <div>
        <a href="{{ route('admin.words.import') }}" class="btn btn-success me-2">
            <i class="fas fa-file-import"></i> Import
        </a>
        <a href="{{ route('admin.words.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Thêm Từ vựng
        </a>
    </div>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('admin.words.index') }}" method="GET" class="row g-3">
            <div class="col-md-4">
                <label for="search" class="form-label">Tìm kiếm</label>
                <input type="text" class="form-control" id="search" name="search" 
                       value="{{ request('search') }}" placeholder="Nhập từ hoặc nghĩa...">
            </div>
            <div class="col-md-4">
                <label for="lesson_id" class="form-label">Bài học</label>
                <select class="form-select" id="lesson_id" name="lesson_id">
                    <option value="">Tất cả bài học</option>
                    @foreach($lessons as $lesson)
                        <option value="{{ $lesson->id }}" {{ request('lesson_id') == $lesson->id ? 'selected' : '' }}>
                            {{ $lesson->title }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2">
                    <i class="fas fa-search"></i> Tìm kiếm
                </button>
                <a href="{{ route('admin.words.index') }}" class="btn btn-secondary">
                    <i class="fas fa-sync"></i> Reset
                </a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Từ</th>
                        <th>Nghĩa</th>
                        <th>Phiên âm</th>
                        <th>Bài học</th>
                        <th>Media</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($words as $word)
                    <tr>
                        <td>{{ $word->id }}</td>
                        <td>{{ $word->word }}</td>
                        <td>{{ $word->translation }}</td>
                        <td>{{ $word->pronunciation }}</td>
                        <td>{{ $word->lesson ? $word->lesson->title : 'N/A' }}</td>
                        <td>
                            @if($word->image_url)
                                <span class="badge bg-info me-1">Hình ảnh</span>
                            @endif
                            @if($word->audio_url)
                                <span class="badge bg-warning">Âm thanh</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('admin.words.show', $word) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.words.edit', $word) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.words.destroy', $word) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" 
                                            onclick="return confirm('Bạn có chắc chắn muốn xóa từ vựng này?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{ $words->appends(request()->query())->links() }}
    </div>
</div>
@endsection
