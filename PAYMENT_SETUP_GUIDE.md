# Payment Gateway Setup Guide

This guide explains how to set up Stripe and PayPal payment gateways for your NEET LMS subscription system.

## Quick Start

Add these payment gateway credentials to your `.env` file to enable payments.

## Stripe Setup

### Get Stripe API Keys

1. **Create a Stripe Account** (if you don't have one)
   - Visit [https://dashboard.stripe.com/register](https://dashboard.stripe.com/register)
   - Sign up and verify your email

2. **Get Your API Keys**
   - Go to [https://dashboard.stripe.com/apikeys](https://dashboard.stripe.com/apikeys)
   - You'll see two keys:
     - **Publishable Key** (starts with `pk_test_` or `pk_live_`)
     - **Secret Key** (starts with `sk_test_` or `sk_live_`)

3. **Get Webhook Secret**
   - Go to [https://dashboard.stripe.com/webhooks](https://dashboard.stripe.com/webhooks)
   - Add a new endpoint:
     - URL: `https://yourdomain.com/webhooks/stripe`
     - Events: Select `payment_intent.succeeded`, `payment_intent.payment_failed`, `charge.refunded`
   - Copy the webhook signing secret

4. **Add to .env**
   ```
   STRIPE_SECRET_KEY=sk_test_xxxxxxxxxxxxx
   STRIPE_PUBLISHABLE_KEY=pk_test_xxxxxxxxxxxxx
   STRIPE_WEBHOOK_SECRET=whsec_xxxxxxxxxxxxx
   ```

### Testing Stripe Payments

Use these test card numbers:
- **Success**: 4242 4242 4242 4242
- **Failure**: 4000 0000 0000 0002
- **Decline**: 5555 5555 5555 4444

Expiry: Any future date (MM/YY)
CVV: Any 3-digit number

## PayPal Setup

### Get PayPal Credentials

1. **Create a PayPal Business Account** (if you don't have one)
   - Visit [https://www.paypal.com/signin](https://www.paypal.com/signin)
   - Sign up for a business account

2. **Get Your API Credentials**
   - Go to [https://developer.paypal.com/dashboard/applications](https://developer.paypal.com/dashboard/applications)
   - Select the Sandbox environment first
   - You'll see your **Client ID** and **Client Secret**

3. **Create a Webhook**
   - Go to [https://developer.paypal.com/dashboard/webhooks](https://developer.paypal.com/dashboard/webhooks)
   - Create a new webhook:
     - Webhook URL: `https://yourdomain.com/webhooks/paypal`
     - Select events:
       - `PAYMENT.CAPTURE.COMPLETED`
       - `PAYMENT.CAPTURE.DENIED`
       - `PAYMENT.CAPTURE.REFUNDED`
   - Copy the **Webhook ID**

4. **Add to .env**
   ```
   PAYPAL_MODE=sandbox
   PAYPAL_CLIENT_ID=xxxxxxxxxxxxx
   PAYPAL_CLIENT_SECRET=xxxxxxxxxxxxx
   PAYPAL_WEBHOOK_ID=xxxxxxxxxxxxx
   ```

### Testing PayPal Payments

1. Use sandbox accounts:
   - **Business Account**: Access from [https://developer.paypal.com/dashboard](https://developer.paypal.com/dashboard)
   - **Personal Account**: Created automatically in sandbox

2. When paying, you can use the test personal account to complete payments

## Complete .env Configuration

```env
# Payment Gateway Configuration
PAYMENT_GATEWAY=stripe
CURRENCY=INR

# Stripe
STRIPE_SECRET_KEY=sk_test_xxxxxxxxxxxxx
STRIPE_PUBLISHABLE_KEY=pk_test_xxxxxxxxxxxxx
STRIPE_WEBHOOK_SECRET=whsec_xxxxxxxxxxxxx

# PayPal
PAYPAL_MODE=sandbox
PAYPAL_CLIENT_ID=xxxxxxxxxxxxx
PAYPAL_CLIENT_SECRET=xxxxxxxxxxxxx
PAYPAL_WEBHOOK_ID=xxxxxxxxxxxxx
```

## Production Setup

When going live:

### Stripe Production
1. Switch from `sk_test_*` to `sk_live_*` keys
2. Switch from `pk_test_*` to `pk_live_*` keys
3. Update webhook signing secret
4. Set webhook URL to your production domain

### PayPal Production
1. Change `PAYPAL_MODE=sandbox` to `PAYPAL_MODE=live`
2. Get production credentials from [https://developer.paypal.com/dashboard/applications](https://developer.paypal.com/dashboard/applications)
3. Update webhook URL to production domain
4. Get production webhook ID

## Payment Flow

### Stripe Flow
1. User selects plan and clicks "Proceed to Payment"
2. Redirects to Stripe checkout page
3. User enters card details
4. Stripe processes payment and returns client secret
5. Payment confirmed and subscription activated

### PayPal Flow
1. User selects plan and clicks "Proceed to Payment"
2. Redirects to PayPal approval page
3. User approves payment
4. Returns to your site with order ID
5. Payment captured and subscription activated

## Troubleshooting

### "Payment gateway not configured"
- Ensure API keys are added to `.env`
- Run `php artisan config:cache` to refresh configuration

### Webhook not working
- Check webhook URL is accessible from internet
- Verify webhook signing secret is correct
- Check logs for errors: `storage/logs/laravel.log`

### Payment processing fails
- For Stripe: Check dashboard logs at [https://dashboard.stripe.com/logs](https://dashboard.stripe.com/logs)
- For PayPal: Check activity log in PayPal dashboard
- Verify correct API mode (test vs production)

## API Documentation

### Available Gateways

The system supports:
- `stripe` - Stripe payment processor
- `paypal` - PayPal payment processor

### Gateway Methods

Each gateway implements these methods:
- `initiatePayment(data)` - Start payment process
- `capturePayment(id)` - Confirm/capture payment
- `refund(id, amount)` - Issue refund
- `getPaymentStatus(id)` - Check payment status

## Support

For issues:
- Stripe Support: [https://support.stripe.com](https://support.stripe.com)
- PayPal Support: [https://www.paypal.com/us/webapps/mpp/support](https://www.paypal.com/us/webapps/mpp/support)

---

Last Updated: 2024
