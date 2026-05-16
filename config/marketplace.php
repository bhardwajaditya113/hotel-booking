<?php

/**
 * Marketplace settlement (India-focused): platform commission + GST on commission,
 * Razorpay Route transfers to linked host accounts when configured.
 *
 * Razorpay Route requires dashboard onboarding:
 * https://razorpay.com/docs/route/
 *
 * Set host_profiles.razorpay_linked_account_id after creating a Linked Account per host.
 */
return [

    /*
    |--------------------------------------------------------------------------
    | Razorpay Route (split remittance)
    |--------------------------------------------------------------------------
    */
    'route_enabled' => env('MARKETPLACE_ROUTE_ENABLED', false),

    /*
    |--------------------------------------------------------------------------
    | Hold routed funds before settlement (Route transfer on_hold)
    |--------------------------------------------------------------------------
    */
    'transfer_on_hold' => env('MARKETPLACE_TRANSFER_ON_HOLD', false),

    /*
    |--------------------------------------------------------------------------
    | Platform commission on guest totals (before GST line below)
    |--------------------------------------------------------------------------
    */
    'platform_fee_percent' => (float) env('MARKETPLACE_PLATFORM_FEE_PERCENT', 10),

    'platform_fee_fixed_inr' => (float) env('MARKETPLACE_PLATFORM_FEE_FIXED_INR', 0),

    /*
    |--------------------------------------------------------------------------
    | GST rate applied on platform commission only (India CGST+SGST display bucket)
    |--------------------------------------------------------------------------
    */
    'gst_percent_on_platform_fee' => (float) env('MARKETPLACE_GST_PERCENT_ON_PLATFORM_FEE', 18),

];
