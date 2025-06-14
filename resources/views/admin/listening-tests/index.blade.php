@extends('admin.layouts.app')

@section('title', 'Listening Tests')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Listening Tests</h1>
        <a href="{{ route('admin.listening-tests.create') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50"></i> Create New Test
        </a>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">All Listening Tests</h6>
        </div>
        <div class="card-body">
            @if($tests->isEmpty())
                <div class="text-center py-5">
                    <i class="fas fa-headphones fa-3x text-gray-300 mb-3"></i>
                    <p>No listening tests found. Create your first test to get started.</p>
                    <a href="{{ route('admin.listening-tests.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Create New Test
                    </a>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Type</th>
                                <th>Questions</th>
                                <th>Time Limit</th>
                                <th>Passing Score</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tests as $test)
                            <tr>
                                <td>
                                    <strong>{{ $test->title }}</strong>
                                    @if($test->description)
                                        <br><small class="text-muted">{{ Str::limit($test->description, 50) }}</small>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge badge-info">{{ strtoupper($test->type) }}</span>
                                </td>
                                <td>
                                    <span class="badge badge-secondary">{{ $test->total_questions }}</span>
                                </td>
                                <td>{{ $test->time_limit }} min</td>
                                <td>{{ $test->passing_score }}%</td>
                                <td>
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input status-toggle" 
                                               id="status{{ $test->id }}" 
                                               data-id="{{ $test->id }}"
                                               {{ $test->is_active ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="status{{ $test->id }}"></label>
                                    </div>
                                </td>
                                <td>{{ $test->created_at->format('Y-m-d') }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.listening-tests.show', $test->id) }}" 
                                           class="btn btn-sm btn-info" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.listening-tests.edit', $test->id) }}" 
                                           class="btn btn-sm btn-primary" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.listening-tests.destroy', $test->id) }}" 
                                              method="POST" class="d-inline delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Delete">
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
                
                <!-- Pagination -->
                <div class="d-flex justify-content-center">
                    {{ $tests->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Status toggle
    $('.status-toggle').change(function() {
        var testId = $(this).data('id');
        var isActive = $(this).is(':checked');
        
        $.ajax({
            url: '/admin/listening-tests/' + testId + '/toggle-status',
            type: 'PATCH',
            data: {
                _token: '{{ csrf_token() }}',
                is_active: isActive
            },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                } else {
                    toastr.error(response.message);
                }
            },
            error: function() {
                toastr.error('Error updating status');
                // Revert the toggle
                $('.status-toggle[data-id="' + testId + '"]').prop('checked', !isActive);
            }
        });
    });
    
    // Delete confirmation
    $('.delete-form').on('submit', function(e) {
        e.preventDefault();
        if (confirm('Are you sure you want to delete this test? This will also delete all sections, questions, and audio files.')) {
            this.submit();
        }
    });
});
</script>
@endsection
