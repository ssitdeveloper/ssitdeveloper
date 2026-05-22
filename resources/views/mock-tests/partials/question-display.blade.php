@if(isset($question))
<div class="question-header mb-3">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <span class="badge badge-primary">{{ $question->chapter->topic->subject->name }}</span>
            <span class="badge badge-secondary">{{ $question->difficulty_level }}</span>
        </div>
        <div>
            <span class="text-muted">Question {{ $questionIndex }} of {{ $totalQuestions }}</span>
        </div>
    </div>
</div>

<div class="question-text mb-4">
    <div class="card">
        <div class="card-body">
            {!! $question->question_text !!}
        </div>
    </div>
</div>

@if($question->image_path)
<div class="question-image mb-4 text-center">
    <img src="{{ asset('storage/' . $question->image_path) }}" alt="Question Image" class="img-fluid rounded">
</div>
@endif

<div class="options mb-4">
    <div class="row">
        @foreach($question->options as $index => $option)
        <div class="col-md-6 mb-3">
            <div class="option-card card h-100 {{ $userAnswer && $userAnswer->selected_option_id == $option->id ? 'border-primary' : '' }}">
                <div class="card-body d-flex align-items-center">
                    <div class="form-check">
                        <input class="form-check-input option-radio" type="radio"
                               name="option" value="{{ $option->id }}"
                               id="option{{ $index + 1 }}"
                               {{ $userAnswer && $userAnswer->selected_option_id == $option->id ? 'checked' : '' }}
                               onchange="saveAnswer({{ $option->id }})">
                        <label class="form-check-label w-100" for="option{{ $index + 1 }}">
                            <span class="option-label">{{ chr(65 + $index) }}.</span>
                            <span class="option-text">{!! $option->option_text !!}</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

<div class="question-actions">
    <div class="form-check">
        <input class="form-check-input" type="checkbox" id="mark-review-checkbox"
               {{ $userAnswer && $userAnswer->is_marked_for_review ? 'checked' : '' }}>
        <label class="form-check-label" for="mark-review-checkbox">
            Mark for review
        </label>
    </div>
</div>

<div class="navigation-buttons mt-4 d-flex justify-content-between">
    <button class="btn btn-outline-primary" onclick="navigateToPrevious()" id="prev-btn">
        <i class="fas fa-chevron-left"></i> Previous
    </button>

    <div class="text-center">
        <small class="text-muted">Use number keys (1-4) to select options, arrow keys to navigate</small>
    </div>

    <button class="btn btn-outline-primary" onclick="navigateToNext()" id="next-btn">
        Next <i class="fas fa-chevron-right"></i>
    </button>
</div>
@endif

<style>
.question-text {
    font-size: 1.1rem;
    line-height: 1.6;
}

.option-card {
    cursor: pointer;
    transition: all 0.2s ease;
}

.option-card:hover {
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.option-card.border-primary {
    background-color: #f8f9ff;
}

.option-label {
    font-weight: bold;
    color: #007bff;
    margin-right: 0.5rem;
}

.option-text {
    flex: 1;
}

.timer-display {
    font-size: 1.2rem;
    font-weight: bold;
    font-family: monospace;
}

.question-number {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin: 2px;
    cursor: pointer;
    font-weight: bold;
    transition: all 0.2s ease;
}

.question-number.not-visited {
    background-color: #e9ecef;
    color: #6c757d;
}

.question-number.not-answered {
    background-color: #ffc107;
    color: white;
}

.question-number.answered {
    background-color: #28a745;
    color: white;
}

.question-number.marked-review {
    background-color: #17a2b8;
    color: white;
}

.question-number.bookmarked {
    background-color: #dc3545;
    color: white;
}

.question-number.active {
    border: 3px solid #007bff;
    transform: scale(1.1);
}
</style>