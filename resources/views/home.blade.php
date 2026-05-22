@extends('layouts.app')

@section('title', 'Home - NEET LMS | Medical Entrance Exam Preparation')
@section('meta_description', 'NEET LMS - Master your medical entrance exam with 3,000+ questions, mock tests, and expert guidance. Join 50,000+ students preparing for success.')

@section('content')
<!-- Hero Section -->
<section class="hero">
    <div class="container">
        <h1 class="animate-slideInLeft">Crack NEET with Confidence</h1>
        <p class="animate-slideInRight">Master your medical entrance exam with 3,000+ questions, 100+ full-length mock tests, and expert guidance from top educators.</p>
        <div class="hero-buttons">
            <a href="{{ route('register') }}" class="btn btn-primary">Start Free Trial</a>
            <a href="{{ route('pricing') }}" class="btn btn-outline">View Plans</a>
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="section">
    <div class="container">
        <div class="grid-4">
            <div class="text-center">
                <h2 class="text-primary">50,000+</h2>
                <p>Active Students</p>
            </div>
            <div class="text-center">
                <h2 class="text-primary">3,000+</h2>
                <p>Quality Questions</p>
            </div>
            <div class="text-center">
                <h2 class="text-primary">95%</h2>
                <p>Success Rate</p>
            </div>
            <div class="text-center">
                <h2 class="text-primary">4.8★</h2>
                <p>User Rating</p>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="section bg-gray-50">
    <div class="container">
        <div class="section-header">
            <h2>Why Choose NEET LMS?</h2>
            <p>Everything you need to succeed in your medical entrance exam</p>
        </div>

        <div class="grid-3">
            <div class="card">
                <div class="card-body">
                    <h4 class="mb-2">Comprehensive Question Bank</h4>
                    <p>3,000+ carefully curated questions covering all medical entrance exam topics with detailed explanations.</p>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <h4 class="mb-2">Full-Length Mock Tests</h4>
                    <p>100+ mock tests designed exactly like real exams to help you practice under timed conditions.</p>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <h4 class="mb-2">Smart Analytics</h4>
                    <p>Advanced performance tracking and AI-powered weak topic detection for personalized learning.</p>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <h4 class="mb-2">Learning Mode</h4>
                    <p>Practice with answers visible to understand concepts and learn from your mistakes.</p>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <h4 class="mb-2">Test Mode</h4>
                    <p>Simulate real exam conditions with timed tests and hidden answers for authentic practice.</p>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <h4 class="mb-2">Bookmarks & Notes</h4>
                    <p>Save important questions and create personal notes for quick revision.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Call-to-Action Section -->
<section class="section">
    <div class="container text-center">
        <h2 class="mb-2">Ready to Ace Your NEET?</h2>
        <p class="text-lg text-gray-600 mb-3">Join thousands of medical students who are already preparing for success</p>
        <a href="{{ route('register') }}" class="btn btn-primary btn-large" style="max-width: 300px; margin: 0 auto; display: block;">Get Started Today</a>
    </div>
</section>
@endsection
