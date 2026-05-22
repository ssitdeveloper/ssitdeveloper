# Payment Gateway Quick Reference

## 🚀 Getting Started (3 Steps)

### Step 1: Get API Keys
- **Stripe**: Visit [stripe.com/register](https://stripe.com/register) → [keys](https://dashboard.stripe.com/apikeys)
- **PayPal**: Visit [PayPal Developer](https://developer.paypal.com/dashboard) → Create Sandbox account

### Step 2: Add to .env
```env
STRIPE_SECRET_KEY=sk_test_xxxxx
STRIPE_PUBLISHABLE_KEY=pk_test_xxxxx
PAYPAL_CLIENT_ID=xxxxx
PAYPAL_CLIENT_SECRET=xxxxx
```

### Step 3: Test Payment
1. Go to `/neet/student/subscription/upgrade`
2. Select plan and payment method
3. Complete payment
4. Verify subscription updated

---

## 💳 Test Cards

### Stripe
- **Success**: `4242 4242 4242 4242`
- **Decline**: `4000 0000 0000 0002`
- **CVC**: Any 3 digits
- **Expiry**: Any future date

### PayPal
- Use Sandbox Personal Account
- Available in PayPal Developer Dashboard

---

## 📁 Important Files

| File | Purpose |
|------|---------|
| `config/payment.php` | Gateway configuration |
| `routes/student.php` | Subscription routes |
| `app/Http/Controllers/Student/SubscriptionController.php` | Payment handling |
| `app/Services/SubscriptionService.php` | Business logic |
| `resources/views/student/subscription-upgrade.blade.php` | Gateway selection UI |

---

## 🔑 Environment Variables

```env
PAYMENT_GATEWAY=stripe              # Default gateway
CURRENCY=INR                        # Currency

# Stripe (from https://dashboard.stripe.com/apikeys)
STRIPE_SECRET_KEY=sk_test_...
STRIPE_PUBLISHABLE_KEY=pk_test_...
STRIPE_WEBHOOK_SECRET=whsec_...

# PayPal (from https://developer.paypal.com)
PAYPAL_MODE=sandbox                 # or 'live'
PAYPAL_CLIENT_ID=...
PAYPAL_CLIENT_SECRET=...
PAYPAL_WEBHOOK_ID=...
```

---

## 💰 Pricing

| Plan | Duration | Price |
|------|----------|-------|
| Monthly | 30 days | ₹499 |
| Quarterly | 90 days | ₹1,299 |
| Yearly | 365 days | ₹3,999 |
| Premium | 365 days | ₹9,999 |

---

## 🔄 Payment Flow

### Stripe
1. User selects plan & Stripe
2. Shown Stripe checkout form
3. Enters card details
4. Payment confirmed
5. Subscription activated

### PayPal
1. User selects plan & PayPal
2. Redirected to PayPal
3. Approves payment
4. Returns to site
5. Subscription activated

---

## ⚙️ Configuration Checklist

- [ ] Database migrated: `php artisan migrate`
- [ ] `.env` file created with API keys
- [ ] Config cached: `php artisan config:cache`
- [ ] Test student created: `student@test.com` / `password`
- [ ] Subscription routes working: `/neet/student/subscription`
- [ ] Upgrade page loads: `/neet/student/subscription/upgrade`

---

## 🧪 Quick Test

```bash
# Test Stripe configuration
php artisan tinker
>>> config('payment.stripe.secret_key')

# Test PayPal configuration
>>> config('payment.paypal.client_id')

# Check gateways registered
>>> app(App\Services\PaymentGateways\PaymentGatewayManager::class)->configuredGateways()
```

---

## 🐛 Troubleshooting

| Issue | Solution |
|-------|----------|
| "Gateway not configured" | Add API keys to .env and run `php artisan config:cache` |
| Stripe form not showing | Check browser console for errors, verify publishable key |
| PayPal redirect not working | Check sandbox mode matches your account, verify client_id |
| Payment not updating subscription | Check logs: `storage/logs/laravel.log` |
| 500 Error | Check `.env` keys are correct, no typos |

---

## 📞 Support Resources

- **Stripe**: [stripe.com/support](https://stripe.com/support)
- **PayPal**: [paypal.com/support](https://www.paypal.com/us/webapps/mpp/support)
- **Documentation**: See `PAYMENT_SETUP_GUIDE.md`
- **Testing**: See `PAYMENT_TESTING_CHECKLIST.md`
- **Full Guide**: See `PAYMENT_INTEGRATION_COMPLETE.md`

---

## 📚 Guides Available

1. **PAYMENT_SETUP_GUIDE.md** - Complete setup instructions with screenshots
2. **PAYMENT_INTEGRATION_COMPLETE.md** - Technical overview of all changes
3. **PAYMENT_TESTING_CHECKLIST.md** - Step-by-step testing guide
4. **PAYMENT_GATEWAY_SETUP.md** - Quick reference for .env

---

## ✅ Status

- ✅ Stripe integration complete
- ✅ PayPal integration complete
- ✅ Subscription service updated
- ✅ Routes configured
- ✅ UI components ready
- ✅ Documentation complete
- ⏳ Awaiting API key configuration

**Ready to test?** Follow the 3 steps above!

---

Last Updated: 2024
Version: 1.0
