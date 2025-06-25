@extends('admin.layouts.app')

@section('title', 'Chỉnh sửa Quiz')
@section('page-title', 'Chỉnh sửa Quiz')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4>Chỉnh sửa Quiz: {{ $quiz->title }}</h4>
    <div>
        <a href="{{ route('admin.quizzes.show', $quiz) }}" class="btn btn-info">
            <i class="fas fa-eye"></i> Xem chi tiết
        </a>
        <a href="{{ route('admin.quizzes.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Quay lại
        </a>
    </div>
</div>

<div class="row">
    <!-- Form chỉnh sửa thông tin quiz -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-edit"></i> Thông tin Quiz</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.quizzes.update', $quiz) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label for="title" class="form-label">Tiêu đề Quiz <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror" 
                               id="title" name="title" value="{{ old('title', $quiz->title) }}" required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="lesson_id" class="form-label">Bài học <span class="text-danger">*</span></label>
                        <select class="form-select @error('lesson_id') is-invalid @enderror" 
                                id="lesson_id" name="lesson_id" required>
                            <option value="">Chọn bài học</option>
                            @foreach($lessons as $lesson)
                                <option value="{{ $lesson->id }}" 
                                        {{ old('lesson_id', $quiz->lesson_id) == $lesson->id ? 'selected' : '' }}>
                                    {{ $lesson->title }}
                                </option>
                            @endforeach
                        </select>
                        @error('lesson_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Mô tả</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" name="description" rows="4">{{ old('description', $quiz->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Cập nhật Quiz
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Thông tin bổ sung -->
    <div class="col-md-4">
        <div class="card mb-3">
            <div class="card-header">
                <h5><i class="fas fa-info-circle"></i> Thông tin</h5>
            </div>
            <div class="card-body">
                <p><strong>ID:</strong> {{ $quiz->id }}</p>
                <p><strong>Số câu hỏi:</strong> {{ $quiz->questions()->count() }}</p>
                <p><strong>Ngày tạo:</strong> {{ $quiz->created_at->format('d/m/Y H:i') }}</p>
                <p><strong>Cập nhật:</strong> {{ $quiz->updated_at->format('d/m/Y H:i') }}</p>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-question-circle"></i> Quản lý câu hỏi</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.quizzes.questions', $quiz) }}" class="btn btn-primary">
                        <i class="fas fa-list"></i> Danh sách câu hỏi
                    </a>
                    <a href="{{ route('admin.quizzes.create-question', $quiz) }}" class="btn btn-success">
                        <i class="fas fa-plus"></i> Thêm câu hỏi mới
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Danh sách câu hỏi hiện có -->
@if($quiz->questions()->count() > 0)
<div class="card mt-4">
    <div class="card-header">
        <h5><i class="fas fa-list"></i> Câu hỏi hiện có ({{ $quiz->questions()->count() }} câu)</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th width="5%">#</th>
                        <th width="60%">Câu hỏi</th>
                        <th width="15%">Số lựa chọn</th>
                        <th width="20%">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($quiz->questions()->limit(10)->get() as $index => $question)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ Str::limit($question->question, 80) }}</td>
                        <td>{{ count($question->options) }}</td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('admin.quizzes.edit-question', [$quiz, $question]) }}" 
                                   class="btn btn-sm btn-primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.quizzes.destroy-question', [$quiz, $question]) }}" 
                                      method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" 
                                            onclick="return confirm('Bạn có chắc chắn muốn xóa câu hỏi này?')">
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
        
        @if($quiz->questions()->count() > 10)
        <div class="text-center">
            <a href="{{ route('admin.quizzes.questions', $quiz) }}" class="btn btn-outline-primary">
                Xem tất cả {{ $quiz->questions()->count() }} câu hỏi
            </a>
        </div>
        @endif
    </div>
</div>
@endif

<!-- Form thêm câu hỏi nhanh -->
<div class="card mt-4">
    <div class="card-header">
        <h5><i class="fas fa-plus-circle"></i> Thêm câu hỏi nhanh</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.quizzes.store-question', $quiz) }}" method="POST" id="quickQuestionForm">
            @csrf
            
            <div class="mb-3">
                <label for="question" class="form-label">Câu hỏi <span class="text-danger">*</span></label>
                <textarea class="form-control @error('question') is-invalid @enderror" 
                          id="question" name="question" rows="3" required>{{ old('question') }}</textarea>
                @error('question')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Các lựa chọn <span class="text-danger">*</span></label>
                <div id="optionsContainer">
                    <div class="input-group mb-2">
                        <input type="text" class="form-control" name="options[]" placeholder="Lựa chọn 1" required>
                        <button type="button" class="btn btn-outline-danger" onclick="removeOption(this)" disabled>
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                    <div class="input-group mb-2">
                        <input type="text" class="form-control" name="options[]" placeholder="Lựa chọn 2" required>
                        <button type="button" class="btn btn-outline-danger" onclick="removeOption(this)">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <button type="button" class="btn btn-outline-primary btn-sm" onclick="addOption()">
                    <i class="fas fa-plus"></i> Thêm lựa chọn
                </button>
            </div>

            <div class="mb-3">
                <label for="correct_answer" class="form-label">Đáp án đúng <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('correct_answer') is-invalid @enderror" 
                       id="correct_answer" name="correct_answer" placeholder="Nhập đáp án đúng" required>
                <div class="form-text">Nhập chính xác nội dung của một trong các lựa chọn ở trên</div>
                @error('correct_answer')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-plus"></i> Thêm câu hỏi
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function addOption() {
    const container = document.getElementById('optionsContainer');
    const optionCount = container.children.length + 1;
    
    const newOption = document.createElement('div');
    newOption.className = 'input-group mb-2';
    newOption.innerHTML = `
        <input type="text" class="form-control" name="options[]" placeholder="Lựa chọn ${optionCount}" required>
        <button type="button" class="btn btn-outline-danger" onclick="removeOption(this)">
            <i class="fas fa-minus"></i>
        </button>
    `;
    
    container.appendChild(newOption);
    updateRemoveButtons();
}

function removeOption(button) {
    const container = document.getElementById('optionsContainer');
    if (container.children.length > 2) {
        button.parentElement.remove();
        updateRemoveButtons();
        updatePlaceholders();
    }
}

function updateRemoveButtons() {
    const container = document.getElementById('optionsContainer');
    const removeButtons = container.querySelectorAll('.btn-outline-danger');
    
    removeButtons.forEach((button, index) => {
        button.disabled = container.children.length <= 2;
    });
}

function updatePlaceholders() {
    const container = document.getElementById('optionsContainer');
    const inputs = container.querySelectorAll('input[name="options[]"]');
    
    inputs.forEach((input, index) => {
        input.placeholder = `Lựa chọn ${index + 1}`;
    });
}

// Auto-fill correct answer when user clicks on an option
document.addEventListener('DOMContentLoaded', function() {
    const optionsContainer = document.getElementById('optionsContainer');
    const correctAnswerInput = document.getElementById('correct_answer');
    
    optionsContainer.addEventListener('focusout', function(e) {
        if (e.target.name === 'options[]' && e.target.value.trim() !== '') {
            if (correctAnswerInput.value.trim() === '') {
                correctAnswerInput.value = e.target.value.trim();
            }
        }
    });
});
</script>
@endsection