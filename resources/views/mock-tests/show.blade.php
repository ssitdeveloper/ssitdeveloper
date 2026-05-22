@extends('layouts.app')

@section('title', $test->name . ' - Mock Test Details')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <!-- Test Header -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h2 class="mb-0">{{ $test->name }}</h2>
                        <div>
                            @if($test->isAvailable())
                                <form action="{{ route('mock-tests.start', $test->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-light">
                                        <i class="fas fa-play"></i> Start Test
                                    </button>
                                </form>
                            @else
                                <span class="badge badge-secondary">Not Available</span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <p class="lead">{{ $test->description }}</p>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="test-details">
                                <h5>Test Information</h5>
                                <table class="table table-borderless">
                                    <tr>
                                        <td><strong>Duration:</strong></td>
                                        <td>{{ $test->duration_minutes }} minutes</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Total Questions:</strong></td>
                                        <td>{{ $test->total_questions }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Marks per Question:</strong></td>
                                        <td>{{ $test->marks_per_question }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Negative Marking:</strong></td>
                                        <td>{{ $test->negative_marking > 0 ? '-' . $test->negative_marking : 'None' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Total Marks:</strong></td>
                                        <td>{{ $test->total_questions * $test->marks_per_question }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Available From:</strong></td>
                                        <td>{{ $test->available_from->format('M d, Y h:i A') }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Available Until:</strong></td>
                                        <td>{{ $test->available_until->format('M d, Y h:i A') }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <div class="col-md-6">
                            @if($test->subject_distribution)
                            <div class="subject-breakdown">
                                <h5>Subject-wise Distribution</h5>
                                <div class="chart-container mb-3">
                                    <canvas id="subjectChart" width="300" height="300"></canvas>
                                </div>
                                <div class="subject-list">
                                    @foreach($test->subject_distribution as $subject => $count)
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span>{{ $subject }}</span>
                                        <span class="badge badge-primary">{{ $count }} questions</span>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Previous Attempts -->
            @if($previousAttempts->count() > 0)
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Your Previous Attempts</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Attempt Date</th>
                                    <th>Status</th>
                                    <th>Score</th>
                                    <th>Percentage</th>
                                    <th>Rank</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($previousAttempts as $attempt)
                                <tr>
                                    <td>{{ $attempt->created_at->format('M d, Y h:i A') }}</td>
                                    <td>
                                        @if($attempt->status === 'completed')
                                            <span class="badge badge-success">Completed</span>
                                        @elseif($attempt->status === 'in_progress')
                                            <span class="badge badge-warning">In Progress</span>
                                        @else
                                            <span class="badge badge-secondary">{{ ucfirst($attempt->status) }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($attempt->result)
                                            {{ $attempt->result->obtained_marks }}/{{ $attempt->result->total_marks }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        @if($attempt->result)
                                            {{ $attempt->result->percentage }}%
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        @if($attempt->result)
                                            {{ $attempt->result->rank }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        @if($attempt->status === 'completed')
                                            <a href="{{ route('mock-tests.result', $attempt->id) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-eye"></i> View Result
                                            </a>
                                            <a href="{{ route('mock-tests.review', $attempt->id) }}" class="btn btn-sm btn-secondary">
                                                <i class="fas fa-search"></i> Review
                                            </a>
                                        @elseif($attempt->status === 'in_progress')
                                            <a href="{{ route('mock-tests.resume', $attempt->id) }}" class="btn btn-sm btn-success">
                                                <i class="fas fa-play"></i> Resume
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif

            <!-- Instructions -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Test Instructions</h5>
                </div>
                <div class="card-body">
                    <div class="instructions">
                        <div class="instruction-item">
                            <i class="fas fa-clock text-primary"></i>
                            <div>
                                <strong>Time Management:</strong> You have {{ $test->duration_minutes }} minutes to complete the test.
                                The timer will start as soon as you begin the test.
                            </div>
                        </div>

                        <div class="instruction-item">
                            <i class="fas fa-question-circle text-success"></i>
                            <div>
                                <strong>Questions:</strong> There are {{ $test->total_questions }} questions in total.
                                Each question carries {{ $test->marks_per_question }} mark(s).
                            </div>
                        </div>

                        <div class="instruction-item">
                            <i class="fas fa-check-circle text-info"></i>
                            <div>
                                <strong>Answering:</strong> Click on the option you think is correct.
                                You can change your answer anytime before submitting.
                            </div>
                        </div>

                        @if($test->negative_marking > 0)
                        <div class="instruction-item">
                            <i class="fas fa-exclamation-triangle text-warning"></i>
                            <div>
                                <strong>Negative Marking:</strong> There is negative marking of {{ $test->negative_marking }} mark(s)
                                for each wrong answer.
                            </div>
                        </div>
                        @endif

                        <div class="instruction-item">
                            <i class="fas fa-flag text-secondary"></i>
                            <div>
                                <strong>Mark for Review:</strong> You can mark questions for review and come back to them later.
                            </div>
                        </div>

                        <div class="instruction-item">
                            <i class="fas fa-save text-primary"></i>
                            <div>
                                <strong>Auto-save:</strong> Your answers are automatically saved as you progress through the test.
                            </div>
                        </div>

                        <div class="instruction-item">
                            <i class="fas fa-paper-plane text-danger"></i>
                            <div>
                                <strong>Submission:</strong> Make sure to submit the test before time runs out.
                                Once submitted, you cannot change your answers.
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="text-center mt-4">
                <a href="{{ route('mock-tests.index') }}" class="btn btn-secondary mr-2">
                    <i class="fas fa-arrow-left"></i> Back to Tests
                </a>
                @if($test->isAvailable())
                <form action="{{ route('mock-tests.start', $test->id) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-play"></i> Start Test Now
                    </button>
                </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
@if($test->subject_distribution)
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('subjectChart').getContext('2d');
    const subjectData = @json($test->subject_distribution);

    const labels = Object.keys(subjectData);
    const data = Object.values(subjectData);

    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: data,
                backgroundColor: [
                    '#007bff',
                    '#28a745',
                    '#ffc107',
                    '#dc3545',
                    '#6f42c1',
                    '#e83e8c',
                    '#fd7e14',
                    '#20c997'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            legend: {
                position: 'bottom',
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'bottom'
                }
            }
        }
    });
});
</script>
@endif
@endsection

<style>
.test-details h5 {
    color: #007bff;
    margin-bottom: 1rem;
}

.subject-breakdown h5 {
    color: #28a745;
    margin-bottom: 1rem;
}

.chart-container {
    height: 250px;
    position: relative;
}

.subject-list {
    max-height: 200px;
    overflow-y: auto;
}

.instructions {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.instruction-item {
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
    padding: 0.75rem;
    background: #f8f9fa;
    border-radius: 0.375rem;
    border-left: 4px solid #007bff;
}

.instruction-item i {
    margin-top: 0.125rem;
    flex-shrink: 0;
}

@media (max-width: 768px) {
    .instruction-item {
        flex-direction: column;
        text-align: center;
    }

    .chart-container {
        height: 200px;
    }
}
</style>