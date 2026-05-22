@extends('layouts.app')

@section('title', 'Test Result - ' . $attempt->test->name)

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <!-- Result Header -->
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-0">{{ $attempt->test->name }} - Results</h3>
                            <small>Completed on {{ $attempt->completed_at->format('M d, Y \a\t h:i A') }}</small>
                        </div>
                        <div class="text-right">
                            <div class="score-display">
                                <h2 class="mb-0">{{ $result->percentage }}%</h2>
                                <small>{{ $result->obtained_marks }}/{{ $result->total_marks }}</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-3">
                            <div class="stat-card">
                                <h4 class="text-primary">{{ $result->correct_answers }}</h4>
                                <small class="text-muted">Correct</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-card">
                                <h4 class="text-danger">{{ $result->wrong_answers }}</h4>
                                <small class="text-muted">Wrong</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-card">
                                <h4 class="text-warning">{{ $result->unanswered_questions }}</h4>
                                <small class="text-muted">Unanswered</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-card">
                                <h4 class="text-info">{{ $result->rank }}</h4>
                                <small class="text-muted">Rank</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Performance Analysis -->
            <div class="row mb-4">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Subject-wise Performance</h5>
                        </div>
                        <div class="card-body">
                            @if(isset($result->subject_wise_analysis) && is_array($result->subject_wise_analysis))
                                @foreach($result->subject_wise_analysis as $subject => $stats)
                                <div class="subject-performance mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="font-weight-bold">{{ $subject }}</span>
                                        <span class="text-muted">{{ $stats['correct'] ?? 0 }}/{{ $stats['total'] ?? 0 }} correct</span>
                                    </div>
                                    <div class="progress">
                                        <div class="progress-bar bg-success"
                                             style="width: {{ $stats['total'] > 0 ? (($stats['correct'] ?? 0) / $stats['total']) * 100 : 0 }}%">
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Difficulty Analysis</h5>
                        </div>
                        <div class="card-body">
                            @if(isset($result->difficulty_wise_analysis) && is_array($result->difficulty_wise_analysis))
                                @foreach($result->difficulty_wise_analysis as $difficulty => $stats)
                                <div class="difficulty-stat mb-3">
                                    <div class="d-flex justify-content-between">
                                        <span class="text-capitalize">{{ $difficulty }}</span>
                                        <span class="badge badge-secondary">{{ $stats['correct'] ?? 0 }}/{{ $stats['total'] ?? 0 }}</span>
                                    </div>
                                </div>
                                @endforeach
                            @endif
                        </div>
                    </div>

                    <div class="card mt-3">
                        <div class="card-header">
                            <h5 class="mb-0">Recommendations</h5>
                        </div>
                        <div class="card-body">
                            @if(isset($result->recommendations) && is_array($result->recommendations))
                                @foreach($result->recommendations as $recommendation)
                                <div class="alert alert-info py-2 mb-2">
                                    <i class="fas fa-lightbulb"></i> {{ $recommendation }}
                                </div>
                                @endforeach
                            @else
                                <p class="text-muted mb-0">No specific recommendations available.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detailed Answers -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Detailed Answers</h5>
                    <div>
                        <a href="{{ route('mock-tests.review', $attempt->id) }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-eye"></i> Review Test
                        </a>
                        <a href="{{ route('mock-tests.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-list"></i> All Tests
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Q.No</th>
                                    <th>Subject</th>
                                    <th>Status</th>
                                    <th>Your Answer</th>
                                    <th>Correct Answer</th>
                                    <th>Marks</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($detailedAnswers as $answer)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $answer->question->chapter->topic->subject->name }}</td>
                                    <td>
                                        @if($answer->isCorrect())
                                            <span class="badge badge-success">Correct</span>
                                        @elseif($answer->selected_option_id)
                                            <span class="badge badge-danger">Wrong</span>
                                        @else
                                            <span class="badge badge-warning">Unanswered</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($answer->selected_option)
                                            {{ chr(65 + $answer->question->options->search(function($option) use ($answer) {
                                                return $option->id == $answer->selected_option_id;
                                            })) }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        {{ chr(65 + $answer->question->options->search(function($option) {
                                            return $option->is_correct;
                                        })) }}
                                    </td>
                                    <td>
                                        @if($answer->isCorrect())
                                            <span class="text-success">+{{ $attempt->test->marks_per_question }}</span>
                                        @elseif($answer->selected_option_id)
                                            <span class="text-danger">-{{ $attempt->test->negative_marking }}</span>
                                        @else
                                            <span class="text-muted">0</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

<style>
.stat-card {
    padding: 1rem;
    border-radius: 0.5rem;
    background: #f8f9fa;
}

.score-display h2 {
    color: #28a745;
    font-weight: bold;
}

.subject-performance {
    border-left: 4px solid #007bff;
    padding-left: 1rem;
}

.difficulty-stat {
    padding: 0.5rem 0;
    border-bottom: 1px solid #eee;
}

.difficulty-stat:last-child {
    border-bottom: none;
}

.table th {
    border-top: none;
    font-weight: 600;
}

@media (max-width: 768px) {
    .score-display h2 {
        font-size: 2rem;
    }

    .stat-card h4 {
        font-size: 1.5rem;
    }
}
</style>