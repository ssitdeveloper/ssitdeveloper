@extends('layouts.app')

@section('title', 'Features - NEET LMS')
@section('meta_description', 'Discover all the features that make NEET LMS the best platform for medical entrance exam preparation.')

@section('content')
<!-- Hero Section -->
<section class="hero">
    <div class="container">
        <h1>Powerful Features for Your Success</h1>
        <p>Everything you need to master your medical entrance exam</p>
    </div>
</section>

<!-- Core Features -->
<section class="section">
    <div class="container">
        <div class="section-header">
            <h2>Core Features</h2>
        </div>

        <div class="grid-3">
            <div class="card">
                <div class="card-body">
                    <h4 class="mb-2">3,000+ Questions</h4>
                    <p>Comprehensive question bank covering all medical exam topics with detailed explanations and difficulty levels.</p>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <h4 class="mb-2">Mock Tests</h4>
                    <p>Full-length mock tests with real exam conditions and instant result analysis.</p>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <h4 class="mb-2">Progress Analytics</h4>
                    <p>Detailed performance analytics, weak topic detection, and personalized recommendations.</p>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <h4 class="mb-2">Learning Mode</h4>
                    <p>Practice with answers visible to understand concepts and learn from mistakes.</p>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <h4 class="mb-2">Test Mode</h4>
                    <p>Timed tests without answers visible for authentic exam simulation.</p>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <h4 class="mb-2">Bookmarks & Notes</h4>
                    <p>Save important questions and add personal notes for quick revision.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Why Choose Us -->
<section class="section bg-gray-50">
    <div class="container">
        <div class="section-header">
            <h2>Why Choose NEET LMS?</h2>
        </div>

        <div class="grid-4">
            <div>
                <h4>Academic Excellence</h4>
                <ul class="feature-list">
                    <li>Curated by experienced medical educators</li>
                    <li>Questions aligned with latest exam patterns</li>
                    <li>Detailed explanations for each question</li>
                    <li>Updated regularly with new content</li>
                </ul>
            </div>
            <div>
                <h4>Smart Learning Tools</h4>
                <ul class="feature-list">
                    <li>Toggle between learning and test modes</li>
                    <li>Filter by subject, topic, and difficulty</li>
                    <li>Bookmark important questions</li>
                    <li>AI-powered weak topic detection</li>
                </ul>
            </div>
            <div>
                <h4>Performance Tracking</h4>
                <ul class="feature-list">
                    <li>Real-time progress tracking</li>
                    <li>Subject-wise performance analysis</li>
                    <li>Accuracy and timing metrics</li>
                    <li>Personalized study recommendations</li>
                </ul>
            </div>
            <div>
                <h4>Affordable & Accessible</h4>
                <ul class="feature-list">
                    <li>Flexible subscription plans</li>
                    <li>Access from any device</li>
                    <li>24/7 customer support</li>
                    <li>Lifetime access to purchased content</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- Call-to-Action -->
<section class="section">
    <div class="container text-center">
        <h2 class="mb-2">Ready to Start Your Journey?</h2>
        <p class="text-lg text-gray-600 mb-3">Join thousands of students who are already preparing for their medical entrance exams</p>
        <a href="{{ route('pricing') }}" class="btn btn-primary">View Pricing Plans</a>
    </div>
</section>
@endsection
