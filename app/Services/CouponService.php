<?php

namespace App\Services;

use App\Models\Coupon;

class CouponService
{
    public function validateCoupon(string $code): ?Coupon
    {
        $coupon = Coupon::where('code', strtoupper($code))->first();

        if (!$coupon || !$coupon->isValid()) {
            return null;
        }

        return $coupon;
    }

    public function calculateDiscount(Coupon $coupon, float $amount): float
    {
        return $coupon->getDiscountAmount($amount);
    }

    public function applyCoupon(Coupon $coupon, float $amount): array
    {
        $discount = $this->calculateDiscount($coupon, $amount);
        $coupon->use();

        return [
            'discount' => $discount,
            'total' => $amount - $discount,
        ];
    }
}
