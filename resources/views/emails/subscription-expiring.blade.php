@component('mail::message')
# Your NEET LMS Subscription Expires Soon

Hi {{ $studentName }},

Your **{{ $planName }}** subscription to NEET LMS will expire in **{{ $daysRemaining }} day{{ $daysRemaining != 1 ? 's' : '' }}** ({{ $expiresAt }}).

## Expiration Notice

@component('mail::panel')
| Detail | Information |
|--------|-------------|
| **Plan** | {{ $planName ?? 'Active' }} |
| **Expires On** | {{ $expiresAt }} |
| **Auto-Renewal** | {{ $autoRenewalStatus }} |
| **Time Remaining** | {{ $daysRemaining }} day{{ $daysRemaining != 1 ? 's' : '' }} |
@endcomponent

## Don't Lose Your Progress!

Your subscription includes:

@foreach($planBenefits as $benefit)
- {{ $benefit }}
@endforeach

## Renew Your Subscription

@component('mail::button', ['url' => $renewUrl])
Renew Now
@endcomponent

## What Happens When My Subscription Expires?

❌ You won't be able to access practice questions
❌ Mock tests will be unavailable
❌ Performance analytics will be read-only
❌ New practice recommendations won't be generated

## How to Renew

### Option 1: Auto-Renewal
If auto-renewal is enabled, we'll automatically charge your saved payment method on the expiration date.

### Option 2: Manual Renewal
Visit your subscription page and manually renew your plan.

### Option 3: Upgrade
Want more features? Upgrade to a higher-tier plan at renewal!

---

## Special Offer!

@if(true)
Don't miss out! Renew today and keep your study momentum. Consistent learning leads to success!
@endif

@component('mail::button', ['url' => $renewUrl])
Renew Subscription
@endcomponent

---

## Manage Your Subscription

- **View Details:** [Subscription Settings]({{ $manageUrl }})
- **Update Payment Method:** Available in your account settings
- **Have Questions?** Contact us - we're here to help!

## Still Practicing Your Weak Areas?

Based on your recent performance, focus on:
- Reviewing fundamentals
- Taking more practice tests
- Using our personalized recommendations

Even a few more days of focused practice can make a huge difference!

---

Don't interrupt your NEET preparation journey. Renew your subscription now and keep progressing toward your goals!

Best regards,
**NEET LMS Team**

@slot('footer')
Subscription expires: {{ $expiresAt }} | Auto-renewal: {{ $autoRenewalStatus }}
@endslot
@endcomponent
