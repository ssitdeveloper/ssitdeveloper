# ============================================================================
# PAYMENT GATEWAYS CONFIGURATION
# ============================================================================
# Add the payment gateway credentials to your .env file

# Default payment gateway (stripe or paypal)
PAYMENT_GATEWAY=stripe

# Currency configuration
CURRENCY=INR

# ============================================================================
# STRIPE CONFIGURATION
# ============================================================================
# Get your keys from: https://dashboard.stripe.com/apikeys

STRIPE_SECRET_KEY=sk_test_xxxxxxxxxxxxx
STRIPE_PUBLISHABLE_KEY=pk_test_xxxxxxxxxxxxx
STRIPE_WEBHOOK_SECRET=whsec_xxxxxxxxxxxxx

# ============================================================================
# PAYPAL CONFIGURATION
# ============================================================================
# Get your credentials from: https://developer.paypal.com/dashboard

PAYPAL_MODE=sandbox  # Change to 'live' for production
PAYPAL_CLIENT_ID=xxxxxxxxxxxxx
PAYPAL_CLIENT_SECRET=xxxxxxxxxxxxx
PAYPAL_WEBHOOK_ID=xxxxxxxxxxxxx
