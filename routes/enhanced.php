<?php

use App\Http\Controllers\Frontend\ReviewController;
use App\Http\Controllers\Frontend\WishlistController;
use App\Http\Controllers\Frontend\WalletController;
use App\Http\Controllers\Frontend\LoyaltyController;
use App\Http\Controllers\Frontend\CancellationController;
use App\Http\Controllers\Frontend\SearchController;
use App\Http\Controllers\Frontend\NotificationController;
use App\Http\Controllers\Frontend\CouponController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Enhanced Features Routes
|--------------------------------------------------------------------------
|
| Routes for all the new enhanced features including reviews, wishlists,
| wallet, loyalty program, cancellation, and advanced search.
|
*/

// ==========================================
// ADVANCED SEARCH ROUTES (Public)
// ==========================================
Route::controller(SearchController::class)->group(function () {
    Route::get('/search', 'index')->name('search');
    Route::get('/search/results', 'search')->name('search.results');
    Route::get('/search/suggestions', 'suggestions')->name('search.suggestions');
    Route::post('/search/quick', 'quickSearch')->name('search.quick');
    Route::get('/search/filter-counts', 'filterCounts')->name('search.filter-counts');
    Route::get('/rooms/compare', 'compare')->name('rooms.compare');
    Route::get('/rooms/map', 'mapView')->name('rooms.map');
});

// ==========================================
// REVIEWS ROUTES (Public viewing, Auth for actions)
// ==========================================
Route::controller(ReviewController::class)->group(function () {
    // Public routes
    Route::get('/room/{roomId}/reviews', 'index')->name('room.reviews');
    Route::get('/room/{roomId}/reviews/load', 'getReviews')->name('room.reviews.load');
});

Route::middleware(['auth'])->group(function () {
    Route::controller(ReviewController::class)->group(function () {
        Route::get('/booking/{bookingId}/review', 'create')->name('review.create');
        Route::post('/booking/{bookingId}/review', 'store')->name('review.store');
        Route::post('/review/{reviewId}/helpful', 'markHelpful')->name('review.helpful');
        Route::get('/my-reviews', 'myReviews')->name('reviews.mine');
    });
});

// ==========================================
// WISHLIST ROUTES (Auth required)
// ==========================================
Route::middleware(['auth'])->group(function () {
    Route::controller(WishlistController::class)->group(function () {
        Route::get('/wishlists', 'index')->name('wishlists.index');
        Route::get('/wishlists/{id}', 'show')->name('wishlists.show');
        Route::post('/wishlists', 'store')->name('wishlists.store');
        Route::put('/wishlists/{id}', 'update')->name('wishlists.update');
        Route::delete('/wishlists/{id}', 'destroy')->name('wishlists.destroy');
        
        // Wishlist items
        Route::post('/wishlist/add', 'addItem')->name('wishlist.add');
        Route::post('/wishlist/remove', 'removeItem')->name('wishlist.remove');
        Route::post('/wishlist/toggle', 'toggle')->name('wishlist.toggle');
        Route::post('/wishlist/move', 'moveItem')->name('wishlist.move');
        Route::get('/wishlists-list', 'getWishlists')->name('wishlists.list');
        
        // Sharing
        Route::post('/wishlists/{id}/share', 'share')->name('wishlists.share');
    });
});

// Shared wishlist (public with token)
Route::get('/shared/wishlist/{token}', [WishlistController::class, 'viewShared'])->name('wishlists.shared');

// ==========================================
// WALLET ROUTES (Auth required)
// ==========================================
Route::middleware(['auth'])->group(function () {
    Route::controller(WalletController::class)->prefix('wallet')->name('wallet.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/add-money', 'showAddMoney')->name('add-money');
        Route::post('/add-money', 'addMoney')->name('add-money.process');
        Route::get('/transactions', 'getTransactions')->name('transactions');
        Route::post('/statement', 'downloadStatement')->name('statement');
        
        // Payment callbacks
        Route::get('/payment/success/{transaction}', 'paymentSuccess')->name('payment.success');
        Route::get('/payment/cancel/{transaction}', 'paymentCancel')->name('payment.cancel');
        Route::post('/razorpay/callback', 'razorpayCallback')->name('razorpay.callback');
    });
});

// ==========================================
// LOYALTY PROGRAM ROUTES (Auth required)
// ==========================================
Route::middleware(['auth'])->group(function () {
    Route::controller(LoyaltyController::class)->prefix('loyalty')->name('loyalty.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/tiers', 'tiers')->name('tiers');
        Route::get('/history', 'history')->name('history');
        Route::get('/rewards', 'rewards')->name('rewards');
        Route::post('/rewards/{rewardId}/redeem', 'redeemReward')->name('rewards.redeem');
        Route::get('/my-rewards', 'myRewards')->name('my-rewards');
        Route::get('/rewards/{userRewardId}/use', 'useReward')->name('rewards.use');
        Route::get('/referrals', 'referrals')->name('referrals');
        Route::get('/balance', 'getBalance')->name('balance');
    });
});

// ==========================================
// CANCELLATION ROUTES (Auth required)
// ==========================================
Route::middleware(['auth'])->group(function () {
    Route::controller(CancellationController::class)->group(function () {
        Route::get('/booking/{bookingId}/cancel', 'show')->name('cancellation.show');
        Route::post('/booking/{bookingId}/cancel', 'process')->name('cancellation.process');
        Route::get('/cancellation/{cancellationId}', 'details')->name('cancellation.details');
    });
});

// Public cancellation policy routes
Route::get('/cancellation-policies', [CancellationController::class, 'policies'])->name('cancellation.policies');
Route::get('/room/{roomId}/cancellation-policy', [CancellationController::class, 'getPolicy'])->name('room.cancellation-policy');

// ==========================================
// NOTIFICATION ROUTES (Auth required)
// ==========================================
Route::middleware(['auth'])->group(function () {
    Route::controller(NotificationController::class)->prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/unread-count', 'unreadCount')->name('unread-count');
        Route::get('/recent', 'recent')->name('recent');
        Route::post('/{id}/read', 'markAsRead')->name('read');
        Route::post('/read-all', 'markAllAsRead')->name('read-all');
        Route::delete('/{id}', 'destroy')->name('destroy');
        Route::get('/settings', 'settings')->name('settings');
        Route::put('/settings', 'updateSettings')->name('settings.update');
    });
});

// ==========================================
// COUPON ROUTES
// ==========================================
Route::middleware(['auth'])->group(function () {
    Route::controller(CouponController::class)->prefix('coupon')->name('coupon.')->group(function () {
        Route::post('/apply', 'apply')->name('apply');
        Route::post('/remove', 'remove')->name('remove');
        Route::get('/available', 'available')->name('available');
    });
});

// ==========================================
// ADDITIONAL REVIEW ROUTES
// ==========================================
Route::get('/reviews/{roomId}/load-more', [ReviewController::class, 'loadMore'])->name('reviews.load-more');
Route::middleware(['auth'])->group(function () {
    Route::post('/reviews/{reviewId}/report', [ReviewController::class, 'report'])->name('review.report');
});

