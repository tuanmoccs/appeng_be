@extends('admin.layouts.app')

@section('title', 'Tạo Bài học')
@section('page-title', 'Tạo Bài học')

@section('content')
<style>
    .section-card {
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
        background: #f8f9fa;
    }
    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
        padding-bottom: 10px;
        border-bottom: 2px solid #007bff;
    }
    .section-header span {
        font-weight: bold;
        font-size: 1.1em;
    }
    .btn-remove {
        background: #dc3545;
        color: white;
        border: none;
        padding: 5px 15px;
        border-radius: 4px;
        cursor: pointer;
    }
    .btn-remove:hover {
        background: #c82333;
    }
    .vocab-item, .example-item, .exercise-item, .pair-item {
        background: white;
        border: 1px solid #dee2e6;
        border-radius: 6px;
        padding: 15px;
        margin-bottom: 10px;
    }
    .question-item {
        background: white;
        border: 1px solid #dee2e6;
        border-radius: 6px;
        padding: 20px;
        margin-bottom: 15px;
    }
    .preview-box {
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 4px;
        padding: 15px;
        max-height: 400px;
        overflow-y: auto;
    }
    .sub-section {
        background: #fff;
        border: 1px solid #e0e0e0;
        border-radius: 6px;
        padding: 15px;
        margin-top: 10px;
    }
</style>

