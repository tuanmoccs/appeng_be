@extends('admin.layouts.app')

@section('title', 'Chi tiết Bài Test')
@section('page-title', 'Chi tiết Bài Test')

@section('content')
<div class="row mb-3">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <h3>{{ $test->title }}</h3>
                        <p class="text-muted">{{ $test->description ?: 'Không có mô tả' }}</p>
                        
                        <div class="row mt-3">
                            <div class="col-md-3">
                                <strong>Loại Test:</strong><br>
                                <span class="badge bg-{{ $test->type == 'placement' ? 'primary' : ($test->type == 'achievement' ? 'success' : 'secondary') }} fs-6">
                                    {{ ucfirst($test->type) }}
                                </span>
                            </div>
                            <div class="col-md-3">
                                <strong>Thời gian:</strong><br>
                                {{ $test->time_limit ? $test->time_limit . ' phút' : 'Không giới hạn' }}
                            </div>
                            <div class="col-md-3">
                                <strong>Điểm đạt:</strong><br>
                                {{ $test->passing_score }}%
                            </div>
                            <div class="col-md-3">
                                <strong>Trạng thái:</strong><br>
                                <span class="badge bg-{{ $test->is_active ? 'success' : 'danger' }} fs-6">
                                    {{ $test->is_active ? 'Hoạt động' : 'Tạm dừng' }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 text-end">
                        <div class="btn-group" role="group">
                            <a href="{{ route('admin.tests.edit', $test) }}" class="btn btn-primary">
                                <i class="fas fa-edit"></i> Chỉnh sửa
                            </a>
                            <a href="{{ route('admin.tests.questions', $test) }}" class="btn btn-warning">
                                <i class="fas fa-question"></i> Câu hỏi
                            </a>
                            <form action="{{ route('admin.tests.destroy', $test) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger" 
                                        onclick="return confirm('Bạn có chắc chắn muốn xóa bài test này?')">
                                    <i class="fas fa-trash"></i> Xóa
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-3 mb-3">
        <div class="card text-white bg-primary">
            <div class="card-body text-center">
                <h3>{{ $test->questions()->count() }}</h3>
                <p class="mb-0">Câu hỏi hiện tại</p>
                <small>/ {{ $test->total_questions }} câu hỏi</small>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card text-white bg-success">
            <div class="card-body text-center">
                <h3>{{ $test->results()->count() }}</h3>
                <p class="mb-0">Lượt làm bài</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card text-white bg-info">
            <div class="card-body text-center">
                @php
                    $avgScore = $test->results()->avg('score');
                    $avgPercentage = $test->results()->count() > 0 ? 
                        ($avgScore / $test->results()->first()->total_questions ?? 1) * 100 : 0;
                @endphp
                <h3>{{ round($avgPercentage) }}%</h3>
                <p class="mb-0">Điểm trung bình</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card text-white bg-warning">
            <div class="card-body text-center">
                @php
                    $passedCount = $test->results()->where('passed', true)->count();
                    $passRate = $test->results()->count() > 0 ? 
                        ($passedCount / $test->results()->count()) * 100 : 0;
                @endphp
                <h3>{{ round($passRate) }}%</h3>
                <p class="mb-0">Tỷ lệ đạt</p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <div class="card">
            <div class="card-header">
                <h5>Câu hỏi gần đây</h5>
            </div>
            <div class="card-body">
                @if($test->questions()->count() > 0)
                    <div class="list-group">
                        @foreach($test->questions()->latest()->take(5)->get() as $question)
                            <div class="list-group-item">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1">{{ Str::limit($question->question, 60) }}</h6>
                                    <small class="text-muted">{{ $question->created_at->diffForHumans() }}</small>
                                </div>
                                <p class="mb-1">
                                    <span class="badge bg-{{ $question->difficulty == 'easy' ? 'success' : ($question->difficulty == 'medium' ? 'warning' : 'danger') }}">
                                        {{ ucfirst($question->difficulty) }}
                                    </span>
                                    {{ count($question->options) }} lựa chọn
                                </p>
                            </div>
                        @endforeach
                    </div>
                    
                    <div class="mt-3 text-center">
                        <a href="{{ route('admin.tests.questions', $test) }}" class="btn btn-outline-primary">
                            Xem tất cả câu hỏi
                        </a>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-question-circle fa-3x text-muted mb-3"></i>
                        <h6>Chưa có câu hỏi nào</h6>
                        <p class="text-muted">Hãy thêm câu hỏi đầu tiên cho bài test này.</p>
                        <a href="{{ route('admin.tests.questions.create', $test) }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Thêm câu hỏi
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <div class="col-md-6 mb-3">
        <div class="card">
            <div class="card-header">
                <h5>Kết quả gần đây</h5>
            </div>
            <div class="card-body">
                @if($test->results()->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Người dùng</th>
                                    <th>Điểm</th>
                                    <th>Kết quả</th>
                                    <th>Ngày</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($test->results()->with('user')->latest()->take(10)->get() as $result)
                                    <tr>
                                        <td>{{ $result->user->name }}</td>
                                        <td>{{ $result->score }}/{{ $result->total_questions }}</td>
                                        <td>
                                            <span class="badge bg-{{ $result->passed ? 'success' : 'danger' }}">
                                                {{ $result->passed ? 'Đạt' : 'Không đạt' }}
                                            </span>
                                        </td>
                                        <td>{{ $result->created_at->format('d/m/Y') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-chart-line fa-3x text-muted mb-3"></i>
                        <h6>Chưa có kết quả nào</h6>
                        <p class="text-muted">Chưa có người dùng nào làm bài test này.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5>Thông tin chi tiết</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table">
                            <tr>
                                <th>ID:</th>
                                <td>{{ $test->id }}</td>
                            </tr>
                            <tr>
                                <th>Tiêu đề:</th>
                                <td>{{ $test->title }}</td>
                            </tr>
                            <tr>
                                <th>Loại test:</th>
                                <td>{{ ucfirst($test->type) }}</td>
                            </tr>
                            <tr>
                                <th>Tổng số câu hỏi:</th>
                                <td>{{ $test->total_questions }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table">
                            <tr>
                                <th>Thời gian làm bài:</th>
                                <td>{{ $test->time_limit ? $test->time_limit . ' phút' : 'Không giới hạn' }}</td>
                            </tr>
                            <tr>
                                <th>Điểm đạt:</th>
                                <td>{{ $test->passing_score }}%</td>
                            </tr>
                            <tr>
                                <th>Ngày tạo:</th>
                                <td>{{ $test->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                            <tr>
                                <th>Cập nhật lần cuối:</th>
                                <td>{{ $test->updated_at->format('d/m/Y H:i') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
