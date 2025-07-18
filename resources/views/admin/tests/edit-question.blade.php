@extends('admin.layouts.app')

@section('title', 'Chỉnh sửa Câu hỏi Test')
@section('page-title', 'Chỉnh sửa Câu hỏi')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5>Chỉnh sửa Câu hỏi</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.tests.questions.update', [$test, $question]) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label for="question" class="form-label">Câu hỏi <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('question') is-invalid @enderror" 
                                  id="question" name="question" rows="3" required>{{ old('question', $question->question) }}</textarea>
                        @error('question')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Lựa chọn <span class="text-danger">*</span></label>
                        <div id="options-container">
                            @foreach($question->options as $index => $option)
                                <div class="input-group mb-2">
                                    <span class="input-group-text">{{ chr(65 + $index) }}</span>
                                    <input type="text" class="form-control @error('options.' . $index) is-invalid @enderror" 
                                           name="options[]" value="{{ old('options.' . $index, $option) }}" required>
                                    @if($index >= 2)
                                        <button type="button" class="btn btn-outline-danger remove-option">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-primary" id="add-option">
                            <i class="fas fa-plus"></i> Thêm lựa chọn
                        </button>
                        @error('options')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="correct_answer" class="form-label">Đáp án đúng <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('correct_answer') is-invalid @enderror" 
                                       id="correct_answer" name="correct_answer" value="{{ old('correct_answer', $question->correct_answer) }}" required>
                                <div class="form-text">Nhập chính xác nội dung của đáp án đúng</div>
                                @error('correct_answer')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="difficulty" class="form-label">Độ khó <span class="text-danger">*</span></label>
                                <select class="form-select @error('difficulty') is-invalid @enderror" id="difficulty" name="difficulty" required>
                                    <option value="">Chọn độ khó</option>
                                    <option value="easy" {{ old('difficulty', $question->difficulty) == 'easy' ? 'selected' : '' }}>Dễ</option>
                                    <option value="medium" {{ old('difficulty', $question->difficulty) == 'medium' ? 'selected' : '' }}>Trung bình</option>
                                    <option value="hard" {{ old('difficulty', $question->difficulty) == 'hard' ? 'selected' : '' }}>Khó</option>
                                </select>
                                @error('difficulty')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="order" class="form-label">Thứ tự <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('order') is-invalid @enderror" 
                                       id="order" name="order" value="{{ old('order', $question->order) }}" 
                                       min="1" required>
                                @error('order')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.tests.questions', $test) }}" class="btn btn-secondary">Hủy</a>
                        <button type="submit" class="btn btn-primary">Cập nhật</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h6>Thông tin</h6>
            </div>
            <div class="card-body">
                <p><strong>Test:</strong> {{ $test->title }}</p>
                <p><strong>Loại:</strong> {{ ucfirst($test->type) }}</p>
                <p><strong>ID câu hỏi:</strong> {{ $question->id }}</p>
                <p><strong>Ngày tạo:</strong> {{ $question->created_at->format('d/m/Y H:i') }}</p>
                <p><strong>Cập nhật lần cuối:</strong> {{ $question->updated_at->format('d/m/Y H:i') }}</p>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <h6>Hành động nguy hiểm</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.tests.questions.destroy', [$test, $question]) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger w-100" 
                            onclick="return confirm('Bạn có chắc chắn muốn xóa câu hỏi này?')">
                        <i class="fas fa-trash"></i> Xóa câu hỏi này
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let optionCount = {{ count($question->options) }};
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
