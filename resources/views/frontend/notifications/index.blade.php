@extends('frontend.main_master')

@section('main')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold">Notifications</h1>
            <p class="text-gray-600 mt-1">Stay updated on your bookings and offers</p>
        </div>
        <div class="flex gap-3">
            @if($notifications->where('read_at', null)->count() > 0)
                <button onclick="markAllAsRead()" class="px-4 py-2 border rounded-lg hover:bg-gray-50">
                    <i class="fa-solid fa-check-double mr-2"></i> Mark all read
                </button>
            @endif
            <a href="{{ route('notifications.settings') }}" class="px-4 py-2 border rounded-lg hover:bg-gray-50">
                <i class="fa-solid fa-cog"></i>
            </a>
        </div>
    </div>

    <!-- Filter Tabs -->
    <div class="flex gap-4 mb-6 border-b">
        <button onclick="filterNotifications('all')" 
                class="px-4 py-2 font-medium border-b-2 transition notification-tab {{ request('filter') !== 'unread' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
            All
        </button>
        <button onclick="filterNotifications('unread')" 
                class="px-4 py-2 font-medium border-b-2 transition notification-tab {{ request('filter') === 'unread' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
            Unread 
            @if($unreadCount > 0)
                <span class="ml-1 px-2 py-0.5 bg-red-100 text-red-600 text-xs rounded-full">{{ $unreadCount }}</span>
            @endif
        </button>
    </div>

    @if($notifications->count() > 0)
        <div class="space-y-3">
            @foreach($notifications as $notification)
                <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition p-4 {{ is_null($notification->read_at) ? 'border-l-4 border-blue-500' : '' }}" 
                     id="notification-{{ $notification->id }}">
                    <div class="flex items-start">
                        <!-- Icon -->
                        <div class="w-12 h-12 rounded-full flex items-center justify-center flex-shrink-0
                            @switch($notification->type)
                                @case('booking_confirmed')
                                    bg-green-100 text-green-600
                                    @break
                                @case('booking_cancelled')
                                    bg-red-100 text-red-600
                                    @break
                                @case('payment_received')
                                    bg-blue-100 text-blue-600
                                    @break
                                @case('review_reminder')
                                    bg-yellow-100 text-yellow-600
                                    @break
                                @case('loyalty_points')
                                    bg-purple-100 text-purple-600
                                    @break
                                @case('offer')
                                    bg-orange-100 text-orange-600
                                    @break
                                @default
                                    bg-gray-100 text-gray-600
                            @endswitch
                        ">
                            <i class="fa-solid 
                                @switch($notification->type)
                                    @case('booking_confirmed')
                                        fa-calendar-check
                                        @break
                                    @case('booking_cancelled')
                                        fa-calendar-xmark
                                        @break
                                    @case('payment_received')
                                        fa-credit-card
                                        @break
                                    @case('review_reminder')
                                        fa-star
                                        @break
                                    @case('loyalty_points')
                                        fa-award
                                        @break
                                    @case('offer')
                                        fa-tag
                                        @break
                                    @default
                                        fa-bell
                                @endswitch
                            text-lg"></i>
                        </div>

                        <!-- Content -->
                        <div class="ml-4 flex-1">
                            <div class="flex items-start justify-between">
                                <div>
                                    <h3 class="font-semibold {{ is_null($notification->read_at) ? '' : 'text-gray-600' }}">
                                        {{ $notification->title }}
                                    </h3>
                                    <p class="text-gray-600 text-sm mt-1">{{ $notification->message }}</p>
                                    
                                    @if($notification->action_url)
                                        <a href="{{ $notification->action_url }}" 
                                           class="inline-block mt-2 text-blue-600 hover:underline text-sm font-medium">
                                            {{ $notification->action_text ?? 'View Details' }} <i class="fa-solid fa-arrow-right text-xs ml-1"></i>
                                        </a>
                                    @endif
                                </div>

                                <div class="flex items-center gap-2 ml-4">
                                    <span class="text-xs text-gray-400 whitespace-nowrap">
                                        {{ $notification->created_at->diffForHumans() }}
                                    </span>
                                    <div class="relative">
                                        <button onclick="toggleNotificationMenu({{ $notification->id }})" 
                                                class="p-1 hover:bg-gray-100 rounded">
                                            <i class="fa-solid fa-ellipsis-v text-gray-400"></i>
                                        </button>
                                        <div id="menu-{{ $notification->id }}" 
                                             class="hidden absolute right-0 mt-1 w-36 bg-white rounded-lg shadow-lg py-1 z-10">
                                            @if(is_null($notification->read_at))
                                                <button onclick="markAsRead({{ $notification->id }})" 
                                                        class="w-full px-4 py-2 text-left text-sm hover:bg-gray-100">
                                                    <i class="fa-solid fa-check mr-2"></i> Mark read
                                                </button>
                                            @endif
                                            <button onclick="deleteNotification({{ $notification->id }})" 
                                                    class="w-full px-4 py-2 text-left text-sm text-red-600 hover:bg-red-50">
                                                <i class="fa-solid fa-trash mr-2"></i> Delete
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-8">
            {{ $notifications->links() }}
        </div>
    @else
        <!-- Empty State -->
        <div class="text-center py-16">
            <div class="w-24 h-24 mx-auto bg-gray-100 rounded-full flex items-center justify-center mb-6">
                <i class="fa-solid fa-bell-slash text-4xl text-gray-400"></i>
            </div>
            <h2 class="text-2xl font-semibold mb-2">No notifications</h2>
            <p class="text-gray-600">You're all caught up! Check back later for updates.</p>
        </div>
    @endif
</div>

@push('scripts')
<script>
function filterNotifications(type) {
    const url = new URL(window.location.href);
    if (type === 'unread') {
        url.searchParams.set('filter', 'unread');
    } else {
        url.searchParams.delete('filter');
    }
    window.location.href = url.toString();
}

function toggleNotificationMenu(id) {
    document.querySelectorAll('[id^="menu-"]').forEach(menu => {
        if (menu.id !== `menu-${id}`) {
            menu.classList.add('hidden');
        }
    });
    document.getElementById(`menu-${id}`).classList.toggle('hidden');
}

// Close menus on click outside
document.addEventListener('click', function(e) {
    if (!e.target.closest('[id^="menu-"]') && !e.target.closest('[onclick^="toggleNotificationMenu"]')) {
        document.querySelectorAll('[id^="menu-"]').forEach(menu => menu.classList.add('hidden'));
    }
});

function markAsRead(id) {
    fetch(`/notifications/${id}/read`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const notificationEl = document.getElementById(`notification-${id}`);
            notificationEl.classList.remove('border-l-4', 'border-blue-500');
            notificationEl.querySelector('h3').classList.add('text-gray-600');
            document.getElementById(`menu-${id}`).classList.add('hidden');
        }
    });
}

function markAllAsRead() {
    fetch('/notifications/read-all', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    });
}

function deleteNotification(id) {
    if (confirm('Delete this notification?')) {
        fetch(`/notifications/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById(`notification-${id}`).remove();
            }
        });
    }
}
</script>
@endpush
@endsection
