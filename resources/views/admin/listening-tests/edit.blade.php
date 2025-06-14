@extends('admin.layouts.app')

@section('title', 'Edit Listening Test')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Listening Test</h1>
        <div>
            <a href="{{ route('admin.listening-tests.show', $test->id) }}" class="d-none d-sm-inline-block btn btn-sm btn-info shadow-sm mr-2">
                <i class="fas fa-eye fa-sm text-white-50"></i> View Test
            </a>
            <a href="{{ route('admin.listening-tests.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
                <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to List
            </a>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Test Details</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.listening-tests.update', $test->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group row">
                    <label for="title" class="col-sm-2 col-form-label">Title <span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $test->title) }}" required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="form-group row">
                    <label for="description" class="col-sm-2 col-form-label">Description</label>
                    <div class="col-sm-10">
                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $test->description) }}</textarea>
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
                            <option value="ielts" {{ old('type', $test->type) == 'ielts' ? 'selected' : '' }}>IELTS</option>
                            <option value="toeic" {{ old('type', $test->type) == 'toeic' ? 'selected' : '' }}>TOEIC</option>
                            <option value="toefl" {{ old('type', $test->type) == 'toefl' ? 'selected' : '' }}>TOEFL</option>
                            <option value="general" {{ old('type', $test->type) == 'general' ? 'selected' : '' }}>General</option>
                        </select>
                        @error('type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="form-group row">
                    <label for="time_limit" class="col-sm-2 col-form-label">Time Limit (minutes) <span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                        <input type="number" class="form-control @error('time_limit') is-invalid @enderror" id="time_limit" name="time_limit" value="{{ old('time_limit', $test->time_limit) }}" min="1" required>
                        @error('time_limit')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="form-group row">
                    <label for="passing_score" class="col-sm-2 col-form-label">Passing Score (%) <span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                        <input type="number" class="form-control @error('passing_score') is-invalid @enderror" id="passing_score" name="passing_score" value="{{ old('passing_score', $test->passing_score) }}" min="0" max="100" required>
                        @error('passing_score')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="form-group row">
                    <label for="is_active" class="col-sm-2 col-form-label">Status</label>
                    <div class="col-sm-10">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1" {{ old('is_active', $test->is_active) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="is_active">Active</label>
                        </div>
                        <small class="form-text text-muted">Only active tests will be visible to users.</small>
                    </div>
                </div>
                
                <div class="form-group row">
                    <div class="col-sm-10 offset-sm-2">
                        <button type="submit" class="btn btn-primary">Update Test</button>
                        <a href="{{ route('admin.listening-tests.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
