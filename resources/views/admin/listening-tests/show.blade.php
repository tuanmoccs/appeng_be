@extends('admin.layouts.app')

@section('title', 'Listening Test Details')

@section('styles')
<style>
    .audio-player {
        width: 100%;
    }
    .section-card {
        margin-bottom: 20px;
        border-left: 4px solid #4e73df;
    }
    .question-card {
        margin-bottom: 10px;
        border-left: 4px solid #36b9cc;
    }
    .correct-answer {
        color: #1cc88a;
        font-weight: bold;
    }
    .modal {
    z-index: 9999 !important;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ $test->title }}</h1>
        <div>
            <a href="{{ route('admin.listening-tests.edit', $test->id) }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm mr-2">
                <i class="fas fa-edit fa-sm text-white-50"></i> Edit Test
            </a>
            <a href="{{ route('admin.listening-tests.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
                <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to List
            </a>
        </div>
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

    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif

    <!-- Test Info Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Test Information</h6>
            <div>
                <span class="badge badge-{{ $test->is_active ? 'success' : 'secondary' }}">
                    {{ $test->is_active ? 'Active' : 'Inactive' }}
                </span>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Type:</strong> {{ ucfirst($test->type) }}</p>
                    <p><strong>Time Limit:</strong> {{ $test->time_limit }} minutes</p>
                    <p><strong>Passing Score:</strong> {{ $test->passing_score }}%</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Total Questions:</strong> {{ $test->total_questions }}</p>
                    <p><strong>Created:</strong> {{ $test->created_at->format('Y-m-d H:i') }}</p>
                    <p><strong>Last Updated:</strong> {{ $test->updated_at->format('Y-m-d H:i') }}</p>
                </div>
            </div>
            @if($test->description)
            <div class="row mt-3">
                <div class="col-12">
                    <p><strong>Description:</strong></p>
                    <p>{{ $test->description }}</p>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Sections and Questions -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Sections and Questions</h6>
            <button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#addSectionModal">
                <i class="fas fa-plus fa-sm"></i> Add Section
            </button>
        </div>
        <div class="card-body">
            @if($test->sections->isEmpty())
                <div class="text-center py-5">
                    <i class="fas fa-folder-open fa-3x text-gray-300 mb-3"></i>
                    <p>No sections added yet. Add a section to get started.</p>
                </div>
            @else
                @foreach($test->sections->sortBy('order') as $section)
                <div class="card section-card">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">
                            Section {{ $section->order }}: {{ $section->title }}
                            <span class="badge badge-info ml-2">{{ ucfirst($section->question_type) }}</span>
                        </h6>
                        <div>
                            <button class="btn btn-sm btn-info edit-section-btn" 
                                    data-id="{{ $section->id }}"
                                    data-title="{{ $section->title }}"
                                    data-instructions="{{ $section->instructions }}"
                                    data-question-type="{{ $section->question_type }}"
                                    data-order="{{ $section->order }}"
                                    data-toggle="modal" 
                                    data-target="#editSectionModal">
                                <i class="fas fa-edit fa-sm"></i>
                            </button>
                            <button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#addQuestionModal" data-section-id="{{ $section->id }}">
                                <i class="fas fa-plus fa-sm"></i> Add Question
                            </button>
                            <form action="{{ route('admin.listening-sections.destroy', ['test' => $test->id, 'section' => $section->id]) }}" method="POST" class="d-inline delete-section-form">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">
                                    <i class="fas fa-trash fa-sm"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                    <div class="card-body">
                        @if($section->instructions)
                            <div class="alert alert-info">
                                <strong>Instructions:</strong> {{ $section->instructions }}
                            </div>
                        @endif
                        
                        @if($section->audio_file)
                            <div class="mb-3">
                                <label><strong>Section Audio:</strong></label>
                                <audio controls class="audio-player">
                                    <source src="{{ asset('storage/' . $section->audio_file) }}" type="audio/mpeg">
                                    Your browser does not support the audio element.
                                </audio>
                                <small class="d-block mt-1 text-muted">Duration: {{ gmdate("i:s", $section->audio_duration) }}</small>
                            </div>
                        @endif
                        
                        <h6 class="font-weight-bold mt-4 mb-3">Questions ({{ $section->questions->count() }})</h6>
                        
                        @if($section->questions->isEmpty())
                            <div class="alert alert-warning">
                                No questions added to this section yet.
                            </div>
                        @else
                            @foreach($section->questions->sortBy('order') as $question)
                                <div class="card question-card mb-3">
                                    <div class="card-header py-2 d-flex flex-row align-items-center justify-content-between">
                                        <h6 class="m-0 font-weight-bold">Question {{ $question->order }}</h6>
                                        <div>
                                            <button class="btn btn-sm btn-info edit-question-btn" 
                                                    data-id="{{ $question->id }}"
                                                    data-question="{{ $question->question }}"
                                                    data-options="{{ json_encode($question->options) }}"
                                                    data-correct-answer="{{ $question->correct_answer }}"
                                                    data-audio-start-time="{{ $question->audio_start_time }}"
                                                    data-audio-end-time="{{ $question->audio_end_time }}"
                                                    data-order="{{ $question->order }}"
                                                    data-toggle="modal" 
                                                    data-target="#editQuestionModal">
                                                <i class="fas fa-edit fa-sm"></i>
                                            </button>
                                            <form action="{{ route('admin.listening-questions.destroy', ['section' => $section->id, 'question' => $question->id]) }}" method="POST" class="d-inline delete-question-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="fas fa-trash fa-sm"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                    <div class="card-body py-3">
                                        <p><strong>{{ $question->question }}</strong></p>
                                        
                                        @if($question->audio_file && $section->question_type === 'single')
                                            <div class="mb-3">
                                                <audio controls class="audio-player">
                                                    <source src="{{ asset('storage/' . $question->audio_file) }}" type="audio/mpeg">
                                                    Your browser does not support the audio element.
                                                </audio>
                                            </div>
                                        @elseif($section->question_type === 'multiple' && ($question->audio_start_time !== null || $question->audio_end_time !== null))
                                            <div class="mb-2">
                                                <small class="text-muted">
                                                    Audio timing: 
                                                    {{ $question->audio_start_time !== null ? gmdate("i:s", $question->audio_start_time) : 'Start' }} 
                                                    to 
                                                    {{ $question->audio_end_time !== null ? gmdate("i:s", $question->audio_end_time) : 'End' }}
                                                </small>
                                            </div>
                                        @endif
                                        
                                        <div class="mt-2">
                                            <p><strong>Options:</strong></p>
                                            <ul class="list-group">
                                                @foreach($question->options as $option)
                                                    <li class="list-group-item {{ $option === $question->correct_answer ? 'correct-answer' : '' }}">
                                                        {{ $option }} {{ $option === $question->correct_answer ? '✓' : '' }}
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
                @endforeach
            @endif
        </div>
    </div>
