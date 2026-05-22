@extends('layouts.student')

@section('title', 'Subscription & Billing')

@section('content')
    <div class="student-card" style="margin-bottom: var(--spacing-4);">
        <h2 style="margin-top: 0; font-size: var(--font-size-xl); margin-bottom: var(--spacing-3);">Your Subscription</h2>
        <p style="color: var(--color-gray-600); margin: 0;">Manage your subscription and upgrade your plan.</p>
    </div>

    <!-- Current Plan -->
    <div class="student-card" style="background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-primary-light) 100%); color: white; margin-bottom: var(--spacing-4);">
        <h3 style="margin-top: 0; margin-bottom: var(--spacing-2);">Current Plan: Premium</h3>
        <p style="margin: 0 0 var(--spacing-3) 0; opacity: 0.9;">Active until May 25, 2026</p>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: var(--spacing-3); margin-bottom: var(--spacing-3);">
            <div>
                <p style="margin: 0 0 var(--spacing-1) 0; opacity: 0.8; font-size: var(--font-size-sm);">Monthly Charge</p>
                <strong style="font-size: 1.5rem;">₹999</strong>
            </div>
            <div>
                <p style="margin: 0 0 var(--spacing-1) 0; opacity: 0.8; font-size: var(--font-size-sm);">Renewal Date</p>
                <strong style="font-size: 1.5rem;">May 25</strong>
            </div>
            <div>
                <p style="margin: 0 0 var(--spacing-1) 0; opacity: 0.8; font-size: var(--font-size-sm);">Days Left</p>
                <strong style="font-size: 1.5rem;">5 days</strong>
            </div>
        </div>
        <div style="display: flex; gap: var(--spacing-2);">
            <button class="btn" style="background-color: white; color: var(--color-primary); padding: 8px 16px; border: none; border-radius: 6px; cursor: pointer; font-weight: bold;">Cancel Subscription</button>
            <button class="btn" style="background-color: rgba(255,255,255,0.2); color: white; padding: 8px 16px; border: none; border-radius: 6px; cursor: pointer; font-weight: bold;">View Invoice</button>
        </div>
    </div>

    <!-- Available Plans -->
    <div style="margin-bottom: var(--spacing-4);">
        <h3 style="margin-bottom: var(--spacing-3); font-size: var(--font-size-lg);">Upgrade Your Plan</h3>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: var(--spacing-3);">
            <!-- Basic Plan -->
            <div class="student-card" style="border: 2px solid var(--color-gray-300);">
                <h4 style="margin-top: 0; margin-bottom: var(--spacing-1);">Basic</h4>
                <p style="margin: 0 0 var(--spacing-3) 0; color: var(--color-gray-600);">For beginners</p>
                <div style="margin-bottom: var(--spacing-3);">
                    <span style="font-size: 2rem; font-weight: bold; color: var(--color-primary);">₹499</span>
                    <span style="color: var(--color-gray-600); margin-left: var(--spacing-1);">/month</span>
                </div>
                <ul style="list-style: none; padding: 0; margin: 0 0 var(--spacing-3) 0;">
                    <li style="padding: var(--spacing-1) 0; color: var(--color-gray-600);">• 5 practice tests/month</li>
                    <li style="padding: var(--spacing-1) 0; color: var(--color-gray-600);">• Access to courses</li>
                    <li style="padding: var(--spacing-1) 0; color: var(--color-gray-600);">• Basic analytics</li>
                </ul>
                <button class="btn btn-outline" style="width: 100%;">Downgrade</button>
            </div>

            <!-- Premium Plan (Current) -->
            <div class="student-card" style="border: 3px solid var(--color-primary); background-color: rgba(44, 90, 160, 0.05); position: relative;">
                <div style="position: absolute; top: -12px; right: 16px; background-color: var(--color-primary); color: white; padding: 4px 12px; border-radius: 999px; font-size: 0.75rem; font-weight: bold;">CURRENT</div>
                <h4 style="margin-top: 0; margin-bottom: var(--spacing-1);">Premium</h4>
                <p style="margin: 0 0 var(--spacing-3) 0; color: var(--color-gray-600);">Most popular</p>
                <div style="margin-bottom: var(--spacing-3);">
                    <span style="font-size: 2rem; font-weight: bold; color: var(--color-primary);">₹999</span>
                    <span style="color: var(--color-gray-600); margin-left: var(--spacing-1);">/month</span>
                </div>
                <ul style="list-style: none; padding: 0; margin: 0 0 var(--spacing-3) 0;">
                    <li style="padding: var(--spacing-1) 0; color: var(--color-gray-600);">• Unlimited tests</li>
                    <li style="padding: var(--spacing-1) 0; color: var(--color-gray-600);">• All courses</li>
                    <li style="padding: var(--spacing-1) 0; color: var(--color-gray-600);">• Advanced analytics</li>
                    <li style="padding: var(--spacing-1) 0; color: var(--color-gray-600);">• Priority support</li>
                </ul>
                <button class="btn btn-primary" style="width: 100%;" disabled>Your Plan</button>
            </div>

            <!-- Elite Plan -->
            <div class="student-card" style="border: 2px solid var(--color-success);">
                <h4 style="margin-top: 0; margin-bottom: var(--spacing-1);">Elite</h4>
                <p style="margin: 0 0 var(--spacing-3) 0; color: var(--color-gray-600);">For serious aspirants</p>
                <div style="margin-bottom: var(--spacing-3);">
                    <span style="font-size: 2rem; font-weight: bold; color: var(--color-primary);">₹1,999</span>
                    <span style="color: var(--color-gray-600); margin-left: var(--spacing-1);">/month</span>
                </div>
                <ul style="list-style: none; padding: 0; margin: 0 0 var(--spacing-3) 0;">
                    <li style="padding: var(--spacing-1) 0; color: var(--color-gray-600);">• Everything in Premium</li>
                    <li style="padding: var(--spacing-1) 0; color: var(--color-gray-600);">• Personal mentor</li>
                    <li style="padding: var(--spacing-1) 0; color: var(--color-gray-600);">• Live classes</li>
                    <li style="padding: var(--spacing-1) 0; color: var(--color-gray-600);">• Doubt support 24/7</li>
                </ul>
                <button class="btn btn-primary" style="width: 100%;">Upgrade to Elite</button>
            </div>
        </div>
    </div>

    <!-- Billing History -->
    <div class="student-card">
        <h3 style="margin-top: 0; margin-bottom: var(--spacing-3);">Billing History</h3>
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background-color: var(--color-gray-50); border-bottom: 2px solid var(--color-gray-200);">
                        <th style="padding: var(--spacing-2); text-align: left; font-weight: bold;">Date</th>
                        <th style="padding: var(--spacing-2); text-align: left; font-weight: bold;">Description</th>
                        <th style="padding: var(--spacing-2); text-align: left; font-weight: bold;">Amount</th>
                        <th style="padding: var(--spacing-2); text-align: left; font-weight: bold;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr style="border-bottom: 1px solid var(--color-gray-200);">
                        <td style="padding: var(--spacing-2);">May 20, 2026</td>
                        <td style="padding: var(--spacing-2);">Premium Plan - Monthly</td>
                        <td style="padding: var(--spacing-2);"><strong>₹999</strong></td>
                        <td style="padding: var(--spacing-2);"><span style="background-color: #D1FAE5; color: #065F46; padding: 4px 8px; border-radius: 4px; font-size: 0.875rem;">Paid</span></td>
                    </tr>
                    <tr style="border-bottom: 1px solid var(--color-gray-200);">
                        <td style="padding: var(--spacing-2);">Apr 20, 2026</td>
                        <td style="padding: var(--spacing-2);">Premium Plan - Monthly</td>
                        <td style="padding: var(--spacing-2);"><strong>₹999</strong></td>
                        <td style="padding: var(--spacing-2);"><span style="background-color: #D1FAE5; color: #065F46; padding: 4px 8px; border-radius: 4px; font-size: 0.875rem;">Paid</span></td>
                    </tr>
                    <tr style="border-bottom: 1px solid var(--color-gray-200);">
                        <td style="padding: var(--spacing-2);">Mar 20, 2026</td>
                        <td style="padding: var(--spacing-2);">Premium Plan - Monthly</td>
                        <td style="padding: var(--spacing-2);"><strong>₹999</strong></td>
                        <td style="padding: var(--spacing-2);"><span style="background-color: #D1FAE5; color: #065F46; padding: 4px 8px; border-radius: 4px; font-size: 0.875rem;">Paid</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
@endsection