<div class="container mt-4">
    <h2>Tạo Bài học mới - Visual Editor (Full JSON Support)</h2>
    
    <form id="lessonForm" method="POST" action="/admin/lessons">
        @csrf
        <!-- Basic Info -->
        <div class="card mb-4">
            <div class="card-header">
                <h5>Thông tin cơ bản</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Tiêu đề *</label>
                    <input type="text" class="form-control" id="title" name="title" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Mô tả *</label>
                    <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <label class="form-label">Cấp độ *</label>
                        <select class="form-select" id="level" name="level" required>
                            <option value="beginner">Beginner</option>
                            <option value="intermediate">Intermediate</option>
                            <option value="advanced">Advanced</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Thời gian (phút) *</label>
                        <input type="number" class="form-control" id="duration" name="duration" min="1" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Thứ tự *</label>
                        <input type="number" class="form-control" id="order" name="order" min="1" required>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabs -->
        <ul class="nav nav-tabs" id="editorTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="content-tab" type="button" onclick="switchTab('content')">
                    Nội dung bài học
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="quiz-tab" type="button" onclick="switchTab('quiz')">
                    Quiz kiểm tra
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="preview-tab" type="button" onclick="switchTab('preview')">
                    Xem trước JSON
                </button>
            </li>
        </ul>

        <div class="tab-content" id="editorTabContent">
            <!-- Content Tab -->
            <div class="tab-pane fade show active" id="content">
                <div class="p-3">
                    <button type="button" class="btn btn-primary mb-3" onclick="addSection()">
                        + Thêm Section
                    </button>
                    <div id="sectionsContainer"></div>
                </div>
            </div>

            <!-- Quiz Tab -->
            <div class="tab-pane fade" id="quiz">
                <div class="p-3">
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Tiêu đề Quiz *</label>
                                <input type="text" class="form-control" id="quizTitle" value="Quiz">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Mô tả Quiz</label>
                                <textarea class="form-control" id="quizDescription" rows="2"></textarea>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="form-label">Thời gian (phút)</label>
                                    <input type="number" class="form-control" id="quizTimeLimit" value="15" min="1">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Điểm đạt (%)</label>
                                    <input type="number" class="form-control" id="quizPassingScore" value="80" min="0" max="100">
                                </div>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-success mb-3" onclick="addQuestion()">
                        + Thêm câu hỏi
                    </button>
                    <div id="questionsContainer"></div>
                </div>
            </div>

            <!-- Preview Tab -->
            <div class="tab-pane fade" id="preview">
                <div class="p-3">
                    <h5>Content JSON:</h5>
                    <pre id="contentPreview" class="preview-box"></pre>
                    <h5 class="mt-3">Quiz JSON:</h5>
                    <pre id="quizPreview" class="preview-box"></pre>
                </div>
            </div>
        </div>

        <!-- Hidden inputs -->
        <input type="hidden" id="contentJson" name="content">
        <input type="hidden" id="quizJson" name="quiz">

        <div class="mt-4 mb-5">
            <button type="submit" class="btn btn-primary btn-lg">Tạo bài học</button>
            <a href="/admin/lessons" class="btn btn-secondary btn-lg">Hủy</a>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    let sectionCounter = 0;
    let questionCounter = 0;
    const sections = [];
    function switchTab(tabName) {
        // Remove active from all tabs
        document.querySelectorAll('.nav-link').forEach(tab => tab.classList.remove('active'));
        document.querySelectorAll('.tab-pane').forEach(pane => {
            pane.classList.remove('show', 'active');
        });

        // Activate selected tab
        document.getElementById(tabName + '-tab').classList.add('active');
        document.getElementById(tabName).classList.add('show', 'active');

        // Update preview tab
        if (tabName === 'preview') {
            const {contentJSON, quizJSON} = generateJSON();
            document.getElementById('contentPreview').textContent = JSON.stringify(contentJSON, null, 2);
            document.getElementById('quizPreview').textContent = JSON.stringify(quizJSON, null, 2);
        }
    }


    function addSection() {
        const id = ++sectionCounter;
        const html = `
            <div class="section-card" id="section-${id}">
                <div class="section-header">
                    <span>Section ${id}</span>
                    <button type="button" class="btn-remove" onclick="removeSection(${id})">Xóa Section</button>
                </div>
                <div class="mb-3">
                    <label class="form-label">Loại section *</label>
                    <select class="form-select" id="sectionType-${id}" onchange="handleSectionTypeChange(${id})">
                        <option value="theory">Lý thuyết</option>
                        <option value="vocabulary">Từ vựng</option>
                        <option value="practice">Luyện tập</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Tiêu đề section *</label>
                    <input type="text" class="form-control" id="sectionTitle-${id}">
                </div>
                <div id="sectionContent-${id}"></div>
            </div>
        `;
        document.getElementById('sectionsContainer').insertAdjacentHTML('beforeend', html);
        sections.push({id, type: 'theory', title: ''});
        handleSectionTypeChange(id);
    }

    function handleSectionTypeChange(id) {
        const type = document.getElementById(`sectionType-${id}`).value;
        const section = sections.find(s => s.id === id);
        section.type = type;
        
        let html = '';
        if (type === 'theory') {
            html = `
                <div class="mb-3">
                    <label class="form-label">Nội dung lý thuyết *</label>
                    <textarea class="form-control" id="theoryContent-${id}" rows="5"></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">URL hình ảnh</label>
                    <input type="text" class="form-control" id="theoryImage-${id}">
                </div>
                <div class="sub-section">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <strong>Ví dụ (Examples)</strong>
                        <button type="button" class="btn btn-sm btn-success" onclick="addExample(${id})">+ Thêm ví dụ</button>
                    </div>
                    <div id="examplesContainer-${id}"></div>
                </div>
            `;
        } else if (type === 'vocabulary') {
            html = `
                <button type="button" class="btn btn-sm btn-success mb-2" onclick="addVocabItem(${id})">
                    + Thêm từ vựng
                </button>
                <div id="vocabItems-${id}"></div>
            `;
        } else if (type === 'practice') {
            html = `
                <div class="sub-section">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <strong>Bài tập (Exercises)</strong>
                        <button type="button" class="btn btn-sm btn-success" onclick="addExercise(${id})">+ Thêm bài tập</button>
                    </div>
                    <div id="exercisesContainer-${id}"></div>
                </div>
            `;
        }
        document.getElementById(`sectionContent-${id}`).innerHTML = html;
    }

    // ===== THEORY EXAMPLES =====
    function addExample(sectionId) {
        const itemId = Date.now();
        const html = `
            <div class="example-item" id="example-${sectionId}-${itemId}">
                <div class="row">
                    <div class="col-md-12 mb-2">
                        <label class="form-label small">Câu ví dụ (tiếng Anh)</label>
                        <input type="text" class="form-control" placeholder="I go to school every day." id="exampleSentence-${sectionId}-${itemId}">
                    </div>
                    <div class="col-md-10 mb-2">
                        <label class="form-label small">Bản dịch</label>
                        <input type="text" class="form-control" placeholder="Tôi đi học mỗi ngày." id="exampleTranslation-${sectionId}-${itemId}">
                    </div>
                    <div class="col-md-2 mb-2">
                        <label class="form-label small">Từ highlight</label>
                        <input type="text" class="form-control" placeholder="go" id="exampleHighlight-${sectionId}-${itemId}">
                    </div>
                    <div class="col-md-12">
                        <button type="button" class="btn-remove btn-sm" onclick="removeExample(${sectionId}, ${itemId})">Xóa</button>
                    </div>
                </div>
            </div>
        `;
        document.getElementById(`examplesContainer-${sectionId}`).insertAdjacentHTML('beforeend', html);
    }

    function removeExample(sectionId, itemId) {
        document.getElementById(`example-${sectionId}-${itemId}`).remove();
    }

    // ===== VOCABULARY =====
    function addVocabItem(sectionId) {
        const itemId = Date.now();
        const html = `
            <div class="vocab-item" id="vocab-${sectionId}-${itemId}">
                <div class="row">
                    <div class="col-md-4 mb-2">
                        <label class="form-label small">Từ vựng *</label>
                        <input type="text" class="form-control" placeholder="always" id="vocabWord-${sectionId}-${itemId}">
                    </div>
                    <div class="col-md-4 mb-2">
                        <label class="form-label small">Phiên âm</label>
                        <input type="text" class="form-control" placeholder="/ˈɔːl.weɪz/" id="vocabPronun-${sectionId}-${itemId}">
                    </div>
                    <div class="col-md-4 mb-2">
                        <label class="form-label small">Nghĩa *</label>
                        <input type="text" class="form-control" placeholder="luôn luôn" id="vocabMeaning-${sectionId}-${itemId}">
                    </div>
                    <div class="col-md-6 mb-2">
                        <label class="form-label small">URL hình ảnh</label>
                        <input type="text" class="form-control" id="vocabImage-${sectionId}-${itemId}">
                    </div>
                    <div class="col-md-6 mb-2">
                        <label class="form-label small">URL audio</label>
                        <input type="text" class="form-control" id="vocabAudio-${sectionId}-${itemId}">
                    </div>
                    <div class="col-md-12 mb-2">
                        <label class="form-label small">Câu ví dụ (tiếng Anh)</label>
                        <input type="text" class="form-control" placeholder="I always brush my teeth." id="vocabExample-${sectionId}-${itemId}">
                    </div>
                    <div class="col-md-12 mb-2">
                        <label class="form-label small">Bản dịch ví dụ</label>
                        <input type="text" class="form-control" placeholder="Tôi luôn đánh răng." id="vocabExampleTrans-${sectionId}-${itemId}">
                    </div>
                    <div class="col-md-12">
                        <button type="button" class="btn-remove btn-sm" onclick="removeVocabItem(${sectionId}, ${itemId})">Xóa</button>
                    </div>
                </div>
            </div>
        `;
        document.getElementById(`vocabItems-${sectionId}`).insertAdjacentHTML('beforeend', html);
    }

    function removeVocabItem(sectionId, itemId) {
        document.getElementById(`vocab-${sectionId}-${itemId}`).remove();
    }

    // ===== PRACTICE EXERCISES =====
    function addExercise(sectionId) {
        const itemId = Date.now();
        const html = `
            <div class="exercise-item" id="exercise-${sectionId}-${itemId}">
                <div class="row">
                    <div class="col-md-12 mb-2">
                        <label class="form-label small">Hướng dẫn</label>
                        <input type="text" class="form-control" placeholder="Điền vào chỗ trống" id="exerciseInstruction-${sectionId}-${itemId}">
                    </div>
                    <div class="col-md-12 mb-2">
                        <label class="form-label small">Câu hỏi *</label>
                        <input type="text" class="form-control" placeholder="She ___ to school every day." id="exerciseSentence-${sectionId}-${itemId}">
                    </div>
                    <div class="col-md-12 mb-2">
                        <label class="form-label small">Đáp án đúng *</label>
                        <input type="text" class="form-control" placeholder="goes" id="exerciseAnswer-${sectionId}-${itemId}">
                    </div>
                    <div class="col-md-12 mb-2">
                        <label class="form-label small">Các lựa chọn (phân cách bằng dấu |)</label>
                        <input type="text" class="form-control" placeholder="go|goes|going|went" id="exerciseOptions-${sectionId}-${itemId}">
                    </div>
                    <div class="col-md-12">
                        <button type="button" class="btn-remove btn-sm" onclick="removeExercise(${sectionId}, ${itemId})">Xóa</button>
                    </div>
                </div>
            </div>
        `;
        document.getElementById(`exercisesContainer-${sectionId}`).insertAdjacentHTML('beforeend', html);
    }

    function removeExercise(sectionId, itemId) {
        document.getElementById(`exercise-${sectionId}-${itemId}`).remove();
    }

    function removeSection(id) {
        document.getElementById(`section-${id}`).remove();
        const index = sections.findIndex(s => s.id === id);
        if (index > -1) sections.splice(index, 1);
    }

    // ===== QUIZ QUESTIONS =====
    function addQuestion() {
        const id = ++questionCounter;
        const html = `
            <div class="question-item" id="question-${id}">
                <div class="d-flex justify-content-between mb-3">
                    <strong>Câu hỏi ${id}</strong>
                    <button type="button" class="btn-remove btn-sm" onclick="removeQuestion(${id})">Xóa câu hỏi</button>
                </div>
                <div class="mb-2">
                    <label class="form-label">Loại câu hỏi *</label>
                    <select class="form-select" id="questionType-${id}" onchange="handleQuestionTypeChange(${id})">
                        <option value="multiple_choice">Trắc nghiệm (Multiple Choice)</option>
                        <option value="true_false">Đúng/Sai (True/False)</option>
                        <option value="fill_blank">Điền vào chỗ trống (Fill Blank)</option>
                        <option value="matching">Nối câu (Matching)</option>
                    </select>
                </div>
                <div class="mb-2">
                    <label class="form-label">Câu hỏi *</label>
                    <textarea class="form-control" id="questionText-${id}" rows="2"></textarea>
                </div>
                <div class="mb-2">
                    <label class="form-label">URL hình ảnh (tùy chọn)</label>
                    <input type="text" class="form-control" id="questionImage-${id}">
                </div>
                <div id="questionOptionsContainer-${id}"></div>
                <div class="mb-2">
                    <label class="form-label">Đáp án đúng *</label>
                    <input type="text" class="form-control" id="correctAnswer-${id}">
                </div>
                <div class="mb-2">
                    <label class="form-label">Giải thích</label>
                    <textarea class="form-control" id="explanation-${id}" rows="2"></textarea>
                </div>
            </div>
        `;
        document.getElementById('questionsContainer').insertAdjacentHTML('beforeend', html);
        handleQuestionTypeChange(id);
    }

    function handleQuestionTypeChange(id) {
        const type = document.getElementById(`questionType-${id}`).value;
        const container = document.getElementById(`questionOptionsContainer-${id}`);
        
        let html = '';
        if (type === 'multiple_choice') {
            html = `
                <div class="mb-2">
                    <label class="form-label">Các lựa chọn *</label>
                    <input type="text" class="form-control mb-1" placeholder="Đáp án A" id="optionA-${id}">
                    <input type="text" class="form-control mb-1" placeholder="Đáp án B" id="optionB-${id}">
                    <input type="text" class="form-control mb-1" placeholder="Đáp án C" id="optionC-${id}">
                    <input type="text" class="form-control mb-1" placeholder="Đáp án D" id="optionD-${id}">
                </div>
            `;
        } else if (type === 'matching') {
            html = `
                <div class="sub-section mb-2">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <strong>Các cặp nối (Pairs)</strong>
                        <button type="button" class="btn btn-sm btn-success" onclick="addMatchingPair(${id})">+ Thêm cặp</button>
                    </div>
                    <div id="pairsContainer-${id}"></div>
                </div>
            `;
        }
        container.innerHTML = html;
    }

    function addMatchingPair(questionId) {
        const pairId = Date.now();
        const html = `
            <div class="pair-item" id="pair-${questionId}-${pairId}">
                <div class="row">
                    <div class="col-md-5 mb-2">
                        <input type="text" class="form-control" placeholder="Left (VD: always)" id="pairLeft-${questionId}-${pairId}">
                    </div>
                    <div class="col-md-5 mb-2">
                        <input type="text" class="form-control" placeholder="Right (VD: luôn luôn)" id="pairRight-${questionId}-${pairId}">
                    </div>
                    <div class="col-md-2 mb-2">
                        <button type="button" class="btn-remove btn-sm" onclick="removePair(${questionId}, ${pairId})">Xóa</button>
                    </div>
                </div>
            </div>
        `;
        document.getElementById(`pairsContainer-${questionId}`).insertAdjacentHTML('beforeend', html);
    }

    function removePair(questionId, pairId) {
        document.getElementById(`pair-${questionId}-${pairId}`).remove();
    }

    function removeQuestion(id) {
        document.getElementById(`question-${id}`).remove();
    }

    // ===== GENERATE JSON =====
    function generateJSON() {
        // Content JSON
        const contentSections = sections.map(section => {
            const title = document.getElementById(`sectionTitle-${section.id}`)?.value || '';
            
            if (section.type === 'theory') {
                const examples = [];
                const examplesContainer = document.getElementById(`examplesContainer-${section.id}`);
                if (examplesContainer) {
                    examplesContainer.querySelectorAll('.example-item').forEach(item => {
                        const id = item.id.split('-')[2];
                        const sentence = document.getElementById(`exampleSentence-${section.id}-${id}`)?.value;
                        const translation = document.getElementById(`exampleTranslation-${section.id}-${id}`)?.value;
                        const highlight = document.getElementById(`exampleHighlight-${section.id}-${id}`)?.value;
                        if (sentence) {
                            examples.push({
                                sentence,
                                translation: translation || '',
                                highlight: highlight || ''
                            });
                        }
                    });
                }
                
                return {
                    type: 'theory',
                    title,
                    content: document.getElementById(`theoryContent-${section.id}`)?.value || '',
                    image_url: document.getElementById(`theoryImage-${section.id}`)?.value || null,
                    examples: examples.length > 0 ? examples : undefined
                };
            } 
            else if (section.type === 'vocabulary') {
                const items = [];
                const container = document.getElementById(`vocabItems-${section.id}`);
                if (container) {
                    container.querySelectorAll('.vocab-item').forEach(item => {
                        const id = item.id.split('-')[2];
                        const word = document.getElementById(`vocabWord-${section.id}-${id}`)?.value;
                        if (word) {
                            items.push({
                                word,
                                pronunciation: document.getElementById(`vocabPronun-${section.id}-${id}`)?.value || '',
                                meaning: document.getElementById(`vocabMeaning-${section.id}-${id}`)?.value || '',
                                image_url: document.getElementById(`vocabImage-${section.id}-${id}`)?.value || null,
                                audio_url: document.getElementById(`vocabAudio-${section.id}-${id}`)?.value || null,
                                example: document.getElementById(`vocabExample-${section.id}-${id}`)?.value || '',
                                example_translation: document.getElementById(`vocabExampleTrans-${section.id}-${id}`)?.value || ''
                            });
                        }
                    });
                }
                return {type: 'vocabulary', title, items};
            }
            else if (section.type === 'practice') {
                const exercises = [];
                const container = document.getElementById(`exercisesContainer-${section.id}`);
                if (container) {
                    container.querySelectorAll('.exercise-item').forEach(item => {
                        const id = item.id.split('-')[2];
                        const sentence = document.getElementById(`exerciseSentence-${section.id}-${id}`)?.value;
                        if (sentence) {
                            const optionsStr = document.getElementById(`exerciseOptions-${section.id}-${id}`)?.value || '';
                            const options = optionsStr ? optionsStr.split('|').map(o => o.trim()).filter(o => o) : [];
                            
                            exercises.push({
                                instruction: document.getElementById(`exerciseInstruction-${section.id}-${id}`)?.value || '',
                                sentence,
                                answer: document.getElementById(`exerciseAnswer-${section.id}-${id}`)?.value || '',
                                options: options.length > 0 ? options : undefined
                            });
                        }
                    });
                }
                return {type: 'practice', title, exercises};
            }
            return {type: section.type, title};
        });

        // Quiz JSON
        const quizQuestions = [];
        document.querySelectorAll('.question-item').forEach((item, index) => {
            const id = item.id.split('-')[1];
            const type = document.getElementById(`questionType-${id}`)?.value;
            const question = document.getElementById(`questionText-${id}`)?.value;
            
            if (question) {
                const questionObj = {
                    id: index + 1,
                    type,
                    question,
                    image_url: document.getElementById(`questionImage-${id}`)?.value || null,
                    correct_answer: document.getElementById(`correctAnswer-${id}`)?.value || '',
                    explanation: document.getElementById(`explanation-${id}`)?.value || ''
                };

                if (type === 'multiple_choice') {
                    const options = [
                        document.getElementById(`optionA-${id}`)?.value || '',
                        document.getElementById(`optionB-${id}`)?.value || '',
                        document.getElementById(`optionC-${id}`)?.value || '',
                        document.getElementById(`optionD-${id}`)?.value || ''
                    ].filter(o => o);
                    questionObj.options = options;
                } else if (type === 'matching') {
                    const pairs = [];
                    const pairsContainer = document.getElementById(`pairsContainer-${id}`);
                    if (pairsContainer) {
                        pairsContainer.querySelectorAll('.pair-item').forEach(pairItem => {
                            const pairId = pairItem.id.split('-')[2];
                            const left = document.getElementById(`pairLeft-${id}-${pairId}`)?.value;
                            const right = document.getElementById(`pairRight-${id}-${pairId}`)?.value;
                            if (left && right) {
                                pairs.push({left, right});
                            }
                        });
                    }
                    questionObj.pairs = pairs;
                }

                quizQuestions.push(questionObj);
            }
        });

        const contentJSON = {sections: contentSections};
        const quizJSON = quizQuestions.length > 0 ? {
            title: document.getElementById('quizTitle')?.value || 'Quiz',
            description: document.getElementById('quizDescription')?.value || '',
            time_limit: parseInt(document.getElementById('quizTimeLimit')?.value) || 15,
            passing_score: parseInt(document.getElementById('quizPassingScore')?.value) || 80,
            questions: quizQuestions
        } : null;

        return {contentJSON, quizJSON};
    }

    // Update preview on tab change
    document.getElementById('preview-tab').addEventListener('click', () => {
        const {contentJSON, quizJSON} = generateJSON();
        document.getElementById('contentPreview').textContent = JSON.stringify(contentJSON, null, 2);
        document.getElementById('quizPreview').textContent = JSON.stringify(quizJSON, null, 2);
    });

    // Form submission
    document.getElementById('lessonForm').addEventListener('submit', (e) => {
        e.preventDefault();
        const {contentJSON, quizJSON} = generateJSON();
        
        document.getElementById('contentJson').value = JSON.stringify(contentJSON);
        document.getElementById('quizJson').value = quizJSON ? JSON.stringify(quizJSON) : '';
        
        // Submit form
        e.target.submit();
    });

    // Initialize with one section
    addSection();
</script>
@endsection