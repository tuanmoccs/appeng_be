@extends('admin.layouts.app')

@section('title', 'Thêm Câu hỏi')
@section('page-title', 'Thêm Câu hỏi cho Quiz: ' . $quiz->title)

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5>Thông tin Câu hỏi</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.quizzes.questions.store', $quiz) }}" method="POST">
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
                        <label class="form-label">Lựa chọn <span class="text-danger">*</span></label>
                        <div id="options-container">
                            @for($i = 0; $i < 4; $i++)
                                <div class="input-group mb-2">
                                    <span class="input-group-text">{{ chr(65 + $i) }}</span>
                                    <input type="text" class="form-control @error('options.' . $i) is-invalid @enderror" 
                                           name="options[]" value="{{ old('options.' . $i) }}" required>
                                    @if($i >= 2)
                                        <button type="button" class="btn btn-outline-danger remove-option">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                    @endif
                                </div>
                            @endfor
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-primary" id="add-option">
                            <i class="fas fa-plus"></i> Thêm lựa chọn
                        </button>
                        @error('options')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="correct_answer" class="form-label">Đáp án đúng <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('correct_answer') is-invalid @enderror" 
                               id="correct_answer" name="correct_answer" value="{{ old('correct_answer') }}" required>
                        <div class="form-text">Nhập chính xác nội dung của đáp án đúng</div>
                        @error('correct_answer')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.quizzes.questions', $quiz) }}" class="btn btn-secondary">Hủy</a>
                        <button type="submit" class="btn btn-primary">Thêm Câu hỏi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h6>Thông tin Quiz</h6>
            </div>
            <div class="card-body">
                <p><strong>Tiêu đề:</strong> {{ $quiz->title }}</p>
                <p><strong>Bài học:</strong> {{ $quiz->lesson->title }}</p>
                <p><strong>Số câu hỏi hiện tại:</strong> {{ $quiz->questions()->count() }}</p>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <h6>Hướng dẫn</h6>
            </div>
            <div class="card-body">
                <ul class="list-unstyled">
                    <li>• Câu hỏi nên rõ ràng, dễ hiểu</li>
                    <li>• Tối thiểu 2 lựa chọn, tối đa 6 lựa chọn</li>
                    <li>• Đáp án đúng phải khớp chính xác với một trong các lựa chọn</li>
                </ul>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let optionCount = 4;
    const maxOptions = 6;
    
    // Add option
    document.getElementById('add-option').addEventListener('click', function() {
        if (optionCount < maxOptions) {
            const container = document.getElementById('options-container');
            const newOption = document.createElement('div');
            newOption.className = 'input-group mb-2';
            newOption.innerHTML = `
                <span class="input-group-text">${String.fromCharCode(65 + optionCount)}</span>
                <input type="text" class="form-control" name="options[]" required>
                <button type="button" class="btn btn-outline-danger remove-option">
                    <i class="fas fa-minus"></i>
                </button>
            `;
            container.appendChild(newOption);
            optionCount++;
            
            if (optionCount >= maxOptions) {
                this.style.display = 'none';
            }
        }
    });
    
    // Remove option
    document.addEventListener('click', function(e) {
        if (e.target.closest('.remove-option')) {
            if (optionCount > 2) {
                e.target.closest('.input-group').remove();
                optionCount--;
                document.getElementById('add-option').style.display = 'inline-block';
                
                // Update labels
                const options = document.querySelectorAll('#options-container .input-group-text');
                options.forEach((label, index) => {
                    label.textContent = String.fromCharCode(65 + index);
                });
            }
        }
    });
});
</script>
@endsection
@endsection
