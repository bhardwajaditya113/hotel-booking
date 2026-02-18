{{-- Wishlist Heart Button Component --}}
{{-- Usage: @include('components.wishlist-button', ['roomId' => $room->id, 'size' => 'lg']) --}}

@php
    $isWishlisted = auth()->check() ? auth()->user()->hasInWishlist($roomId) : false;
    $sizeClass = match($size ?? 'md') {
        'sm' => 'w-8 h-8 text-sm',
        'lg' => 'w-12 h-12 text-xl',
        default => 'w-10 h-10 text-base',
    };
@endphp

<button 
    type="button"
    onclick="toggleWishlist({{ $roomId }}, this)"
    class="wishlist-btn {{ $sizeClass }} rounded-full flex items-center justify-center transition-all duration-200 {{ $isWishlisted ? 'bg-red-500 text-white' : 'bg-white/90 text-gray-600 hover:bg-white hover:text-red-500' }} shadow-md hover:shadow-lg"
    data-room-id="{{ $roomId }}"
    data-wishlisted="{{ $isWishlisted ? 'true' : 'false' }}"
    title="{{ $isWishlisted ? 'Remove from wishlist' : 'Add to wishlist' }}"
>
    <i class="fa-{{ $isWishlisted ? 'solid' : 'regular' }} fa-heart"></i>
</button>

@once
@push('scripts')
<script>
function toggleWishlist(roomId, button) {
    @auth
    const isWishlisted = button.dataset.wishlisted === 'true';
    
    // Optimistic UI update
    button.classList.toggle('bg-red-500');
    button.classList.toggle('text-white');
    button.classList.toggle('bg-white/90');
    button.classList.toggle('text-gray-600');
    
    const icon = button.querySelector('i');
    icon.classList.toggle('fa-solid');
    icon.classList.toggle('fa-regular');
    
    fetch('{{ route('wishlist.toggle') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ room_id: roomId })
    })
    .then(res => res.json())
    .then(data => {
        button.dataset.wishlisted = data.added ? 'true' : 'false';
        button.title = data.added ? 'Remove from wishlist' : 'Add to wishlist';
        showToast(data.message, 'success');
    })
    .catch(error => {
        // Revert on error
        button.classList.toggle('bg-red-500');
        button.classList.toggle('text-white');
        button.classList.toggle('bg-white/90');
        button.classList.toggle('text-gray-600');
        icon.classList.toggle('fa-solid');
        icon.classList.toggle('fa-regular');
        showToast('Failed to update wishlist', 'error');
    });
    @else
    // Redirect to login
    window.location.href = '{{ route('login') }}?redirect=' + encodeURIComponent(window.location.href);
    @endauth
}

function showToast(message, type = 'success') {
    // Remove existing toasts
    document.querySelectorAll('.toast-notification').forEach(t => t.remove());
    
    const toast = document.createElement('div');
    toast.className = `toast-notification fixed bottom-4 right-4 px-6 py-3 rounded-lg shadow-lg z-50 transform transition-all duration-300 ${type === 'success' ? 'bg-green-600' : 'bg-red-600'} text-white`;
    toast.textContent = message;
    document.body.appendChild(toast);
    
    // Animate in
    requestAnimationFrame(() => {
        toast.classList.add('translate-y-0', 'opacity-100');
    });
    
    // Remove after delay
    setTimeout(() => {
        toast.classList.add('translate-y-4', 'opacity-0');
        setTimeout(() => toast.remove(), 300);
    }, 2500);
}
</script>
@endpush
@endonce
