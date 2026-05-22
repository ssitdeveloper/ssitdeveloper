@extends('layouts.app')

@section('title', 'Contact - NEET LMS')
@section('meta_description', 'Get in touch with NEET LMS. We\'re here to help with any questions or feedback.')

@section('content')
<!-- Hero Section -->
<section class="hero">
    <div class="container">
        <h1>Get in Touch</h1>
        <p>We'd love to hear from you. Send us a message and we'll respond as soon as possible.</p>
    </div>
</section>

<!-- Contact Section -->
<section class="section">
    <div class="container">
        <div class="grid-2">
            <!-- Contact Form -->
            <div>
                <h2 class="mb-3">Send us a Message</h2>
                <form method="POST" action="#" class="contact-form">
                    @csrf

                    @if ($errors->any())
                        <div class="alert alert-danger" role="alert">
                            Please fix the errors below
                        </div>
                    @endif

                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            Thank you! Your message has been sent successfully.
                        </div>
                    @endif

                    <div class="form-group">
                        <label for="name" class="form-label required">Full Name</label>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            class="form-input @error('name') is-invalid @enderror"
                            placeholder="Your name"
                            value="{{ old('name') }}"
                            required
                        >
                        @error('name')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="email" class="form-label required">Email Address</label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            class="form-input @error('email') is-invalid @enderror"
                            placeholder="your@email.com"
                            value="{{ old('email') }}"
                            required
                        >
                        @error('email')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="phone" class="form-label">Phone Number</label>
                        <input
                            type="tel"
                            id="phone"
                            name="phone"
                            class="form-input @error('phone') is-invalid @enderror"
                            placeholder="+91 9876543210"
                            value="{{ old('phone') }}"
                        >
                        @error('phone')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="subject" class="form-label required">Subject</label>
                        <select
                            id="subject"
                            name="subject"
                            class="form-input @error('subject') is-invalid @enderror"
                            required
                        >
                            <option value="">Select a subject</option>
                            <option value="general" @if(old('subject') === 'general') selected @endif>General Inquiry</option>
                            <option value="support" @if(old('subject') === 'support') selected @endif>Technical Support</option>
                            <option value="feedback" @if(old('subject') === 'feedback') selected @endif>Feedback</option>
                            <option value="partnership" @if(old('subject') === 'partnership') selected @endif>Partnership</option>
                            <option value="other" @if(old('subject') === 'other') selected @endif>Other</option>
                        </select>
                        @error('subject')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="message" class="form-label required">Message</label>
                        <textarea
                            id="message"
                            name="message"
                            class="form-input @error('message') is-invalid @enderror"
                            placeholder="Tell us how we can help..."
                            rows="5"
                            required
                        >{{ old('message') }}</textarea>
                        @error('message')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary btn-large">Send Message</button>
                </form>
            </div>

            <!-- Contact Info -->
            <div>
                <h2 class="mb-3">Contact Information</h2>

                <div class="contact-info-box">
                    <h4>Call Us</h4>
                    <p><a href="tel:+919876543210">+91 98765 43210</a></p>
                    <p style="font-size: var(--font-size-sm); color: var(--color-gray-600); margin-top: var(--spacing-1);">Monday - Friday, 9 AM - 6 PM IST</p>
                </div>

                <div class="contact-info-box">
                    <h4>Email Us</h4>
                    <p><a href="mailto:support@neetlms.com">support@neetlms.com</a></p>
                    <p style="font-size: var(--font-size-sm); color: var(--color-gray-600); margin-top: var(--spacing-1);">Response within 24 hours</p>
                </div>

                <div class="contact-info-box">
                    <h4>Visit Us</h4>
                    <p>NEET LMS HQ<br>123 Medical Center<br>New Delhi, India 110001</p>
                </div>

                <div class="contact-info-box">
                    <h4>Live Chat</h4>
                    <p>Chat with our support team in real-time for instant assistance.</p>
                    <button type="button" class="btn btn-outline btn-small" style="margin-top: var(--spacing-2);">Start Chat</button>
                </div>

                <!-- Social Links -->
                <div style="margin-top: var(--spacing-4);">
                    <h4 class="mb-2">Follow Us</h4>
                    <div style="display: flex; gap: var(--spacing-2);">
                        <a href="#" style="color: var(--color-primary); text-decoration: none; font-weight: var(--font-weight-semibold);">Twitter</a>
                        <a href="#" style="color: var(--color-primary); text-decoration: none; font-weight: var(--font-weight-semibold);">LinkedIn</a>
                        <a href="#" style="color: var(--color-primary); text-decoration: none; font-weight: var(--font-weight-semibold);">Facebook</a>
                        <a href="#" style="color: var(--color-primary); text-decoration: none; font-weight: var(--font-weight-semibold);">Instagram</a>
                    </div>
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
                    How quickly will I hear back?
                    <span class="faq-toggle">+</span>
                </button>
                <div class="faq-answer">
                    We aim to respond to all inquiries within 24 hours. For urgent support, please call us or use our live chat feature.
                </div>
            </div>

            <div class="faq-item">
                <button class="faq-question">
                    What are your support hours?
                    <span class="faq-toggle">+</span>
                </button>
                <div class="faq-answer">
                    Our support team is available Monday to Friday, 9 AM - 6 PM IST. For technical issues, you can reach us anytime through email.
                </div>
            </div>

            <div class="faq-item">
                <button class="faq-question">
                    How can I report a bug or issue?
                    <span class="faq-toggle">+</span>
                </button>
                <div class="faq-answer">
                    Please contact our technical support team at support@neetlms.com with detailed information about the issue you're facing.
                </div>
            </div>

            <div class="faq-item">
                <button class="faq-question">
                    Can I schedule a call with someone on the team?
                    <span class="faq-toggle">+</span>
                </button>
                <div class="faq-answer">
                    Yes! Please mention your preferred time in your message or email, and we'll schedule a call with the right person.
                </div>
            </div>
        </div>
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

// Form validation
const form = document.querySelector('.contact-form');
if (form) {
    form.addEventListener('submit', function(e) {
        const inputs = this.querySelectorAll('[required]');
        let isValid = true;

        inputs.forEach(input => {
            if (!input.value.trim()) {
                input.classList.add('is-invalid');
                isValid = false;
            } else {
                input.classList.remove('is-invalid');
            }
        });

        if (!isValid) {
            e.preventDefault();
        }
    });
}
</script>
@endsection
