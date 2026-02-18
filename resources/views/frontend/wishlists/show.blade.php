@extends('frontend.main_master')

@section('main')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <a href="{{ route('wishlists.index') }}" class="text-blue-600 hover:underline mb-2 inline-block">
                <i class="fa-solid fa-arrow-left mr-2"></i> Back to Wishlists
            </a>
            <div class="flex flex-wrap items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold">{{ $wishlist->name }}</h1>
                    @if($wishlist->description)
                    <p class="text-gray-600 mt-1">{{ $wishlist->description }}</p>
                    @endif
                </div>
                <div class="flex gap-2 mt-4 sm:mt-0">
                    <button onclick="shareWishlist()" class="px-4 py-2 border rounded-lg hover:bg-gray-50">
                        <i class="fa-solid fa-share-nodes mr-2"></i> Share
                    </button>
                    <button onclick="editWishlist()" class="px-4 py-2 border rounded-lg hover:bg-gray-50">
                        <i class="fa-solid fa-edit mr-2"></i> Edit
                    </button>
                </div>
            </div>
            <p class="text-sm text-gray-500 mt-2">{{ $wishlist->items->count() }} items</p>
        </div>

        <!-- Wishlist Items -->
        @if($wishlist->items->count() > 0)
        <div class="space-y-4">
            @foreach($wishlist->items as $item)
            <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition" id="item-{{ $item->id }}">
                <div class="flex flex-col md:flex-row">
                    <div class="md:w-1/3">
                        <a href="{{ url('/room/details/' . $item->room->id) }}">
                            <img src="{{ asset($item->room->image) }}" alt="{{ $item->room->room_name }}" 
                                 class="w-full h-48 md:h-full object-cover">
                        </a>
                    </div>
                    <div class="md:w-2/3 p-6">
                        <div class="flex justify-between items-start">
                            <div>
                                <a href="{{ url('/room/details/' . $item->room->id) }}" class="text-xl font-semibold hover:text-blue-600">
                                    {{ $item->room->room_name }}
                                </a>
                                <p class="text-gray-500">{{ $item->room->type->name ?? 'Standard Room' }}</p>
                            </div>
                            <button onclick="removeFromWishlist({{ $item->id }}, {{ $item->room->id }})" 
                                    class="text-red-500 hover:text-red-700 p-2">
                                <i class="fa-solid fa-heart text-xl"></i>
                            </button>
                        </div>

                        <!-- Room Details -->
                        <div class="flex flex-wrap gap-4 mt-4 text-sm text-gray-600">
                            <span><i class="fa-solid fa-user mr-1"></i> {{ $item->room->total_adult }} Guests</span>
                            <span><i class="fa-solid fa-bed mr-1"></i> {{ $item->room->bed_style ?? 'Standard' }}</span>
                            @if($item->room->room_capacity)
                            <span><i class="fa-solid fa-expand mr-1"></i> {{ $item->room->room_capacity }} sq ft</span>
                            @endif
                        </div>

                        <!-- Rating -->
                        @if($item->room->reviews && $item->room->reviews->count() > 0)
                        <div class="flex items-center mt-3">
                            <span class="bg-green-600 text-white px-2 py-1 rounded text-sm font-semibold">
                                {{ number_format($item->room->reviews->avg('overall_rating'), 1) }}
                            </span>
                            <span class="ml-2 text-gray-500">{{ $item->room->reviews->count() }} reviews</span>
                        </div>
                        @endif

                        <!-- Price & Actions -->
                        <div class="flex flex-wrap items-end justify-between mt-4 pt-4 border-t">
                            <div>
                                <span class="text-2xl font-bold text-blue-600">₹{{ number_format($item->room->price) }}</span>
                                <span class="text-gray-500">/night</span>
                            </div>
                            <div class="flex gap-2 mt-2 sm:mt-0">
                                <select onchange="moveToWishlist({{ $item->id }}, this.value)" 
                                        class="px-3 py-2 border rounded-lg text-sm">
                                    <option value="">Move to...</option>
                                    @foreach(auth()->user()->wishlists as $otherWishlist)
                                        @if($otherWishlist->id !== $wishlist->id)
                                        <option value="{{ $otherWishlist->id }}">{{ $otherWishlist->name }}</option>
                                        @endif
                                    @endforeach
                                </select>
                                <a href="{{ url('/room/details/' . $item->room->id) }}" 
                                   class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                    View Room
                                </a>
                            </div>
                        </div>

                        @if($item->notes)
                        <div class="mt-3 p-3 bg-yellow-50 rounded-lg text-sm">
                            <i class="fa-solid fa-sticky-note mr-2 text-yellow-600"></i>
                            {{ $item->notes }}
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-16 bg-white rounded-xl shadow-md">
            <i class="fa-regular fa-heart text-6xl text-gray-300 mb-4"></i>
            <h3 class="text-xl font-semibold text-gray-600">This wishlist is empty</h3>
            <p class="text-gray-500 mt-2">Start adding rooms you love!</p>
            <a href="{{ route('froom.all') }}" class="mt-6 inline-block px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                Browse Rooms
            </a>
        </div>
        @endif
    </div>
</div>

<!-- Share Modal -->
<div id="shareModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-xl p-6 w-full max-w-md mx-4">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold">Share Wishlist</h3>
            <button onclick="closeShareModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fa-solid fa-times"></i>
            </button>
        </div>
        <form action="{{ route('wishlists.share', $wishlist->id) }}" method="POST">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                    <input type="email" name="email" required placeholder="friend@example.com"
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Message (Optional)</label>
                    <textarea name="message" rows="3" placeholder="Check out these amazing rooms!"
                              class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"></textarea>
                </div>
            </div>
            <div class="mt-6 flex gap-3">
                <button type="button" onclick="closeShareModal()" class="flex-1 px-4 py-2 border rounded-lg hover:bg-gray-50">
                    Cancel
                </button>
                <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    <i class="fa-solid fa-paper-plane mr-2"></i> Send
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function shareWishlist() {
    document.getElementById('shareModal').classList.remove('hidden');
    document.getElementById('shareModal').classList.add('flex');
}

function closeShareModal() {
    document.getElementById('shareModal').classList.add('hidden');
    document.getElementById('shareModal').classList.remove('flex');
}

function removeFromWishlist(itemId, roomId) {
    if (!confirm('Remove this room from wishlist?')) return;
    
    fetch('{{ route('wishlist.remove') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            room_id: roomId,
            wishlist_id: {{ $wishlist->id }}
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            document.getElementById('item-' + itemId).remove();
            showToast('Removed from wishlist', 'success');
        }
    });
}

function moveToWishlist(itemId, toWishlistId) {
    if (!toWishlistId) return;
    
    fetch('{{ route('wishlist.move') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            item_id: itemId,
            to_wishlist_id: toWishlistId
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            document.getElementById('item-' + itemId).remove();
            showToast(data.message, 'success');
        }
    });
}

function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `fixed bottom-4 right-4 px-6 py-3 rounded-lg shadow-lg z-50 ${type === 'success' ? 'bg-green-600' : 'bg-red-600'} text-white`;
    toast.textContent = message;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 3000);
}
</script>
@endsection
