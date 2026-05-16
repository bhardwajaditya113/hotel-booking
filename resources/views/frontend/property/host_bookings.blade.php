@extends('frontend.dashboard.account_master')

@section('account_breadcrumb')
    <li><a href="{{ route('dashboard') }}">{{ __('frontend.host_hub.breadcrumb_account') }}</a></li>
    <li><span class="opacity-50 mx-1">/</span></li>
    <li><a href="{{ route('property.dashboard') }}">{{ __('frontend.host_hub.page_title') }}</a></li>
@endsection

@section('account_title')
    {{ __('frontend.host_hub.incoming_bookings_title') }}
@endsection

@section('account_content')
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-4">
        @if (session('message'))
            <div class="alert alert-{{ session('alert-type', 'info') }} rounded-4">{{ session('message') }}</div>
        @endif

        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
            <div>
                <h2 class="h5 fw-bold mb-1">{{ __('frontend.host_hub.incoming_bookings_title') }}</h2>
                <p class="text-muted small mb-0">{{ __('frontend.host_hub.incoming_bookings_sub') }}</p>
            </div>
            <a href="{{ route('property.dashboard') }}" class="btn btn-outline-secondary rounded-pill px-4">{{ __('frontend.host_listing.back_dashboard') }}</a>
        </div>

        @if ($bookings->isEmpty())
            <p class="text-muted mb-0">{{ __('frontend.host_hub.incoming_bookings_empty') }}</p>
        @else
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>{{ __('frontend.host_hub.table_reference') }}</th>
                            <th>{{ __('frontend.host_hub.table_guest') }}</th>
                            <th>{{ __('frontend.host_hub.table_property') }}</th>
                            <th>{{ __('frontend.host_hub.table_dates') }}</th>
                            <th>{{ __('frontend.host_hub.table_payment') }}</th>
                            <th>{{ __('frontend.host_hub.table_host_status') }}</th>
                            <th class="text-end">{{ __('frontend.host_hub.table_actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($bookings as $b)
                            <tr>
                                <td><span class="badge bg-secondary">{{ $b->code }}</span></td>
                                <td>
                                    <div class="fw-semibold">{{ $b->user?->name ?? $b->name }}</div>
                                    <div class="small text-muted">{{ $b->email }}</div>
                                </td>
                                <td>{{ $b->property?->name ?? '—' }}</td>
                                <td class="small">
                                    {{ \Carbon\Carbon::parse($b->check_in)->format('M j, Y') }}
                                    → {{ \Carbon\Carbon::parse($b->check_out)->format('M j, Y') }}
                                </td>
                                <td class="small">{{ $b->payment_method }}</td>
                                <td class="small">
                                    @if ($b->host_approval_status === 'pending')
                                        <span class="badge text-bg-warning text-dark">{{ __('frontend.host_hub.status_pending') }}</span>
                                    @elseif ($b->host_approval_status === 'approved')
                                        <span class="badge text-bg-success">{{ __('frontend.host_hub.status_approved') }}</span>
                                    @elseif ($b->host_approval_status === 'declined')
                                        <span class="badge text-bg-danger">{{ __('frontend.host_hub.status_declined') }}</span>
                                    @else
                                        <span class="badge text-bg-secondary">{{ __('frontend.host_hub.status_instant') }}</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    @if ($b->host_approval_status === 'pending')
                                        <div class="d-inline-flex flex-column align-items-end gap-2">
                                            <form action="{{ route('property.bookings.approve', $b) }}" method="POST" class="m-0">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success rounded-pill px-3">{{ __('frontend.host_hub.approve') }}</button>
                                            </form>
                                            <form action="{{ route('property.bookings.decline', $b) }}" method="POST" class="m-0 text-end" style="max-width: 14rem;">
                                                @csrf
                                                <label class="small text-muted d-block text-start mb-1">{{ __('frontend.host_hub.decline_note_optional') }}</label>
                                                <textarea name="notes" rows="2" class="form-control form-control-sm mb-2" maxlength="500" placeholder="{{ __('frontend.host_hub.decline_note_placeholder') }}"></textarea>
                                                <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3">{{ __('frontend.host_hub.decline') }}</button>
                                            </form>
                                        </div>
                                    @else
                                        —
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-3">{{ $bookings->links() }}</div>
        @endif
    </div>
</div>
@endsection
