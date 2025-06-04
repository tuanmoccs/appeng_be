@extends('admin.layouts.app')

@section('title', 'Quản lý Bài Test')
@section('page-title', 'Quản lý Bài Test')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4>Danh sách Bài Test</h4>
    <a href="{{ route('admin.tests.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Thêm Bài Test
    </a>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tiêu đề</th>
                        <th>Loại</th>
                        <th>Số câu hỏi</th>
                        <th>Thời gian</th>
                        <th>Điểm đạt</th>
                        <th>Trạng thái</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tests as $test)
                    <tr>
                        <td>{{ $test->id }}</td>
                        <td>{{ $test->title }}</td>
                        <td>
                            <span class="badge bg-{{ $test->type == 'placement' ? 'primary' : ($test->type == 'achievement' ? 'success' : 'secondary') }}">
                                {{ ucfirst($test->type) }}
                            </span>
                        </td>
                        <td>{{ $test->questions_count }}/{{ $test->total_questions }}</td>
                        <td>{{ $test->time_limit ? $test->time_limit . ' phút' : 'Không giới hạn' }}</td>
                        <td>{{ $test->passing_score }}%</td>
                        <td>
                            <span class="badge bg-{{ $test->is_active ? 'success' : 'danger' }}">
                                {{ $test->is_active ? 'Hoạt động' : 'Tạm dừng' }}
                            </span>
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('admin.tests.show', $test) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.tests.questions', $test) }}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-question"></i>
                                </a>
                                <a href="{{ route('admin.tests.edit', $test) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.tests.destroy', $test) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" 
                                            onclick="return confirm('Bạn có chắc chắn muốn xóa?')">
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

        {{ $tests->links() }}
    </div>
</div>
@endsection
