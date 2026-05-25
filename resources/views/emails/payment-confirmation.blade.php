@component('mail::message')
# Payment Confirmation

Hi {{ $studentName }},

Thank you for your payment! Your subscription to NEET LMS has been confirmed.

## Payment Details

@component('mail::panel')
| Item | Details |
|------|---------|
| **Order ID** | {{ $payment->id }} |
| **Transaction ID** | {{ $transactionId ?? 'N/A' }} |
| **Amount** | ${{ $amount }} |
| **Currency** | {{ $currency }} |
| **Date** | {{ $paymentDate }} |
| **Status** | ✅ Completed |
@endcomponent

@if($plan)
## Subscription Details

@component('mail::panel')
| Item | Details |
|------|---------|
| **Plan** | {{ $plan->name }} |
| **Duration** | {{ $plan->duration_months }} month{{ $plan->duration_months > 1 ? 's' : '' }} |
| **Expires On** | {{ $planExpiresAt }} |
| **Auto-Renewal** | {{ $subscription && $subscription->auto_renew ? '✅ Enabled' : '❌ Disabled' }} |
@endcomponent
@endif

## What You Get

✅ Full access to question bank ({{ $plan->name === 'PRO' ? '10,000+' : ($plan->name === 'PREMIUM' ? '5,000+' : '1,000+') }} questions)
✅ Unlimited practice tests
✅ Detailed performance analytics
✅ Weekly performance insights
✅ Priority email support

@if($subscriptionUrl)
@component('mail::button', ['url' => $subscriptionUrl])
Manage Your Subscription
@endcomponent
@endif

## Start Learning Now

You can immediately access:

- **Question Bank** - Practice questions across all subjects
- **Mock Tests** - Full-length tests to evaluate your preparation
- **Performance Dashboard** - Track your progress in real-time
- **Recommendations** - Personalized study suggestions

**[Begin Your Learning Journey]({{ config('app.url') }}/student/dashboard)**

## FAQ

**Q: When does my subscription expire?**
A: {{ $planExpiresAt ?? 'N/A' }}

**Q: How do I manage my subscription?**
A: Visit your payment history or subscription settings from your dashboard.

**Q: What if I have billing questions?**
A: Contact our support team - we're here to help!

---

## Need Help?

If you have any questions about your subscription or payment:
- Visit: [Support Center]({{ config('app.url') }}/help)
- Email: support@neetlms.com
- Chat: Available in your dashboard (9 AM - 6 PM IST)

---

Happy learning! Your success is our mission.

Best regards,
**NEET LMS Team**

@slot('footer')
Invoice ID: {{ $payment->id }} | Payment Method: {{ $payment->payment_method ?? 'Card' }}
@endslot
@endcomponent
