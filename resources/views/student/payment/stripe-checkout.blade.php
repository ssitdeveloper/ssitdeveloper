@extends('layouts.student')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-md mx-auto bg-white rounded-lg shadow-lg p-8">
        <h1 class="text-2xl font-bold mb-6">Complete Your Payment</h1>

        <div class="mb-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
            <p class="text-sm text-gray-600 mb-2">Amount to Pay:</p>
            <p class="text-3xl font-bold text-blue-600">
                {{ number_format($amount, 2) }} {{ $currency }}
            </p>
        </div>

        <div id="card-element" class="border border-gray-300 rounded p-3 mb-6"></div>
        <div id="card-errors" role="alert" class="text-red-600 text-sm mb-4"></div>

        <button
            id="submit-btn"
            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition"
            type="button"
        >
            <span id="button-text">Pay {{ number_format($amount, 2) }} {{ $currency }}</span>
        </button>

        <p class="text-xs text-gray-500 text-center mt-4">
            Secure payment powered by Stripe
        </p>
    </div>
</div>

<script src="https://js.stripe.com/v3/"></script>
<script>
document.addEventListener('DOMContentLoaded', async function() {
    const stripe = Stripe('{{ config('payment.stripe.publishable_key') }}');
    const elements = stripe.elements();
    const cardElement = elements.create('card');
    cardElement.mount('#card-element');

    const cardErrors = document.getElementById('card-errors');
    cardElement.addEventListener('change', function(event) {
        if (event.error) {
            cardErrors.textContent = event.error.message;
        } else {
            cardErrors.textContent = '';
        }
    });

    const form = document.querySelector('form') || {};
    const submitBtn = document.getElementById('submit-btn');
    const clientSecret = '{{ $clientSecret }}';
    const paymentId = '{{ $paymentId }}';

    submitBtn.addEventListener('click', async function(e) {
        e.preventDefault();
        submitBtn.disabled = true;

        const { setupIntent, error } = await stripe.confirmCardPayment(clientSecret, {
            payment_method: {
                card: cardElement,
            }
        });

        if (error) {
            cardErrors.textContent = error.message;
            submitBtn.disabled = false;
        } else if (setupIntent && setupIntent.status === 'succeeded') {
            // Redirect to success page
            window.location.href = `{{ route('student.subscription.stripe-callback') }}?payment_id=${paymentId}`;
        }
    });
});
</script>
@endsection
