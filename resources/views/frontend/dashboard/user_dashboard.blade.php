@extends('frontend.dashboard.account_master')

@section('account_title', __('frontend.account.title_overview'))

@section('account_content')
    <div class="service-article-title">
        <h2>Account overview</h2>
    </div>
    <div class="service-article-content">
        <p class="mb-4">Welcome back. Here is a snapshot of your bookings.</p>
        <div class="nx-stat-grid">
            <div class="nx-stat-card nx-stat-card--accent">
                <div class="nx-stat-label">Total bookings</div>
                <div class="nx-stat-value">{{ number_format($bookingStats['total'] ?? 0) }}</div>
            </div>
            <div class="nx-stat-card">
                <div class="nx-stat-label">Pending</div>
                <div class="nx-stat-value">{{ number_format($bookingStats['pending'] ?? 0) }}</div>
            </div>
            <div class="nx-stat-card">
                <div class="nx-stat-label">Completed</div>
                <div class="nx-stat-value">{{ number_format($bookingStats['completed'] ?? 0) }}</div>
            </div>
        </div>
        <div class="mt-4 d-flex flex-wrap gap-2">
            <a href="{{ route('user.booking') }}" class="btn btn-primary">View my bookings</a>
            <a href="{{ route('search.results', ['search_mode' => 'properties']) }}" class="btn btn-outline-primary">Find a stay</a>
        </div>
    </div>
@endsection
