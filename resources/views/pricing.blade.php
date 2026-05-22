@extends('layouts.app')

@section('title', 'Pricing - NEET LMS')
@section('meta_description', 'Simple, transparent pricing for NEET LMS. Choose the plan that fits your budget and goals.')

@section('content')
<!-- Hero Section -->
<section class="hero">
    <div class="container">
        <h1>Simple, Transparent Pricing</h1>
        <p>Choose the perfect plan for your medical entrance exam preparation</p>
    </div>
</section>

<!-- Pricing Cards -->
<section class="section">
    <div class="container">
        <div class="grid-3">
            <!-- Basic Plan -->
            <div class="card">
                <div class="card-header">
                    <h3>Basic</h3>
                    <p>Perfect for getting started</p>
                </div>
                <div class="card-body">
                    <div class="price-display">
                        <span class="currency">₹</span><span class="amount">499</span><span class="period">/month</span>
                    </div>
                    <a href="{{ route('register') }}" class="btn btn-outline btn-large" style="margin: var(--spacing-3) 0;">Get Started</a>
                    <ul class="feature-list">
                        <li>500+ Practice Questions</li>
                        <li>10 Full-Length Mock Tests</li>
                        <li>Basic Analytics</li>
                        <li>Learning Mode Only</li>
                        <li>Email Support</li>
                    </ul>
                </div>
            </div>

            <!-- Premium Plan (Featured) -->
            <div class="card" style="border: 2px solid var(--color-primary); position: relative;">
                <div style="position: absolute; top: -12px; left: 50%; transform: translateX(-50%); background: var(--color-primary); color: white; padding: 4px 12px; border-radius: 20px; font-size: var(--font-size-xs); font-weight: var(--font-weight-semibold);">MOST POPULAR</div>
                <div class="card-header">
                    <h3>Premium</h3>
                    <p>Best for serious preparation</p>
                </div>
                <div class="card-body">
                    <div class="price-display">
                        <span class="currency">₹</span><span class="amount">999</span><span class="period">/month</span>
                    </div>
                    <a href="{{ route('register') }}" class="btn btn-primary btn-large" style="margin: var(--spacing-3) 0;">Get Started Now</a>
                    <ul class="feature-list">
                        <li>2,000+ Practice Questions</li>
                        <li>50 Full-Length Mock Tests</li>
                        <li>Advanced Analytics</li>
                        <li>Learning + Test Mode</li>
                        <li>Personalized Recommendations</li>
                        <li>Bookmarks & Notes</li>
                        <li>Priority Support</li>
                    </ul>
                </div>
            </div>

            <!-- Elite Plan -->
            <div class="card">
                <div class="card-header">
                    <h3>Elite</h3>
                    <p>Complete mastery package</p>
                </div>
                <div class="card-body">
                    <div class="price-display">
                        <span class="currency">₹</span><span class="amount">1,999</span><span class="period">/month</span>
                    </div>
                    <a href="{{ route('register') }}" class="btn btn-outline btn-large" style="margin: var(--spacing-3) 0;">Get Started</a>
                    <ul class="feature-list">
                        <li>3,000+ Practice Questions</li>
                        <li>100+ Full-Length Mock Tests</li>
                        <li>Complete Analytics</li>
                        <li>All Modes Unlocked</li>
                        <li>AI-Powered Weak Topic Detection</li>
                        <li>Unlimited Bookmarks & Notes</li>
                        <li>Performance Reports</li>
                        <li>24/7 Premium Support</li>
                        <li>Exclusive Content</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FAQ Section -->
<section class="section bg-gray-50">
    <div class="container">
        <div class="section-header">
            <h2>Frequently Asked Questions</h2>
        </div>

        <div style="max-width: 800px; margin: 0 auto;">
            <div class="faq-item">
                <button class="faq-question">
                    Can I cancel my subscription anytime?
                    <span class="faq-toggle">+</span>
                </button>
                <div class="faq-answer">
                    Yes, you can cancel your subscription anytime without any penalties or hidden charges. Your access will remain active until the end of your billing period.
                </div>
            </div>

            <div class="faq-item">
                <button class="faq-question">
                    Is there a free trial available?
                    <span class="faq-toggle">+</span>
                </button>
                <div class="faq-answer">
                    Yes! We offer a 7-day free trial for all plans. You can access full features without any credit card requirement.
                </div>
            </div>

            <div class="faq-item">
                <button class="faq-question">
                    What payment methods do you accept?
                    <span class="faq-toggle">+</span>
                </button>
                <div class="faq-answer">
                    We accept all major payment methods including credit/debit cards, net banking, UPI, and digital wallets.
                </div>
            </div>

            <div class="faq-item">
                <button class="faq-question">
                    Can I upgrade or downgrade my plan?
                    <span class="faq-toggle">+</span>
                </button>
                <div class="faq-answer">
                    Absolutely! You can upgrade or downgrade your plan anytime. The price difference will be adjusted in your next billing cycle.
                </div>
            </div>

            <div class="faq-item">
                <button class="faq-question">
                    Do you offer bulk discounts for institutions?
                    <span class="faq-toggle">+</span>
                </button>
                <div class="faq-answer">
                    Yes, we offer special bulk pricing for institutions and coaching centers. Please contact our sales team for more details.
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Call-to-Action -->
<section class="section">
    <div class="container text-center">
        <h2 class="mb-2">Start Your Free Trial Now</h2>
        <p class="text-lg text-gray-600 mb-3">No credit card required. Get full access for 7 days.</p>
        <a href="{{ route('register') }}" class="btn btn-primary">Start Free Trial</a>
    </div>
</section>
@endsection

@section('extra_js')
<script>
document.querySelectorAll('.faq-question').forEach(button => {
    button.addEventListener('click', function() {
        const answer = this.nextElementSibling;
        const toggle = this.querySelector('.faq-toggle');
        
        answer.classList.toggle('active');
        toggle.textContent = answer.classList.contains('active') ? '−' : '+';
    });
});
</script>
@endsection
