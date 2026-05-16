@extends('admin.admin_dashboard')
@section('admin')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

@php
    use App\Support\Currency;
    $fmtWeek = function (?float $pct): string {
        if ($pct === null) {
            return 'No comparison data (last week)';
        }
        $sign = $pct > 0 ? '+' : '';

        return $sign . number_format($pct, 1) . '% vs last week';
    };
@endphp

<div class="page-content">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
        <div>
            <h5 class="mb-0">Dashboard</h5>
            <p class="text-secondary small mb-0">Figures refresh from your live database. Cards auto-update every 45 seconds.</p>
        </div>
        <span class="badge bg-secondary-lt text-secondary" id="nx-dashboard-as-of" title="Last stats refresh">Updated: {{ \Carbon\Carbon::parse($stats['generated_at'])->timezone(config('app.timezone'))->format('M j, H:i') }}</span>
    </div>

    <div class="row row-cols-1 row-cols-md-2 row-cols-xl-4 g-3">
        <div class="col">
            <div class="card border-start border-0 border-4 border-info">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div>
                            <p class="mb-0 text-secondary">Total bookings</p>
                            <h4 class="my-1 text-info" id="nx-stat-total-bookings">{{ $stats['total_bookings'] }}</h4>
                            <p class="mb-0 font-13">Today: <span id="nx-stat-today-revenue">{{ Currency::inr($stats['today_revenue']) }}</span></p>
                        </div>
                        <div class="rounded-circle bg-info-lt text-info ms-auto p-3"><i class="bx bxs-cart fs-4"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card border-start border-0 border-4 border-danger">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div>
                            <p class="mb-0 text-secondary">Pending bookings</p>
                            <h4 class="my-1 text-danger" id="nx-stat-pending-bookings">{{ $stats['pending_bookings'] }}</h4>
                            <p class="mb-0 font-13" id="nx-stat-pending-trend">{{ $fmtWeek($stats['pending_week_pct']) }}</p>
                        </div>
                        <div class="rounded-circle bg-danger-lt text-danger ms-auto p-3"><i class="bx bxs-wallet fs-4"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card border-start border-0 border-4 border-success">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div>
                            <p class="mb-0 text-secondary">Confirmed / complete</p>
                            <h4 class="my-1 text-success" id="nx-stat-complete-bookings">{{ $stats['complete_bookings'] }}</h4>
                            <p class="mb-0 font-13" id="nx-stat-complete-trend">{{ $fmtWeek($stats['complete_week_pct']) }}</p>
                        </div>
                        <div class="rounded-circle bg-success-lt text-success ms-auto p-3"><i class="bx bx-line-chart fs-4"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card border-start border-0 border-4 border-warning">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div>
                            <p class="mb-0 text-secondary">Total revenue (INR)</p>
                            <h4 class="my-1 text-warning" id="nx-stat-total-revenue">{{ Currency::inr($stats['total_revenue']) }}</h4>
                            <p class="mb-0 font-13" id="nx-stat-revenue-trend">{{ $fmtWeek($stats['revenue_week_pct']) }}</p>
                        </div>
                        <div class="rounded-circle bg-warning-lt text-warning ms-auto p-3"><i class="bx bx-rupee fs-4"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row row-cols-1 row-cols-md-2 row-cols-xl-4 g-3 mt-1">
        <div class="col">
            <div class="card border-start border-0 border-4 border-primary">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div>
                            <p class="mb-0 text-secondary">Total properties</p>
                            <h4 class="my-1 text-primary" id="nx-stat-total-properties">{{ $stats['total_properties'] }}</h4>
                            <p class="mb-0 font-13">Hotels: <span id="nx-stat-hotel-properties">{{ $stats['hotel_properties'] }}</span> | Unique: <span id="nx-stat-unique-properties">{{ $stats['unique_stay_properties'] }}</span></p>
                        </div>
                        <div class="rounded-circle bg-primary-lt text-primary ms-auto p-3"><i class="bx bx-building-house fs-4"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card border-start border-0 border-4 border-warning">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div>
                            <p class="mb-0 text-secondary">Pending verification</p>
                            <h4 class="my-1 text-warning" id="nx-stat-pending-properties">{{ $stats['pending_properties'] }}</h4>
                            <p class="mb-0 font-13">Properties awaiting review</p>
                        </div>
                        <div class="rounded-circle bg-warning-lt text-warning ms-auto p-3"><i class="bx bx-time-five fs-4"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card border-start border-0 border-4 border-success">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div>
                            <p class="mb-0 text-secondary">Verified properties</p>
                            <h4 class="my-1 text-success" id="nx-stat-verified-properties">{{ $stats['verified_properties'] }}</h4>
                            <p class="mb-0 font-13">Active &amp; verified</p>
                        </div>
                        <div class="rounded-circle bg-success-lt text-success ms-auto p-3"><i class="bx bx-check-circle fs-4"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card border-start border-0 border-4 border-info">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div>
                            <p class="mb-0 text-secondary">Total hosts</p>
                            <h4 class="my-1 text-info" id="nx-stat-total-hosts">{{ $stats['total_hosts'] }}</h4>
                            <p class="mb-0 font-13">Superhosts: <span id="nx-stat-superhosts">{{ $stats['superhosts'] }}</span></p>
                        </div>
                        <div class="rounded-circle bg-info-lt text-info ms-auto p-3"><i class="bx bx-user-circle fs-4"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Quick actions</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <a href="{{ route('admin.verification.properties.index') }}" class="btn btn-outline-primary w-100">
                                <i class="bx bx-check-square me-1"></i> Verify properties
                                @if ($stats['pending_properties'] > 0)
                                    <span class="badge bg-danger ms-1" id="nx-badge-pending-properties">{{ $stats['pending_properties'] }}</span>
                                @endif
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('admin.verification.hosts.index') }}" class="btn btn-outline-success w-100">
                                <i class="bx bx-user-check me-1"></i> Verify hosts
                                @if ($stats['pending_hosts'] > 0)
                                    <span class="badge bg-danger ms-1" id="nx-badge-pending-hosts">{{ $stats['pending_hosts'] }}</span>
                                @endif
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('admin.reviews.index') }}" class="btn btn-outline-warning w-100">
                                <i class="bx bx-star me-1"></i> Manage reviews
                                @if ($stats['pending_reviews'] > 0)
                                    <span class="badge bg-danger ms-1" id="nx-badge-pending-reviews">{{ $stats['pending_reviews'] }}</span>
                                @endif
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('admin.coupons.index') }}" class="btn btn-outline-info w-100">
                                <i class="bx bx-gift me-1"></i> Manage coupons
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Daily booking revenue (INR, last 14 days)</h6>
                </div>
                <div class="card-body">
                    <canvas id="bookingChart" height="100"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="card mt-3">
        <div class="card-header">
            <h6 class="mb-0">Recent bookings</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-bordered mb-0" style="width:100%">
                    <thead>
                        <tr>
                            <th>Sl</th>
                            <th>Code</th>
                            <th>Date</th>
                            <th>Customer</th>
                            <th>Stay</th>
                            <th>Check in / out</th>
                            <th>Rooms</th>
                            <th>Guests</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($recentBookings as $key => $item)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td><a href="{{ route('edit_booking', $item->id) }}">{{ $item->code }}</a></td>
                                <td>{{ $item->created_at->format('d/m/Y') }}</td>
                                <td>{{ $item->user?->name ?? '—' }}</td>
                                <td>{{ $item->stay_label }}</td>
                                <td>
                                    <span class="badge bg-primary">{{ $item->check_in->format('Y-m-d') }}</span>
                                    <span class="badge bg-warning text-dark">{{ $item->check_out->format('Y-m-d') }}</span>
                                </td>
                                <td>{{ $item->number_of_rooms }}</td>
                                <td>{{ $item->persion }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    var chartLabels = @json($stats['chart_labels']);
    var chartAmounts = @json($stats['chart_amounts']);
    var ctx = document.getElementById('bookingChart');
    var bookingChart = new Chart(ctx.getContext('2d'), {
        type: 'bar',
        data: {
            labels: chartLabels,
            datasets: [{
                label: 'Revenue (INR)',
                data: chartAmounts,
                backgroundColor: 'rgba(13, 110, 110, 0.35)',
                borderColor: 'rgba(13, 110, 110, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: { beginAtZero: true }
            }
        }
    });

    function formatInr(n) {
        var x = Number(n) || 0;
        return '₹' + x.toLocaleString('en-IN', { maximumFractionDigits: 0 });
    }

    function formatWeekTrend(pct) {
        if (pct === null || pct === undefined || isNaN(pct)) {
            return 'No comparison data (last week)';
        }
        var sign = pct > 0 ? '+' : '';
        return sign + Number(pct).toFixed(1) + '% vs last week';
    }

    function formatTime(iso) {
        try {
            var d = new Date(iso);
            return d.toLocaleString(undefined, { month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' });
        } catch (e) {
            return '';
        }
    }

    function applyStats(s) {
        var el;
        el = document.getElementById('nx-stat-total-bookings'); if (el) el.textContent = s.total_bookings;
        el = document.getElementById('nx-stat-pending-bookings'); if (el) el.textContent = s.pending_bookings;
        el = document.getElementById('nx-stat-complete-bookings'); if (el) el.textContent = s.complete_bookings;
        el = document.getElementById('nx-stat-today-revenue'); if (el) el.textContent = formatInr(s.today_revenue);
        el = document.getElementById('nx-stat-total-revenue'); if (el) el.textContent = formatInr(s.total_revenue);
        el = document.getElementById('nx-stat-pending-trend'); if (el) el.textContent = formatWeekTrend(s.pending_week_pct);
        el = document.getElementById('nx-stat-complete-trend'); if (el) el.textContent = formatWeekTrend(s.complete_week_pct);
        el = document.getElementById('nx-stat-revenue-trend'); if (el) el.textContent = formatWeekTrend(s.revenue_week_pct);
        el = document.getElementById('nx-stat-total-properties'); if (el) el.textContent = s.total_properties;
        el = document.getElementById('nx-stat-hotel-properties'); if (el) el.textContent = s.hotel_properties;
        el = document.getElementById('nx-stat-unique-properties'); if (el) el.textContent = s.unique_stay_properties;
        el = document.getElementById('nx-stat-pending-properties'); if (el) el.textContent = s.pending_properties;
        el = document.getElementById('nx-stat-verified-properties'); if (el) el.textContent = s.verified_properties;
        el = document.getElementById('nx-stat-total-hosts'); if (el) el.textContent = s.total_hosts;
        el = document.getElementById('nx-stat-superhosts'); if (el) el.textContent = s.superhosts;
        el = document.getElementById('nx-dashboard-as-of'); if (el) el.textContent = 'Updated: ' + formatTime(s.generated_at);

        bookingChart.data.labels = s.chart_labels;
        bookingChart.data.datasets[0].data = s.chart_amounts;
        bookingChart.update();

        function setBadge(id, n) {
            var b = document.getElementById(id);
            if (!b) return;
            if (n > 0) {
                b.textContent = n;
                b.style.display = '';
            } else {
                b.style.display = 'none';
            }
        }
        setBadge('nx-badge-pending-properties', s.pending_properties);
        setBadge('nx-badge-pending-hosts', s.pending_hosts);
        setBadge('nx-badge-pending-reviews', s.pending_reviews);
    }

    var statsUrl = @json(route('admin.dashboard.stats'));
    setInterval(function () {
        fetch(statsUrl, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function (r) { return r.json(); })
            .then(applyStats)
            .catch(function () {});
    }, 45000);
})();
</script>

@endsection
