@extends('admin.layouts.app')

@section('title', 'Chi tiết Người dùng')
@section('page-title', 'Chi tiết Người dùng')

@section('content')
<div class="row mb-3">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-2 text-center">
                        @if($user->avatar)
                            <img src="{{ $user->avatar }}" alt="{{ $user->name }}" class="img-thumbnail rounded-circle mb-3" style="width: 120px; height: 120px; object-fit: cover;">
                        @else
                            <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center mb-3" style="width: 120px; height: 120px; margin: 0 auto;">
                                <span class="text-white" style="font-size: 48px;">{{ substr($user->name, 0, 1) }}</span>
                            </div>
                        @endif
                    </div>
                    <div class="col-md-10">
                        <h3>{{ $user->name }}</h3>
                        <p><strong>Email:</strong> {{ $user->email }}</p>
                        <p><strong>Ngày đăng ký:</strong> {{ $user->created_at->format('d/m/Y H:i') }}</p>
                        <p><strong>Đăng nhập cuối:</strong> {{ $user->last_login_at ? $user->last_login_at->format('d/m/Y H:i') : 'Chưa đăng nhập' }}</p>
                        
                        <div class="mt-3">
                            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-primary">
                                <i class="fas fa-edit"></i> Chỉnh sửa
                            </a>
                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger" 
                                        onclick="return confirm('Bạn có chắc chắn muốn xóa người dùng này?')">
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
    <div class="col-md-6 mb-3">
        <div class="card">
            <div class="card-header">
                <h5>Thống kê học tập</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body text-center">
                                <h3>{{ $user->lessonProgress->count() }}</h3>
                                <p class="mb-0">Bài học đã hoàn thành</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="card bg-success text-white">
                            <div class="card-body text-center">
                                <h3>{{ $user->quizResults->count() }}</h3>
                                <p class="mb-0">Quiz đã làm</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                @if($user->stats)
                <div class="mt-3">
                    <p><strong>Từ vựng đã học:</strong> {{ $user->stats->words_learned }}</p>
                    <p><strong>Số ngày streak:</strong> {{ $user->stats->streak_days }}</p>
                    <p><strong>Hoạt động gần nhất:</strong> {{ $user->stats->last_activity_at ? $user->stats->last_activity_at->format('d/m/Y H:i') : 'Chưa có' }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>
    
    <div class="col-md-6 mb-3">
        <div class="card">
            <div class="card-header">
                <h5>Thành tích</h5>
            </div>
            <div class="card-body">
                @if($user->achievements->count() > 0)
                    <div class="list-group">
                        @foreach($user->achievements as $achievement)
                            <div class="list-group-item">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1">{{ $achievement->title }}</h6>
                                    <small>{{ $achievement->achieved_at->format('d/m/Y') }}</small>
                                </div>
                                <p class="mb-1">{{ $achievement->description }}</p>
                                <small class="text-muted">{{ ucfirst(str_replace('_', ' ', $achievement->achievement_type)) }}</small>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted">Người dùng chưa đạt được thành tích nào.</p>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12 mb-3">
        <div class="card">
            <div class="card-header">
                <h5>Bài học đã hoàn thành</h5>
            </div>
            <div class="card-body">
                @if($user->lessonProgress->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Bài học</th>
                                    <th>Tiến độ</th>
                                    <th>Ngày hoàn thành</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($user->lessonProgress as $progress)
                                    <tr>
                                        <td>{{ $progress->lesson->title }}</td>
                                        <td>
                                            <div class="progress">
                                                <div class="progress-bar" role="progressbar" style="width: {{ $progress->progress_percentage }}%;" 
                                                     aria-valuenow="{{ $progress->progress_percentage }}" aria-valuemin="0" aria-valuemax="100">
                                                    {{ $progress->progress_percentage }}%
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $progress->completed_at->format('d/m/Y H:i') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted">Người dùng chưa hoàn thành bài học nào.</p>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5>Kết quả Quiz gần đây</h5>
            </div>
            <div class="card-body">
                @if($user->quizResults->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Quiz</th>
                                    <th>Điểm</th>
                                    <th>Tỷ lệ</th>
                                    <th>Ngày làm</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($user->quizResults->take(10) as $result)
                                    <tr>
                                        <td>{{ $result->quiz->title }}</td>
                                        <td>{{ $result->score }}/{{ $result->total_questions }}</td>
                                        <td>
                                            @php $percentage = ($result->score / $result->total_questions) * 100; @endphp
                                            <div class="progress">
                                                <div class="progress-bar {{ $percentage >= 70 ? 'bg-success' : ($percentage >= 40 ? 'bg-warning' : 'bg-danger') }}" 
                                                     role="progressbar" style="width: {{ $percentage }}%;" 
                                                     aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100">
                                                    {{ round($percentage) }}%
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $result->created_at->format('d/m/Y H:i') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted">Người dùng chưa làm quiz nào.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
