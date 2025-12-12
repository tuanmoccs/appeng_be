@extends('admin.layouts.app')

@section('title', 'Thêm Câu hỏi cho Bài đọc')
@section('page-title', 'Thêm Câu hỏi cho Bài đọc')

@section('content')
<div class="row mb-3">
    <div class="col-md-12">
        <a href="{{ route('admin.tests.passages.show', [$test, $passage]) }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Quay lại bài đọc
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5>Thông tin Câu hỏi</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.tests.passages.store-question', [$test, $passage]) }}" method="POST">
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
                        <div id="options-container">
                            @for($i = 0; $i < 4; $i++)
                                <div class="input-group mb-2">
                                    <span class="input-group-text">{{ chr(65 + $i) }}</span>
                                    <input type="text" class="form-control @error('options.' . $i) is-invalid @enderror" 
                                           name="options[]" value="{{ old('options.' . $i) }}" 
                                           placeholder="Nhập lựa chọn {{ chr(65 + $i) }}"
                                           {{ $i < 2 ? 'required' : '' }}>
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

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="correct_answer" class="form-label">Đáp án đúng <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('correct_answer') is-invalid @enderror" 
                                       id="correct_answer" name="correct_answer" value="{{ old('correct_answer') }}" 
                                       placeholder="Nhập chính xác nội dung đáp án đúng" required>
                                <div class="form-text">Nhập chính xác nội dung của đáp án đúng (phải khớp với một trong các lựa chọn)</div>
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
                                    <option value="easy" {{ old('difficulty') == 'easy' ? 'selected' : '' }}>Dễ</option>
                                    <option value="medium" {{ old('difficulty') == 'medium' ? 'selected' : '' }}>Trung bình</option>
                                    <option value="hard" {{ old('difficulty') == 'hard' ? 'selected' : '' }}>Khó</option>
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
                                       id="order" name="order" value="{{ old('order', $nextOrder) }}" 
                                       min="1" required>
                                @error('order')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.tests.passages.show', [$test, $passage]) }}" class="btn btn-secondary">Hủy</a>
                        <div>
                            <button type="submit" name="action" value="save" class="btn btn-primary">
                                <i class="fas fa-save"></i> Lưu câu hỏi
                            </button>
                            <button type="submit" name="action" value="save_and_new" class="btn btn-success">
                                <i class="fas fa-plus"></i> Lưu và thêm câu tiếp
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <!-- Thông tin bài đọc -->
        <div class="card mb-3">
            <div class="card-header bg-info text-white">
                <h6 class="mb-0"><i class="fas fa-book-open"></i> Bài đọc</h6>
            </div>
            <div class="card-body">
                <p><strong>Tiêu đề:</strong> {{ $passage->title ?: 'Không có tiêu đề' }}</p>
                <p><strong>Thứ tự:</strong> {{ $passage->order }}</p>
                <p><strong>Số câu hỏi hiện tại:</strong> {{ $passage->questions()->count() }}</p>
                <hr>
                <p class="small text-muted mb-0"><strong>Nội dung:</strong></p>
                <div class="small text-muted" style="max-height: 150px; overflow-y: auto;">
                    {{ Str::limit($passage->content, 300) }}
                </div>
            </div>
        </div>

        <!-- Thông tin Test -->
        <div class="card mb-3">
            <div class="card-header">
                <h6 class="mb-0">Thông tin Test</h6>
            </div>
            <div class="card-body">
                <p><strong>Tiêu đề:</strong> {{ $test->title }}</p>
                <p><strong>Loại:</strong> {{ ucfirst($test->type) }}</p>
                <p><strong>Tổng câu hỏi cần:</strong> {{ $test->total_questions }}</p>
                <p><strong>Câu hỏi hiện tại:</strong> {{ $test->questions()->count() }}</p>
                
                <div class="progress mb-2">
                    @php $progress = min(($test->questions()->count() / $test->total_questions) * 100, 100); @endphp
                    <div class="progress-bar {{ $progress >= 100 ? 'bg-success' : '' }}" role="progressbar" 
                         style="width: {{ $progress }}%;" aria-valuenow="{{ $progress }}" 
                         aria-valuemin="0" aria-valuemax="100">
                        {{ round($progress) }}%
                    </div>
                </div>
                <small class="text-muted">Tiến độ hoàn thành</small>
            </div>
        </div>

        <!-- Hướng dẫn -->
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Hướng dẫn</h6>
            </div>
            <div class="card-body">
                <ul class="list-unstyled small mb-0">
                    <li class="mb-2">• Câu hỏi nên liên quan đến nội dung bài đọc</li>
                    <li class="mb-2">• Tối thiểu 2 lựa chọn, tối đa 6 lựa chọn</li>
                    <li class="mb-2">• Đáp án đúng phải khớp chính xác với một trong các lựa chọn</li>
                    <li class="mb-2">• Chọn độ khó phù hợp với nội dung</li>
                    <li>• Thứ tự câu hỏi trong bài đọc này</li>
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
                <input type="text" class="form-control" name="options[]" placeholder="Nhập lựa chọn ${String.fromCharCode(65 + optionCount)}">
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
                
                // Update placeholders
                const inputs = document.querySelectorAll('#options-container input[name="options[]"]');
                inputs.forEach((input, index) => {
                    input.placeholder = `Nhập lựa chọn ${String.fromCharCode(65 + index)}`;
                });
            }
        }
    });
    
    // Auto-fill correct answer when clicking on option
    document.querySelectorAll('#options-container input[name="options[]"]').forEach(input => {
        input.addEventListener('dblclick', function() {
            document.getElementById('correct_answer').value = this.value;
        });
    });
});
</script>
@endsection
@endsection
