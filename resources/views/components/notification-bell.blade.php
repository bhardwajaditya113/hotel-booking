{{-- Notification Bell with Dropdown for Navbar --}}
{{-- Include in main navigation: @include('components.notification-bell') --}}

@auth
<div class="relative" x-data="{ open: false, notifications: [], unreadCount: 0 }" x-init="
    fetch('{{ route('notifications.recent') }}')
        .then(r => r.json())
        .then(data => { notifications = data.notifications; unreadCount = data.unread_count; });
    
    // Poll for new notifications every 60 seconds
    setInterval(() => {
        fetch('{{ route('notifications.unread-count') }}')
            .then(r => r.json())
            .then(data => { unreadCount = data.count; });
    }, 60000);
">
    <!-- Bell Icon -->
    <button @click="open = !open" class="relative p-2 text-gray-600 hover:text-gray-900 focus:outline-none">
        <i class="fa-solid fa-bell text-xl"></i>
        <!-- Unread Badge -->
        <span x-show="unreadCount > 0" 
              x-text="unreadCount > 9 ? '9+' : unreadCount"
              class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white text-xs rounded-full flex items-center justify-center font-medium">
        </span>
    </button>

    <!-- Dropdown -->
    <div x-show="open" 
         @click.away="open = false"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 transform scale-95"
         x-transition:enter-end="opacity-100 transform scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 transform scale-100"
         x-transition:leave-end="opacity-0 transform scale-95"
         class="absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-lg ring-1 ring-black ring-opacity-5 z-50"
         style="display: none;">
        
        <!-- Header -->
        <div class="px-4 py-3 border-b flex items-center justify-between">
            <h3 class="font-semibold text-gray-900">Notifications</h3>
            <button x-show="unreadCount > 0" 
                    @click="
                        fetch('{{ route('notifications.read-all') }}', { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }})
                            .then(() => { unreadCount = 0; notifications.forEach(n => n.read_at = new Date()); });
                    "
                    class="text-xs text-blue-600 hover:underline">
                Mark all read
            </button>
        </div>

        <!-- Notification List -->
        <div class="max-h-96 overflow-y-auto">
            <template x-if="notifications.length === 0">
                <div class="px-4 py-8 text-center text-gray-500">
                    <i class="fa-solid fa-bell-slash text-3xl mb-2"></i>
                    <p>No notifications</p>
                </div>
            </template>
            
            <template x-for="notification in notifications" :key="notification.id">
                <a :href="notification.action_url || '#'" 
                   class="block px-4 py-3 hover:bg-gray-50 border-b last:border-b-0"
                   :class="{ 'bg-blue-50': !notification.read_at }">
                    <div class="flex items-start">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0"
                             :class="{
                                 'bg-green-100 text-green-600': notification.type === 'booking_confirmed',
                                 'bg-red-100 text-red-600': notification.type === 'booking_cancelled',
                                 'bg-blue-100 text-blue-600': notification.type === 'payment_received',
                                 'bg-yellow-100 text-yellow-600': notification.type === 'review_reminder',
                                 'bg-purple-100 text-purple-600': notification.type === 'loyalty_points',
                                 'bg-gray-100 text-gray-600': !['booking_confirmed','booking_cancelled','payment_received','review_reminder','loyalty_points'].includes(notification.type)
                             }">
                            <i class="fa-solid"
                               :class="{
                                   'fa-calendar-check': notification.type === 'booking_confirmed',
                                   'fa-calendar-xmark': notification.type === 'booking_cancelled',
                                   'fa-credit-card': notification.type === 'payment_received',
                                   'fa-star': notification.type === 'review_reminder',
                                   'fa-award': notification.type === 'loyalty_points',
                                   'fa-bell': !['booking_confirmed','booking_cancelled','payment_received','review_reminder','loyalty_points'].includes(notification.type)
                               }"></i>
                        </div>
                        <div class="ml-3 flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate" x-text="notification.title"></p>
                            <p class="text-xs text-gray-500 truncate" x-text="notification.message"></p>
                            <p class="text-xs text-gray-400 mt-1" x-text="new Date(notification.created_at).toLocaleDateString()"></p>
                        </div>
                        <div x-show="!notification.read_at" class="w-2 h-2 bg-blue-500 rounded-full flex-shrink-0 mt-2"></div>
                    </div>
                </a>
            </template>
        </div>

        <!-- Footer -->
        <div class="px-4 py-3 border-t bg-gray-50 rounded-b-xl">
            <a href="{{ route('notifications.index') }}" class="block text-center text-sm text-blue-600 hover:underline font-medium">
                View all notifications
            </a>
        </div>
    </div>
</div>
@endauth
