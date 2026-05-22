@extends('layouts.app')

@section('title', 'Mock Tests')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="mb-0">Mock Tests</h1>
                    <p class="text-muted">Practice with full-length mock tests</p>
                </div>
                <div>
                    <a href="{{ route('mock-tests.history') }}" class="btn btn-outline-primary">
                        <i class="fas fa-history"></i> Test History
                    </a>
                </div>
            </div>

            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
            </div>
            @endif

            @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                {{ session('error') }}
                <button type="button" class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
            </div>
            @endif

            @if($tests->count() > 0)
            <div class="row">
                @foreach($tests as $test)
                <div class="col-lg-6 col-xl-4 mb-4">
                    <div class="card h-100 test-card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">{{ $test->name }}</h5>
                            <small class="text-muted">{{ $test->description }}</small>
                        </div>
                        <div class="card-body">
                            <div class="test-info mb-3">
                                <div class="info-item">
                                    <i class="fas fa-clock text-primary"></i>
                                    <span>{{ $test->duration_minutes }} minutes</span>
                                </div>
                                <div class="info-item">
                                    <i class="fas fa-question-circle text-success"></i>
                                    <span>{{ $test->total_questions }} questions</span>
                                </div>
                                <div class="info-item">
                                    <i class="fas fa-trophy text-warning"></i>
                                    <span>{{ $test->marks_per_question }} marks each</span>
                                </div>
                                @if($test->negative_marking > 0)
                                <div class="info-item">
                                    <i class="fas fa-minus-circle text-danger"></i>
                                    <span>-{{ $test->negative_marking }} negative</span>
                                </div>
                                @endif
                            </div>

                            @if($test->subject_distribution)
                            <div class="subject-distribution mb-3">
                                <small class="text-muted d-block mb-2">Subject Distribution:</small>
                                @foreach($test->subject_distribution as $subject => $count)
                                <div class="subject-item">
                                    <span class="subject-name">{{ $subject }}</span>
                                    <span class="badge badge-secondary">{{ $count }}</span>
                                </div>
                                @endforeach
                            </div>
                            @endif

                            <div class="test-actions">
                                <a href="{{ route('mock-tests.show', $test->id) }}" class="btn btn-outline-primary btn-block">
                                    <i class="fas fa-eye"></i> View Details
                                </a>
                            </div>
                        </div>
                        <div class="card-footer text-muted">
                            <small>Available until {{ $test->available_until->format('M d, Y') }}</small>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="text-center py-5">
                <i class="fas fa-clipboard-list fa-4x text-muted mb-3"></i>
                <h3>No Tests Available</h3>
                <p class="text-muted">There are currently no mock tests available. Please check back later.</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

<style>
.test-card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    border: none;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.test-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.test-info {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.info-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.9rem;
}

.info-item i {
    width: 16px;
}

.subject-distribution {
    background: #f8f9fa;
    padding: 0.75rem;
    border-radius: 0.375rem;
}

.subject-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.25rem;
    font-size: 0.85rem;
}

.subject-item:last-child {
    margin-bottom: 0;
}

.subject-name {
    font-weight: 500;
}

.test-actions {
    margin-top: auto;
}

.card-footer {
    background: transparent;
    border-top: 1px solid rgba(0,0,0,0.125);
}

@media (max-width: 768px) {
    .test-card {
        margin-bottom: 1rem;
    }

    .info-item {
        font-size: 0.85rem;
    }
}
</style>