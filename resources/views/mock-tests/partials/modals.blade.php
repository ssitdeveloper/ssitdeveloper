<!-- Submit Test Confirmation Modal -->
<div class="modal fade" id="submitModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Submit Test</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to submit the test? This action cannot be undone.</p>
                <div class="alert alert-info">
                    <strong>Test Summary:</strong><br>
                    Total Questions: {{ count($attempt->question_ids) }}<br>
                    Time Remaining: <span id="modal-timer">--:--:--</span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" onclick="confirmSubmit()">Submit Test</button>
            </div>
        </div>
    </div>
</div>

<!-- Pause Test Modal -->
<div class="modal fade" id="pauseModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Pause Test</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>You can resume this test later from your dashboard. Your answers will be saved.</p>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    Make sure you have a stable internet connection when resuming.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Continue Test</button>
                <button type="button" class="btn btn-warning" onclick="confirmPause()">Pause Test</button>
            </div>
        </div>
    </div>
</div>

<!-- Time Warning Modal -->
<div class="modal fade" id="timeWarningModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title text-white">
                    <i class="fas fa-clock"></i> Time Warning
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>You have less than 5 minutes remaining!</p>
                <p>Please review your answers and submit the test soon.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-warning" data-dismiss="modal">Continue</button>
            </div>
        </div>
    </div>
</div>

<!-- Bookmark Modal -->
<div class="modal fade" id="bookmarkModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Bookmark Question</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Add notes for this question (optional):</p>
                <textarea class="form-control" id="bookmark-notes" rows="3" placeholder="Enter your notes here..."></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveBookmark()">Bookmark</button>
            </div>
        </div>
    </div>
</div>

<script>
// Update modal timer
function updateModalTimer() {
    const hours = Math.floor(timeRemaining / 3600);
    const minutes = Math.floor((timeRemaining % 3600) / 60);
    const seconds = timeRemaining % 60;
    const timeString = `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
    $('#modal-timer').text(timeString);
}

// Show time warning
function showTimeWarning() {
    if (timeRemaining <= 300 && timeRemaining > 0) {
        $('#timeWarningModal').modal('show');
    }
}

// Confirm submit
function confirmSubmit() {
    $('#submitModal').modal('hide');
    $('#submit-test-form').submit();
}

// Confirm pause
function confirmPause() {
    $('#pauseModal').modal('hide');
    window.location.href = '{{ route("student.dashboard") }}';
}

// Save bookmark
function saveBookmark() {
    const notes = $('#bookmark-notes').val();

    $.ajax({
        url: '{{ route("mock-tests.bookmark", $attempt->id) }}',
        method: 'POST',
        data: {
            question_id: currentQuestion.id,
            notes: notes,
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            $('#bookmarkModal').modal('hide');
            $('#bookmark-notes').val('');
            showToast('Question bookmarked successfully', 'success');
            updateBookmarkStatus(currentQuestion.id, true);
        },
        error: function() {
            showToast('Failed to bookmark question', 'error');
        }
    });
}

// Update submit function to show modal
function submitTest() {
    updateModalTimer();
    $('#submitModal').modal('show');
}

// Update pause function to show modal
function pauseTest() {
    $('#pauseModal').modal('show');
}

// Update bookmark function to show modal
function bookmarkQuestion() {
    $('#bookmarkModal').modal('show');
}

// Check for time warnings
setInterval(function() {
    showTimeWarning();
}, 60000); // Check every minute
</script>