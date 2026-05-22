# Payment Gateway Testing Checklist

## Pre-Test Setup

- [ ] Added `STRIPE_SECRET_KEY` to .env
- [ ] Added `STRIPE_PUBLISHABLE_KEY` to .env
- [ ] Added `PAYPAL_MODE=sandbox` to .env
- [ ] Added `PAYPAL_CLIENT_ID` to .env
- [ ] Added `PAYPAL_CLIENT_SECRET` to .env
- [ ] Run `php artisan config:cache` to reload configuration
- [ ] Database migrations complete with `payments` table
- [ ] Test student user exists (student@test.com)

## Test Stripe Payment Flow

### Setup
- [ ] Log in as student: `student@test.com` / `password`
- [ ] Navigate to: `/neet/student/subscription/upgrade`
- [ ] Can see "Monthly", "Quarterly", "Yearly", "Premium" plan cards
- [ ] Each plan shows "Stripe" and "PayPal" payment options

### Basic Flow
- [ ] Select "Monthly" plan
- [ ] Select "Stripe" radio button
- [ ] Click "Proceed to Payment" button
- [ ] See Stripe checkout form appear
- [ ] Form shows amount: ₹499
- [ ] Stripe.js card element is visible

### Payment Processing
- [ ] Enter test card: `4242 4242 4242 4242`
- [ ] Enter any future date (e.g., 12/25)
- [ ] Enter any 3-digit CVC (e.g., 123)
- [ ] Enter any name
- [ ] Click pay button
- [ ] See loading indicator
- [ ] Get redirected to `/subscription` with success message

### Subscription Updated
- [ ] On subscription page, see plan changed to "Monthly"
- [ ] See subscription status: "active"
- [ ] See expires_at date set 30 days in future
- [ ] Payment record created in payments table
- [ ] Payment status marked as "completed"

## Test PayPal Payment Flow

### Setup
- [ ] Have PayPal sandbox personal account ready
- [ ] Navigate to: `/neet/student/subscription/upgrade`
- [ ] Make sure session is cleared between tests

### Payment Flow
- [ ] Select "Quarterly" plan
- [ ] Select "PayPal" radio button
- [ ] Click "Proceed to Payment"
- [ ] Redirected to PayPal sandbox login page
- [ ] Log in with sandbox personal account
- [ ] See order details
- [ ] Click "Approve" or equivalent
- [ ] Redirected back to `/subscription` with success message

### Subscription Updated
- [ ] Plan shows "Quarterly"
- [ ] Expires at date is 90 days in future
- [ ] Payment record shows "paypal" gateway

## Test Payment Failure Scenarios

### Stripe Failure Card
- [ ] Use card: `4000 0000 0000 0002`
- [ ] Should show error message
- [ ] Should NOT update subscription
- [ ] Should NOT create payment record (or mark as failed)

### PayPal Cancellation
- [ ] During PayPal approval, click "Cancel"
- [ ] Should return to upgrade page
- [ ] Should NOT update subscription

## Test Subscription Cancellation

### Cancel Active Subscription
- [ ] Go to `/subscription` while subscribed
- [ ] Click "Cancel Subscription"
- [ ] Confirm cancellation
- [ ] Subscription status changes to "cancelled"
- [ ] Expiry date is set to today
- [ ] See success message

## Test Error Cases

### Missing API Keys
- [ ] Remove `STRIPE_SECRET_KEY` from .env
- [ ] Try to upgrade
- [ ] Should show "Payment gateway not configured" error
- [ ] Not crash with 500 error

### Invalid Plan
- [ ] Manually POST to upgrade with `plan=invalid`
- [ ] Should show validation error
- [ ] No payment record created

### Unauthorized Access
- [ ] Try to access callback without proper payment_id
- [ ] Should show "Unauthorized" or "Not found"
- [ ] Should not process payment

## Test Multiple Gateways

### Both Available
- [ ] Set both STRIPE and PAYPAL keys in .env
- [ ] Go to upgrade page
- [ ] See both Stripe and PayPal options
- [ ] Can select either one

### Only One Available
- [ ] Remove PAYPAL keys from .env
- [ ] Refresh page
- [ ] Only see Stripe option
- [ ] Can still upgrade with Stripe

## Database Verification

### Payments Table
```sql
SELECT * FROM payments WHERE user_id = 1;
```
Should show:
- [ ] Amount: 499 (for monthly)
- [ ] Currency: INR
- [ ] Status: completed (after successful payment)
- [ ] Gateway: stripe or paypal
- [ ] Metadata: Contains plan information

### Subscriptions Table
```sql
SELECT * FROM subscriptions WHERE user_id = 1;
```
Should show:
- [ ] Plan: monthly, quarterly, yearly, or premium
- [ ] Status: active (if current), cancelled (if cancelled)
- [ ] Started_at: Set when activated
- [ ] Expires_at: Calculated based on plan duration

## Webhook Testing (Optional)

### Stripe Webhook
- [ ] Go to Stripe dashboard → Webhooks
- [ ] See webhook endpoint: `/webhooks/stripe`
- [ ] Check for recent events: `payment_intent.succeeded`
- [ ] Verify signing secret matches `STRIPE_WEBHOOK_SECRET`

### PayPal Webhook
- [ ] Go to PayPal developer dashboard
- [ ] See webhook: `/webhooks/paypal`
- [ ] Check for recent events: `PAYMENT.CAPTURE.COMPLETED`
- [ ] Verify webhook ID matches `PAYPAL_WEBHOOK_ID`

## Performance Tests

### Load Time
- [ ] Upgrade page loads in < 2 seconds
- [ ] Stripe checkout form loads in < 1 second
- [ ] Payment processing completes in < 3 seconds

### Concurrent Payments
- [ ] Multiple users can upgrade simultaneously
- [ ] No database locking issues
- [ ] All payments process correctly

## Edge Cases

### Double Payment Prevention
- [ ] User tries to submit payment form twice quickly
- [ ] Only one payment processed
- [ ] Subscription updated only once

### Plan Downgrade
- [ ] Upgrade from Monthly to Yearly
- [ ] Then downgrade back to Monthly
- [ ] Should handle correctly
- [ ] Should pro-rate or refund difference (if applicable)

### Subscription Renewal
- [ ] If auto_renew enabled, test renewal flow
- [ ] Should charge on expiration date
- [ ] Should extend subscription

## Logging & Debugging

### Check Error Logs
```bash
tail -f storage/logs/laravel.log
```
- [ ] No payment-related errors
- [ ] No gateway connection errors
- [ ] No database errors

### Check Payment Details
```bash
php artisan tinker
```
```php
Payment::latest()->first();
```
- [ ] Verify all fields populated correctly
- [ ] Check metadata contains plan info
- [ ] Verify user_id is correct

## Final Sign-Off

- [ ] All tests passed for Stripe
- [ ] All tests passed for PayPal
- [ ] Error handling working correctly
- [ ] Database records accurate
- [ ] No console errors in browser
- [ ] No server errors in logs
- [ ] Ready for production deployment

---

**Testing Date**: _____________

**Tester Name**: _____________

**Status**: ✅ All Tests Passed / ⚠️ Some Issues Found / ❌ Critical Issues

**Notes**:
```
[Add any notes here]
```
