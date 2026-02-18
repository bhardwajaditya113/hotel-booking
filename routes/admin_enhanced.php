<?php

use App\Http\Controllers\Backend\EnhancedFeaturesController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Enhanced Features Routes
|--------------------------------------------------------------------------
|
| Routes for managing reviews, loyalty, coupons, pricing rules in admin panel
|
*/

Route::middleware(['auth', 'roles:admin'])->prefix('admin')->name('admin.')->group(function () {
    
    // ==========================================
    // REVIEW MANAGEMENT
    // ==========================================
    Route::prefix('reviews')->name('reviews.')->controller(EnhancedFeaturesController::class)->group(function () {
        Route::get('/', 'reviewIndex')->name('index');
        Route::get('/{id}', 'reviewShow')->name('show');
        Route::post('/{id}/approve', 'reviewApprove')->name('approve');
        Route::post('/{id}/reject', 'reviewReject')->name('reject');
        Route::post('/{id}/respond', 'reviewRespond')->name('respond');
    });

    // ==========================================
    // LOYALTY REWARDS MANAGEMENT
    // ==========================================
    Route::prefix('loyalty/rewards')->name('loyalty.rewards.')->controller(EnhancedFeaturesController::class)->group(function () {
        Route::get('/', 'rewardIndex')->name('index');
        Route::get('/create', 'rewardCreate')->name('create');
        Route::post('/', 'rewardStore')->name('store');
        Route::get('/{id}/edit', 'rewardEdit')->name('edit');
        Route::put('/{id}', 'rewardUpdate')->name('update');
        Route::delete('/{id}', 'rewardDestroy')->name('destroy');
    });

    // ==========================================
    // CANCELLATION POLICIES MANAGEMENT
    // ==========================================
    Route::prefix('policies')->name('policies.')->controller(EnhancedFeaturesController::class)->group(function () {
        Route::get('/', 'policyIndex')->name('index');
        Route::get('/create', 'policyCreate')->name('create');
        Route::post('/', 'policyStore')->name('store');
        Route::get('/{id}/edit', 'policyEdit')->name('edit');
        Route::put('/{id}', 'policyUpdate')->name('update');
        Route::delete('/{id}', 'policyDestroy')->name('destroy');
    });

    // ==========================================
    // COUPON MANAGEMENT
    // ==========================================
    Route::prefix('coupons')->name('coupons.')->controller(EnhancedFeaturesController::class)->group(function () {
        Route::get('/', 'couponIndex')->name('index');
        Route::get('/create', 'couponCreate')->name('create');
        Route::post('/', 'couponStore')->name('store');
        Route::get('/{id}/edit', 'couponEdit')->name('edit');
        Route::put('/{id}', 'couponUpdate')->name('update');
        Route::post('/{id}/toggle', 'couponToggle')->name('toggle');
        Route::delete('/{id}', 'couponDestroy')->name('destroy');
    });

    // ==========================================
    // PRICING RULES MANAGEMENT
    // ==========================================
    Route::prefix('pricing')->name('pricing.')->controller(EnhancedFeaturesController::class)->group(function () {
        Route::get('/', 'pricingIndex')->name('index');
        Route::get('/create', 'pricingCreate')->name('create');
        Route::post('/', 'pricingStore')->name('store');
        Route::get('/{id}/edit', 'pricingEdit')->name('edit');
        Route::put('/{id}', 'pricingUpdate')->name('update');
        Route::post('/{id}/toggle', 'pricingToggle')->name('toggle');
        Route::delete('/{id}', 'pricingDestroy')->name('destroy');
    });

    // ==========================================
    // PROPERTY VERIFICATION
    // ==========================================
    Route::prefix('verification/properties')->name('verification.properties.')->controller(\App\Http\Controllers\Backend\PropertyVerificationController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/{id}', 'show')->name('show');
        Route::post('/{id}/verify', 'verify')->name('verify');
        Route::post('/bulk-verify', 'bulkVerify')->name('bulk-verify');
    });

    // ==========================================
    // HOST VERIFICATION
    // ==========================================
    Route::prefix('verification/hosts')->name('verification.hosts.')->controller(\App\Http\Controllers\Backend\HostVerificationController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/{id}', 'show')->name('show');
        Route::post('/{id}/verify', 'verify')->name('verify');
        Route::post('/{id}/toggle-superhost', 'toggleSuperhost')->name('toggle-superhost');
        Route::post('/bulk-verify', 'bulkVerify')->name('bulk-verify');
    });
});