</div>

<!-- Add Section Modal -->
<div class="modal fade" id="addSectionModal" tabindex="-1" role="dialog" aria-labelledby="addSectionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addSectionModalLabel">Add New Section</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('admin.listening-sections.store', $test->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="title">Section Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="title" name="title" value="{{ old('title') }}" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="instructions">Instructions</label>
                        <textarea class="form-control" id="instructions" name="instructions" rows="2">{{ old('instructions') }}</textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="question_type">Question Type <span class="text-danger">*</span></label>
                        <select class="form-control" id="question_type" name="question_type" required>
                            <option value="">Select Question Type</option>
                            <option value="single" {{ old('question_type') == 'single' ? 'selected' : '' }}>Single (Each question has its own audio)</option>
                            <option value="multiple" {{ old('question_type') == 'multiple' ? 'selected' : '' }}>Multiple (One audio for multiple questions)</option>
                        </select>
                        <small class="form-text text-muted">
                            For "Single" type, each question will have its own audio file.
                            For "Multiple" type, one audio file will be used for all questions in this section.
                        </small>
                    </div>
                    
                    <div class="form-group">
                        <label for="audio_file">Audio File <span class="text-danger">*</span></label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="audio_file" name="audio_file" accept="audio/mp3,audio/wav,audio/m4a,audio/ogg" required>
                            <label class="custom-file-label" for="audio_file">Choose file</label>
                        </div>
                        <small class="form-text text-muted">
                            Accepted formats: MP3, WAV, M4A, OGG. Maximum size: 20MB.
                        </small>
                    </div>
                    
                    <div class="form-group">
                        <label for="order">Order <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="order" name="order" min="1" value="{{ old('order', $test->sections->count() + 1) }}" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Section</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Section Modal -->
<div class="modal fade" id="editSectionModal" tabindex="-1" role="dialog" aria-labelledby="editSectionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editSectionModalLabel">Edit Section</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editSectionForm" action="" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="form-group">
                        <label for="edit_title">Section Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_title" name="title" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="edit_instructions">Instructions</label>
                        <textarea class="form-control" id="edit_instructions" name="instructions" rows="2"></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="edit_question_type">Question Type <span class="text-danger">*</span></label>
                        <select class="form-control" id="edit_question_type" name="question_type" required>
                            <option value="single">Single (Each question has its own audio)</option>
                            <option value="multiple">Multiple (One audio for multiple questions)</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="edit_audio_file">Audio File</label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="edit_audio_file" name="audio_file" accept="audio/mp3,audio/wav,audio/m4a,audio/ogg">
                            <label class="custom-file-label" for="edit_audio_file">Choose file</label>
                        </div>
                        <small class="form-text text-muted">
                            Leave empty to keep the current audio file. Accepted formats: MP3, WAV, M4A, OGG. Maximum size: 20MB.
                        </small>
                    </div>
                    
                    <div class="form-group">
                        <label for="edit_order">Order <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="edit_order" name="order" min="1" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Section</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Question Modal -->
<div class="modal fade" id="addQuestionModal" tabindex="-1" role="dialog" aria-labelledby="addQuestionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addQuestionModalLabel">Add New Question</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="addQuestionForm" action="" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="listening_section_id" id="question_section_id">
                    
                    <div class="form-group">
                        <label for="question">Question Text <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="question" name="question" rows="2" required></textarea>
                    </div>
                    
                    <div id="singleQuestionAudio" style="display: none;">
                        <div class="form-group">
                            <label for="question_audio_file">Question Audio File <span class="text-danger">*</span></label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="question_audio_file" name="audio_file" accept="audio/mp3,audio/wav,audio/m4a,audio/ogg">
                                <label class="custom-file-label" for="question_audio_file">Choose file</label>
                            </div>
                            <small class="form-text text-muted">
                                Accepted formats: MP3, WAV, M4A, OGG. Maximum size: 10MB.
                            </small>
                        </div>
                    </div>
                    
                    <div id="multipleQuestionTiming" style="display: none;">
                        <div class="form-group row">
                            <div class="col-md-6">
                                <label for="audio_start_time">Audio Start Time (seconds)</label>
                                <input type="number" class="form-control" id="audio_start_time" name="audio_start_time" min="0" step="1">
                                <small class="form-text text-muted">Leave empty to start from the beginning</small>
                            </div>
                            <div class="col-md-6">
                                <label for="audio_end_time">Audio End Time (seconds)</label>
                                <input type="number" class="form-control" id="audio_end_time" name="audio_end_time" min="0" step="1">
                                <small class="form-text text-muted">Leave empty to end at the end of audio</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Options <span class="text-danger">*</span></label>
                        <div id="options-container">
                            <div class="input-group mb-2">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">A</div>
                                </div>
                                <input type="text" class="form-control" name="options[]" required>
                            </div>
                            <div class="input-group mb-2">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">B</div>
                                </div>
                                <input type="text" class="form-control" name="options[]" required>
                            </div>
                            <div class="input-group mb-2">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">C</div>
                                </div>
                                <input type="text" class="form-control" name="options[]" required>
                            </div>
                            <div class="input-group mb-2">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">D</div>
                                </div>
                                <input type="text" class="form-control" name="options[]" required>
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-secondary mt-2" id="add-option-btn">
                            <i class="fas fa-plus"></i> Add Option
                        </button>
                    </div>
                    
                    <div class="form-group">
                        <label for="correct_answer">Correct Answer <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="correct_answer" name="correct_answer" 
                            placeholder="Nhập đáp án đúng (ví dụ: A, B, C, D hoặc nội dung đáp án)" required>
                        <small class="form-text text-muted">
                            Bạn có thể nhập chữ cái (A, B, C, D) hoặc nội dung đáp án chính xác
                        </small>
                    </div>
                    
                    <div class="form-group">
                        <label for="order">Order <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="question_order" name="order" min="1" value="1" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Question</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Question Modal -->
<div class="modal fade" id="editQuestionModal" tabindex="-1" role="dialog" aria-labelledby="editQuestionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editQuestionModalLabel">Edit Question</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editQuestionForm" action="" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="form-group">
                        <label for="edit_question_text">Question Text <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="edit_question_text" name="question" rows="2" required></textarea>
                    </div>
                    
                    <div id="edit_singleQuestionAudio" style="display: none;">
                        <div class="form-group">
                            <label for="edit_question_audio_file">Question Audio File</label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="edit_question_audio_file" name="audio_file" accept="audio/mp3,audio/wav,audio/m4a,audio/ogg">
                                <label class="custom-file-label" for="edit_question_audio_file">Choose file</label>
                            </div>
                            <small class="form-text text-muted">
                                Leave empty to keep the current audio file. Accepted formats: MP3, WAV, M4A, OGG. Maximum size: 10MB.
                            </small>
                        </div>
                    </div>
                    
                    <div id="edit_multipleQuestionTiming" style="display: none;">
                        <div class="form-group row">
                            <div class="col-md-6">
                                <label for="edit_audio_start_time">Audio Start Time (seconds)</label>
                                <input type="number" class="form-control" id="edit_audio_start_time" name="audio_start_time" min="0" step="1">
                                <small class="form-text text-muted">Leave empty to start from the beginning</small>
                            </div>
                            <div class="col-md-6">
                                <label for="edit_audio_end_time">Audio End Time (seconds)</label>
                                <input type="number" class="form-control" id="edit_audio_end_time" name="audio_end_time" min="0" step="1">
                                <small class="form-text text-muted">Leave empty to end at the end of audio</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Options <span class="text-danger">*</span></label>
                        <div id="edit-options-container">
                            <!-- Will be populated dynamically -->
                        </div>
                        <button type="button" class="btn btn-sm btn-secondary mt-2" id="edit-add-option-btn">
                            <i class="fas fa-plus"></i> Add Option
                        </button>
                    </div>
                    
                    <div class="form-group">
                        <label for="edit_correct_answer">Correct Answer <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_correct_answer" name="correct_answer" 
                            placeholder="Nhập đáp án đúng (ví dụ: A, B, C, D hoặc nội dung đáp án)" required>
                        <small class="form-text text-muted">
                            Bạn có thể nhập chữ cái (A, B, C, D) hoặc nội dung đáp án chính xác
                        </small>
                    </div>
                    
                    <div class="form-group">
                        <label for="edit_question_order">Order <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="edit_question_order" name="order" min="1" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Question</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    console.log('JavaScript loaded successfully');
    
    // Test if Bootstrap modal is working
    $('#addSectionModal').on('show.bs.modal', function() {
        console.log('Add Section Modal is opening');
    });
    
    // File input label update with preview
    $('.custom-file-input').on('change', function() {
        const fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').addClass("selected").html(fileName);

        // Audio preview
        if (this.files && this.files[0]) {
            const file = this.files[0];
            if (file.type.startsWith('audio/')) {
                let audioPreview = $(this).closest('.form-group').find('.audio-preview');
                if (audioPreview.length === 0) {
                    audioPreview = $('<div class="audio-preview mt-2"></div>');
                    $(this).closest('.form-group').append(audioPreview);
                }

                const audioUrl = URL.createObjectURL(file);
                audioPreview.html(`
                    <label><strong>Preview:</strong></label>
                    <audio controls class="d-block w-100">
                        <source src="${audioUrl}" type="${file.type}">
                        Your browser does not support the audio element.
                    </audio>
                    <small class="text-muted">File: ${fileName} (${(file.size / 1024 / 1024).toFixed(2)} MB)</small>
                `);
            }
        }
    });

    // Delete confirmation
    $('.delete-section-form').on('submit', function(e) {
        e.preventDefault();
        if (confirm('Are you sure you want to delete this section? This will also delete all questions and audio files.')) {
            this.submit();
        }
    });
    
    $('.delete-question-form').on('submit', function(e) {
        e.preventDefault();
        if (confirm('Are you sure you want to delete this question?')) {
            this.submit();
        }
    });

    // Add Question Modal - Fix the form action setting
    $('#addQuestionModal').on('show.bs.modal', function(event) {
        console.log('Add Question Modal opening');
        const button = $(event.relatedTarget);
        const sectionId = button.data('section-id');
        const modal = $(this);
        
        // Reset form
        modal.find('form')[0].reset();
        modal.find('.audio-preview').remove();
        modal.find('.custom-file-label').removeClass('selected').html('Choose file');
        
        // Set form action - FIXED
        modal.find('#addQuestionForm').attr('action', '{{ route("admin.listening-questions.store", ":sectionId") }}'.replace(':sectionId', sectionId));
        modal.find('#question_section_id').val(sectionId);
        
        // Get section info from DOM
        const sectionCard = button.closest('.section-card');
        const questionType = sectionCard.find('.badge-info').text().toLowerCase();
        const questionCount = sectionCard.find('.question-card').length;
        
        modal.find('#question_order').val(questionCount + 1);
        
        // Show/hide appropriate fields based on question type
        if (questionType === 'single') {
            $('#singleQuestionAudio').show();
            $('#multipleQuestionTiming').hide();
            $('#question_audio_file').prop('required', true);
        } else {
            $('#singleQuestionAudio').hide();
            $('#multipleQuestionTiming').show();
            $('#question_audio_file').prop('required', false);
        }
    });

    // Edit Section Modal - Fix the form action setting
    $('.edit-section-btn').on('click', function() {
        console.log('Edit Section button clicked');
        const id = $(this).data('id');
        const title = $(this).data('title');
        const instructions = $(this).data('instructions');
        const questionType = $(this).data('question-type');
        const order = $(this).data('order');
        
        const form = $('#editSectionForm');
        // FIX THIS LINE - get test ID from current URL or data attribute
        const testId = {{ $test->id }};
        form.attr('action', '/admin/listening-tests/' + testId + '/sections/' + id);
        form.find('#edit_title').val(title);
        form.find('#edit_instructions').val(instructions);
        form.find('#edit_question_type').val(questionType);
        form.find('#edit_order').val(order);
        
        // Reset file input
        form.find('.custom-file-label').removeClass('selected').html('Choose file');
        form.find('.audio-preview').remove();
    });

    // Edit Question Modal - Fix the form action setting
    $('.edit-question-btn').on('click', function() {
        console.log('Edit Question button clicked');
        const id = $(this).data('id');
        const question = $(this).data('question');
        const options = $(this).data('options');
        const correctAnswer = $(this).data('correct-answer');
        const audioStartTime = $(this).data('audio-start-time');
        const audioEndTime = $(this).data('audio-end-time');
        const order = $(this).data('order');
        
        // Find section ID from the closest section card
        const sectionCard = $(this).closest('.section-card');
        const sectionId = sectionCard.find('[data-toggle="modal"][data-target="#addQuestionModal"]').data('section-id');
        
        const form = $('#editQuestionForm');
        form.attr('action', '{{ url("admin/listening-sections") }}/' + sectionId + '/questions/' + id);
        form.find('#edit_question_text').val(question);
        form.find('#edit_audio_start_time').val(audioStartTime || '');
        form.find('#edit_audio_end_time').val(audioEndTime || '');
        form.find('#edit_question_order').val(order);
        
        // Reset file input
        form.find('.custom-file-label').removeClass('selected').html('Choose file');
        form.find('.audio-preview').remove();
        
        // Populate options
        const optionsContainer = $('#edit-options-container');
        optionsContainer.empty();
        
        $.each(options, function(index, option) {
            const letter = String.fromCharCode(65 + index); // A, B, C, ...
            const removeBtn = index > 1 ? `
            <div class="input-group-append">
                <button type="button" class="btn btn-danger remove-option-btn">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        ` : '';
        
        const html = `
            <div class="input-group mb-2">
                <div class="input-group-prepend">
                    <div class="input-group-text">${letter}</div>
                </div>
                <input type="text" class="form-control edit-option" name="options[]" value="${option}" required>
                ${removeBtn}
            </div>
        `;
        optionsContainer.append(html);
    });
    
    form.find('#edit_correct_answer').val(correctAnswer);
    
    // Determine if we need to show audio upload or timing fields
    const sectionType = sectionCard.find('.badge-info').text().toLowerCase();
    if (sectionType === 'single') {
        $('#edit_singleQuestionAudio').show();
        $('#edit_multipleQuestionTiming').hide();
    } else {
        $('#edit_singleQuestionAudio').hide();
        $('#edit_multipleQuestionTiming').show();
    }
});

    // Add option button
    $('#add-option-btn').on('click', function() {
        const optionsCount = $('#options-container .input-group').length;
        const letter = String.fromCharCode(65 + optionsCount); // A, B, C, ...
        
        const html = `
            <div class="input-group mb-2">
                <div class="input-group-prepend">
                    <div class="input-group-text">${letter}</div>
                </div>
                <input type="text" class="form-control option" name="options[]" required>
                <div class="input-group-append">
                    <button type="button" class="btn btn-danger remove-option-btn">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        `;
        
        $('#options-container').append(html);
        updateCorrectAnswerOptions();
    });

    // Edit add option button
    $('#edit-add-option-btn').on('click', function() {
        const optionsCount = $('#edit-options-container .input-group').length;
        const letter = String.fromCharCode(65 + optionsCount); // A, B, C, ...
        
        const html = `
            <div class="input-group mb-2">
                <div class="input-group-prepend">
                    <div class="input-group-text">${letter}</div>
                </div>
                <input type="text" class="form-control edit-option" name="options[]" required>
                <div class="input-group-append">
                    <button type="button" class="btn btn-danger remove-option-btn">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        `;
        
        $('#edit-options-container').append(html);
        updateEditCorrectAnswerOptions();
    });

    // Remove option button (delegated event)
    $(document).on('click', '.remove-option-btn', function() {
        const container = $(this).closest('#options-container, #edit-options-container');
        const minOptions = 2;
        
        if (container.find('.input-group').length > minOptions) {
            $(this).closest('.input-group').remove();
            
            // Renumber the options
            container.find('.input-group-text').each(function(index) {
                $(this).text(String.fromCharCode(65 + index));
            });
            
            // Update correct answer options after removing
            if (container.attr('id') === 'options-container') {
                updateCorrectAnswerOptions();
            } else {
                updateEditCorrectAnswerOptions();
            }
        } else {
            alert('You must have at least 2 options.');
        }
    });

    // Update options when they change
    //$(document).on('input', '.option', function() {
       // updateCorrectAnswerOptions();
   // });
    
   // $(document).on('input', '.edit-option', function() {
       // updateEditCorrectAnswerOptions();
   // });

    // FIX: Updated function for Add Question Modal
    // function updateCorrectAnswerOptions() {
    //     const correctAnswerSelect = $('#correct_answer');
    //     const currentValue = correctAnswerSelect.val();
    //     correctAnswerSelect.empty();
        
    //     $('#options-container input[name="options[]"]').each(function(index) {
    //         const optionText = $(this).val().trim();
    //         if (optionText) {
    //             const selected = (optionText === currentValue) ? 'selected' : '';
    //             correctAnswerSelect.append(`<option value="${optionText}" ${selected}>${optionText}</option>`);
    //         }
    //     });
        
    //     // If no current value is selected and we have options, select the first one
    //     if (!correctAnswerSelect.val() && correctAnswerSelect.find('option').length > 0) {
    //         correctAnswerSelect.find('option').first().prop('selected', true);
    //     }
    // }

    // FIX: Updated function for Edit Question Modal - convert input to select
    // function updateEditCorrectAnswerOptions(selectedValue = null) {
    //     let correctAnswerSelect = $('#edit_correct_answer');
        
    //     // Convert input to select if it's currently an input
    //     if (correctAnswerSelect.is('input')) {
    //         const currentValue = selectedValue || correctAnswerSelect.val();
    //         correctAnswerSelect.replaceWith('<select class="form-control" id="edit_correct_answer" name="correct_answer" required></select>');
    //         correctAnswerSelect = $('#edit_correct_answer');
    //         selectedValue = currentValue;
    //     }
        
    //     correctAnswerSelect.empty();
        
    //     $('#edit-options-container input[name="options[]"]').each(function(index) {
    //         const optionText = $(this).val().trim();
    //         if (optionText) {
    //             const selected = (optionText === selectedValue) ? 'selected' : '';
    //             correctAnswerSelect.append(`<option value="${optionText}" ${selected}>${optionText}</option>`);
    //         }
    //     });
        
    //     // If no value is selected and we have options, select the first one
    //     if (!correctAnswerSelect.val() && correctAnswerSelect.find('option').length > 0) {
    //         correctAnswerSelect.find('option').first().prop('selected', true);
    //     }
    // }

    // Form validation before submit
    $('form').on('submit', function(e) {
        const form = $(this);
        
        // Check if audio file is required but not provided
        const requiredAudioInput = form.find('input[type="file"][required]');
        if (requiredAudioInput.length > 0 && !requiredAudioInput[0].files.length) {
            e.preventDefault();
            alert('Please select an audio file.');
            return false;
        }
        
        // Check file size and type
        let hasError = false;
        form.find('input[type="file"]').each(function() {
            if (this.files.length > 0) {
                const file = this.files[0];
                const maxSize = 20 * 1024 * 1024; // 20MB
                
                if (file.size > maxSize) {
                    e.preventDefault();
                    alert(`File size too large. Maximum allowed size is 20MB.`);
                    hasError = true;
                    return false;
                }
                
                if (!file.type.startsWith('audio/')) {
                    e.preventDefault();
                    alert('Please select a valid audio file.');
                    hasError = true;
                    return false;
                }
            }
        });
        
        return !hasError;
    });
    
    // Debug: Test modal trigger
    $('[data-toggle="modal"]').on('click', function() {
        console.log('Modal trigger clicked:', $(this).data('target'));
    });
});
</script>
@endsection
