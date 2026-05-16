<header class="navbar navbar-expand-md d-print-none nx-admin-topbar border-bottom shadow-sm">
    <div class="container-xl d-flex flex-wrap align-items-center gap-2 py-1 py-md-0">
        <button class="navbar-toggler d-lg-none me-1 nx-admin-menu-trigger rounded-3 border-0 p-2" type="button" data-bs-toggle="collapse" data-bs-target="#sidebar-menu" aria-controls="sidebar-menu" aria-expanded="false" aria-label="Toggle sidebar">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="navbar-nav flex-row flex-grow-1 align-items-center min-w-0">
            {{-- Mobile: brand repeats here while sidebar is collapsed --}}
            <div class="nav-item text-truncate d-lg-none">
                <a href="{{ route('admin.dashboard') }}" class="navbar-text mb-0 text-decoration-none">
                    <x-admin-brand-lockup size="sm" />
                </a>
            </div>
            {{-- Desktop: contextual title strip (sidebar holds primary logo) --}}
            <div class="nav-item text-truncate d-none d-lg-block ps-lg-1">
                <span class="navbar-text mb-0 nx-admin-toolbar-heading">
                    @hasSection('nx-admin-title')
                        @yield('nx-admin-title')
                    @else
                        <span class="nx-admin-toolbar-title">{{ config('app.name', 'Elapse') }}</span>
                        <span class="nx-admin-toolbar-sub">Operations workspace</span>
                    @endif
                </span>
            </div>
        </div>
        <div class="navbar-nav flex-row align-items-center gap-1 ms-md-auto">
            <div class="nav-item dropdown">
                <a class="nav-link px-2 position-relative" href="#" data-bs-toggle="dropdown" aria-label="Notifications" aria-expanded="false">
                    @php
                        $ncount = Auth::user()->panelNotifications()->unread()->count();
                    @endphp
                    <span class="badge bg-danger position-absolute top-0 start-100 translate-middle rounded-pill" style="font-size: 0.65rem;" id="notification-count">{{ $ncount }}</span>
                    <i class="bx bx-bell fs-4"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow nx-notify-dropdown shadow">
                    <span class="dropdown-header">Notifications</span>
                    @php $user = Auth::user(); @endphp
                    @forelse ($user->panelNotifications()->latest()->take(10)->get() as $notification)
                        <a class="dropdown-item nx-notify-item py-2" href="javascript:;" onclick="markNotificationAsRead('{{ $notification->id }}')">
                            <div class="d-flex align-items-start gap-2">
                                <span class="avatar avatar-sm bg-success-lt"><i class="bx bx-check-square"></i></span>
                                <div class="flex-grow-1 min-w-0">
                                    @php
                                        $msg = $notification->message ?? (is_array($notification->data ?? null) ? ($notification->data['message'] ?? '') : '');
                                    @endphp
                                    <div class="fw-semibold small">{{ $notification->title ?? \Illuminate\Support\Str::limit($msg, 48) }}</div>
                                    <div class="text-secondary small">{{ \Illuminate\Support\Str::limit($msg, 100) }}</div>
                                    <div class="text-secondary" style="font-size: 0.72rem;">{{ Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}</div>
                                </div>
                            </div>
                        </a>
                    @empty
                        <span class="dropdown-item text-secondary small py-3 text-center">No notifications yet</span>
                    @endforelse
                </div>
            </div>

            @php
                $id = Auth::user()->id;
                $profileData = App\Models\User::find($id);
            @endphp

            <div class="nav-item dropdown">
                <a class="nav-link d-flex align-items-center gap-2 py-1 px-2" href="#" data-bs-toggle="dropdown" aria-expanded="false">
                    <span class="avatar avatar-sm" style="background-image: url('{{ (!empty($profileData->photo)) ? url('upload/admin_images/'.$profileData->photo) : url('upload/no_image.jpg') }}')"></span>
                    <span class="d-none d-md-inline text-truncate" style="max-width: 9rem;">{{ $profileData->name }}</span>
                </a>
                <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow shadow">
                    <a class="dropdown-item" href="{{ route('admin.profile') }}"><i class="bx bx-user me-2"></i>Profile</a>
                    <a class="dropdown-item" href="{{ route('admin.change.password') }}"><i class="bx bx-lock me-2"></i>Change password</a>
                    <a class="dropdown-item" href="{{ route('admin.dashboard') }}"><i class="bx bx-home-circle me-2"></i>Dashboard</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item text-danger" href="{{ route('admin.logout') }}"><i class="bx bx-log-out-circle me-2"></i>Logout</a>
                </div>
            </div>
        </div>
    </div>
</header>

<script>
    function markNotificationAsRead(notificationId) {
        fetch('{{ url('/mark-notification-as-read') }}/' + encodeURIComponent(notificationId), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({})
        })
            .then(function (response) { return response.json(); })
            .then(function (data) {
                var el = document.getElementById('notification-count');
                if (el) {
                    el.textContent = data.count;
                }
            })
            .catch(function (error) {
                console.log('Error', error);
            });
    }
</script>
