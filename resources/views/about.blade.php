@extends('layouts.app')

@section('title', 'About - NEET LMS')
@section('meta_description', 'Learn about NEET LMS and our mission to help medical students succeed.')

@section('content')
<!-- Hero Section -->
<section class="hero">
    <div class="container">
        <h1>About NEET LMS</h1>
        <p>Empowering medical students to achieve their dreams</p>
    </div>
</section>

<!-- Our Story -->
<section class="section">
    <div class="container">
        <div class="grid-2">
            <div>
                <h2>Our Story</h2>
                <p class="mb-2">NEET LMS was founded by a group of experienced medical educators and software engineers who believed that quality education should be accessible to everyone.</p>
                <p class="mb-2">We started with a simple vision: to create a platform that combines the best of technology and personalized learning to help medical students succeed in their entrance exams.</p>
                <p>Today, we're proud to serve over 50,000 active students and help them achieve their dreams of becoming doctors.</p>
            </div>
            <div style="display: flex; align-items: center; justify-content: center;">
                <div style="font-size: 6rem; opacity: 0.8;">📚</div>
            </div>
        </div>
    </div>
</section>

<!-- Mission, Vision, Values -->
<section class="section bg-gray-50">
    <div class="container">
        <div class="grid-3">
            <div class="card">
                <div class="card-body">
                    <h3 style="color: var(--color-primary); margin-bottom: var(--spacing-2);">Our Mission</h3>
                    <p>To provide world-class medical entrance exam preparation that is affordable, accessible, and effective for all students.</p>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <h3 style="color: var(--color-primary); margin-bottom: var(--spacing-2);">Our Vision</h3>
                    <p>To transform medical education through innovative technology and make quality preparation available to every aspiring doctor.</p>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <h3 style="color: var(--color-primary); margin-bottom: var(--spacing-2);">Our Values</h3>
                    <p>Excellence, integrity, innovation, and student-centricity guide everything we do.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Impact Stats -->
<section class="section">
    <div class="container">
        <div class="section-header">
            <h2>Our Impact</h2>
        </div>

        <div class="grid-4">
            <div class="text-center">
                <h2 class="text-primary">50,000+</h2>
                <p>Active Students</p>
            </div>
            <div class="text-center">
                <h2 class="text-primary">95%</h2>
                <p>Success Rate</p>
            </div>
            <div class="text-center">
                <h2 class="text-primary">3,000+</h2>
                <p>Quality Questions</p>
            </div>
            <div class="text-center">
                <h2 class="text-primary">4.8★</h2>
                <p>User Rating</p>
            </div>
        </div>
    </div>
</section>

<!-- Why Love NEET -->
<section class="section bg-gray-50">
    <div class="container">
        <div class="section-header">
            <h2>Why Students Love Us</h2>
        </div>

        <div class="grid-3">
            <div class="card">
                <div class="card-body">
                    <h4 class="mb-2">Expert-Created Content</h4>
                    <p>All questions are created and reviewed by experienced medical educators and doctors to ensure accuracy and relevance.</p>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <h4 class="mb-2">Smart Learning Tools</h4>
                    <p>Advanced analytics, personalized recommendations, and AI-powered weak topic detection help you study smarter.</p>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <h4 class="mb-2">Affordable Pricing</h4>
                    <p>Quality education at prices that work for students. Flexible plans and regular discounts make preparation accessible to everyone.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Team Section -->
<section class="section">
    <div class="container">
        <div class="section-header">
            <h2>Meet Our Team</h2>
        </div>

        <div class="grid-4">
            <div class="card" style="text-align: center;">
                <div class="card-body">
                    <div style="font-size: 3rem; margin-bottom: var(--spacing-2);">👨‍⚕️</div>
                    <h4>Dr. Rajesh Kumar</h4>
                    <p style="color: var(--color-primary); font-weight: var(--font-weight-semibold); margin-bottom: var(--spacing-1);">Co-Founder & Medical Director</p>
                    <p style="font-size: var(--font-size-sm);">10+ years in medical education</p>
                </div>
            </div>
            <div class="card" style="text-align: center;">
                <div class="card-body">
                    <div style="font-size: 3rem; margin-bottom: var(--spacing-2);">👨‍💼</div>
                    <h4>Arjun Singh</h4>
                    <p style="color: var(--color-primary); font-weight: var(--font-weight-semibold); margin-bottom: var(--spacing-1);">Co-Founder & CEO</p>
                    <p style="font-size: var(--font-size-sm);">EdTech entrepreneur with 8 years experience</p>
                </div>
            </div>
            <div class="card" style="text-align: center;">
                <div class="card-body">
                    <div style="font-size: 3rem; margin-bottom: var(--spacing-2);">👩‍💻</div>
                    <h4>Priya Sharma</h4>
                    <p style="color: var(--color-primary); font-weight: var(--font-weight-semibold); margin-bottom: var(--spacing-1);">CTO</p>
                    <p style="font-size: var(--font-size-sm);">Full-stack developer passionate about education</p>
                </div>
            </div>
            <div class="card" style="text-align: center;">
                <div class="card-body">
                    <div style="font-size: 3rem; margin-bottom: var(--spacing-2);">👨‍🏫</div>
                    <h4>Dr. Vikram Patel</h4>
                    <p style="color: var(--color-primary); font-weight: var(--font-weight-semibold); margin-bottom: var(--spacing-1);">Content Director</p>
                    <p style="font-size: var(--font-size-sm);">Medical educator and content strategist</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Call-to-Action -->
<section class="section">
    <div class="container text-center">
        <h2 class="mb-2">Join Our Community</h2>
        <p class="text-lg text-gray-600 mb-3">Be part of the NEET LMS family and start your journey to success</p>
        <a href="{{ route('register') }}" class="btn btn-primary">Get Started Today</a>
    </div>
</section>
@endsection
