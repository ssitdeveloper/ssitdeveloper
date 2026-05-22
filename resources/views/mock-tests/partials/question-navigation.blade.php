<div class="question-numbers">
    @foreach($navigation as $item)
    <div class="question-number
         {{ !$item['answered'] && !$item['marked_for_review'] ? 'not-visited' : '' }}
         {{ $item['answered'] ? 'answered' : (!$item['marked_for_review'] ? 'not-answered' : '') }}
         {{ $item['marked_for_review'] ? 'marked-review' : '' }}
         {{ $item['bookmarked'] ? 'bookmarked' : '' }}
         {{ $loop->first ? 'active' : '' }}"
         data-question-id="{{ $item['question_id'] }}"
         onclick="loadQuestion({{ $item['question_id'] }})">
        {{ $item['index'] }}
    </div>
    @endforeach
</div>

<script>
$(document).ready(function() {
    // Update navigation buttons state
    updateNavigationButtons();
});

function updateNavigationButtons() {
    const currentIndex = currentAttempt.question_ids.indexOf(currentQuestion.id);
    const $prevBtn = $('#prev-btn');
    const $nextBtn = $('#next-btn');

    $prevBtn.prop('disabled', currentIndex === 0);
    $nextBtn.prop('disabled', currentIndex === currentAttempt.question_ids.length - 1);
}
</script>