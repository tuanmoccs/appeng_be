@extends('admin.layouts.app')

@section('title', 'Create Listening Test')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Create New Listening Test</h1>
        <a href="{{ route('admin.listening-tests.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to List
        </a>
    </div>

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
            <h6 class="m-0 font-weight-bold text-primary">Test Details</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.listening-tests.store') }}" method="POST">
                @csrf
                <div class="form-group row">
                    <label for="title" class="col-sm-2 col-form-label">Title <span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control @error('title') is-invalid @enderror" 
                               id="title" name="title" value="{{ old('title') }}" required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="form-group row">
                    <label for="description" class="col-sm-2 col-form-label">Description</label>
                    <div class="col-sm-10">
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" name="description" rows="3">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="form-group row">
                    <label for="type" class="col-sm-2 col-form-label">Type <span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                        <select class="form-control @error('type') is-invalid @enderror" id="type" name="type" required>
                            <option value="">Select Type</option>
                            <option value="ielts" {{ old('type') == 'ielts' ? 'selected' : '' }}>IELTS</option>
                            <option value="toeic" {{ old('type') == 'toeic' ? 'selected' : '' }}>TOEIC</option>
                            <option value="toefl" {{ old('type') == 'toefl' ? 'selected' : '' }}>TOEFL</option>
                            <option value="general" {{ old('type') == 'general' ? 'selected' : '' }}>General</option>
                        </select>
                        @error('type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="form-group row">
                    <label for="time_limit" class="col-sm-2 col-form-label">Time Limit (minutes) <span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                        <input type="number" class="form-control @error('time_limit') is-invalid @enderror" 
                               id="time_limit" name="time_limit" value="{{ old('time_limit', 30) }}" min="1" required>
                        @error('time_limit')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="form-group row">
                    <label for="passing_score" class="col-sm-2 col-form-label">Passing Score (%) <span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                        <input type="number" class="form-control @error('passing_score') is-invalid @enderror" 
                               id="passing_score" name="passing_score" value="{{ old('passing_score', 70) }}" 
                               min="0" max="100" required>
                        @error('passing_score')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="form-group row">
                    <label for="is_active" class="col-sm-2 col-form-label">Status</label>
                    <div class="col-sm-10">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" 
                                   value="1" {{ old('is_active') ? 'checked' : '' }}>
                            <label class="custom-control-label" for="is_active">Active</label>
                        </div>
                        <small class="form-text text-muted">Only active tests will be visible to users.</small>
                    </div>
                </div>
                
                <div class="form-group row">
                    <div class="col-sm-10 offset-sm-2">
                        <button type="submit" class="btn btn-primary">Create Test</button>
                        <a href="{{ route('admin.listening-tests.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
