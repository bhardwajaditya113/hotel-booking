@extends('frontend.main_master')

@section('main')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold">My Wishlists</h1>
            <p class="text-gray-600 mt-1">Save your favorite rooms and plan your trips</p>
        </div>
        <button onclick="showCreateWishlistModal()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
            <i class="fa-solid fa-plus mr-2"></i> Create Wishlist
        </button>
    </div>

    @if($wishlists->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($wishlists as $wishlist)
                <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition group">
                    <!-- Cover Image Grid -->
                    <a href="{{ route('wishlist.show', $wishlist->id) }}" class="block">
                        <div class="h-48 bg-gray-200 relative">
                            @if($wishlist->items->count() >= 4)
                                <div class="grid grid-cols-2 h-full">
                                    @foreach($wishlist->items->take(4) as $item)
                                        <div class="relative overflow-hidden">
                                            <img src="{{ asset($item->room->image ?? 'frontend/img/placeholder.jpg') }}" 
                                                 alt="{{ $item->room->room_name }}" 
                                                 class="w-full h-full object-cover">
                                        </div>
                                    @endforeach
                                </div>
                            @elseif($wishlist->items->count() > 0)
                                <img src="{{ asset($wishlist->items->first()->room->image ?? 'frontend/img/placeholder.jpg') }}" 
                                     alt="Wishlist Cover" 
                                     class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-gray-100 to-gray-200">
                                    <i class="fa-solid fa-heart text-6xl text-gray-300"></i>
                                </div>
                            @endif
                            
                            <!-- Overlay Actions -->
                            <div class="absolute top-3 right-3 opacity-0 group-hover:opacity-100 transition-opacity flex gap-2">
                                <button onclick="event.preventDefault(); shareWishlist({{ $wishlist->id }})" 
                                        class="w-8 h-8 bg-white rounded-full shadow flex items-center justify-center hover:bg-gray-100">
                                    <i class="fa-solid fa-share-nodes text-gray-700"></i>
                                </button>
                                <button onclick="event.preventDefault(); editWishlist({{ $wishlist->id }}, '{{ $wishlist->name }}', '{{ $wishlist->is_public ? '1' : '0' }}')" 
                                        class="w-8 h-8 bg-white rounded-full shadow flex items-center justify-center hover:bg-gray-100">
                                    <i class="fa-solid fa-pen text-gray-700"></i>
                                </button>
                            </div>

                            <!-- Privacy Badge -->
                            @if($wishlist->is_public)
                                <span class="absolute top-3 left-3 px-2 py-1 bg-green-500 text-white text-xs rounded-full">
                                    <i class="fa-solid fa-globe mr-1"></i> Public
                                </span>
                            @else
                                <span class="absolute top-3 left-3 px-2 py-1 bg-gray-700 text-white text-xs rounded-full">
                                    <i class="fa-solid fa-lock mr-1"></i> Private
                                </span>
                            @endif
                        </div>
                    </a>

                    <div class="p-4">
                        <a href="{{ route('wishlist.show', $wishlist->id) }}" class="block">
                            <h3 class="font-semibold text-lg hover:text-blue-600">{{ $wishlist->name }}</h3>
                        </a>
                        <p class="text-gray-500 text-sm mt-1">{{ $wishlist->items->count() }} saved • Updated {{ $wishlist->updated_at->diffForHumans() }}</p>
                        
                        @if($wishlist->items->count() > 0)
                            <div class="mt-3 flex items-center gap-2 text-sm text-gray-600">
                                <span class="font-medium">
                                    ₹{{ number_format($wishlist->items->min(fn($item) => $item->room->price ?? 0)) }}
                                </span>
                                <span>-</span>
                                <span class="font-medium">
                                    ₹{{ number_format($wishlist->items->max(fn($item) => $item->room->price ?? 0)) }}
                                </span>
                                <span class="text-gray-400">/ night</span>
                            </div>
                        @endif

                        <div class="mt-4 pt-4 border-t flex items-center justify-between">
                            @if($wishlist->shares->count() > 0)
                                <span class="text-sm text-gray-500">
                                    <i class="fa-solid fa-users mr-1"></i> Shared with {{ $wishlist->shares->count() }}
                                </span>
                            @else
                                <span class="text-sm text-gray-400">Not shared</span>
                            @endif
                            <button onclick="deleteWishlist({{ $wishlist->id }})" 
                                    class="text-red-500 hover:text-red-700 text-sm">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-8">
            {{ $wishlists->links() }}
        </div>
    @else
        <!-- Empty State -->
        <div class="text-center py-16">
            <div class="w-24 h-24 mx-auto bg-gray-100 rounded-full flex items-center justify-center mb-6">
                <i class="fa-solid fa-heart text-4xl text-gray-400"></i>
            </div>
            <h2 class="text-2xl font-semibold mb-2">No wishlists yet</h2>
            <p class="text-gray-600 mb-6">Create a wishlist to save and organize your favorite rooms</p>
            <button onclick="showCreateWishlistModal()" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                <i class="fa-solid fa-plus mr-2"></i> Create Your First Wishlist
            </button>
        </div>
    @endif
</div>

<!-- Create/Edit Wishlist Modal -->
<div id="wishlistModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center">
    <div class="bg-white rounded-xl max-w-md w-full mx-4 p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-xl font-semibold" id="modalTitle">Create Wishlist</h3>
            <button onclick="closeWishlistModal()" class="text-gray-500 hover:text-gray-700">
                <i class="fa-solid fa-times"></i>
            </button>
        </div>
        
        <form id="wishlistForm" method="POST">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Wishlist Name</label>
                <input type="text" name="name" id="wishlistName" 
                       placeholder="e.g., Summer Vacation, Weekend Getaways"
                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500" required>
            </div>

            <div class="mb-6">
                <label class="flex items-center">
                    <input type="checkbox" name="is_public" id="wishlistPublic" class="rounded text-blue-600">
                    <span class="ml-2">Make this wishlist public</span>
                </label>
                <p class="text-sm text-gray-500 mt-1 ml-6">Others can view but not edit</p>
            </div>

            <div class="flex gap-3">
                <button type="button" onclick="closeWishlistModal()" class="flex-1 px-4 py-2 border rounded-lg hover:bg-gray-50">
                    Cancel
                </button>
                <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    <i class="fa-solid fa-check mr-2"></i> <span id="submitText">Create</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Share Modal -->
<div id="shareModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center">
    <div class="bg-white rounded-xl max-w-md w-full mx-4 p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-xl font-semibold">Share Wishlist</h3>
            <button onclick="closeShareModal()" class="text-gray-500 hover:text-gray-700">
                <i class="fa-solid fa-times"></i>
            </button>
        </div>
        
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Share Link</label>
            <div class="flex">
                <input type="text" id="shareLink" readonly 
                       class="flex-1 px-4 py-2 border rounded-l-lg bg-gray-50">
                <button onclick="copyShareLink()" class="px-4 py-2 bg-blue-600 text-white rounded-r-lg hover:bg-blue-700">
                    <i class="fa-solid fa-copy"></i>
                </button>
            </div>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Or share via</label>
            <div class="flex gap-3">
                <button onclick="shareVia('whatsapp')" class="flex-1 px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600">
                    <i class="fa-brands fa-whatsapp mr-2"></i> WhatsApp
                </button>
                <button onclick="shareVia('email')" class="flex-1 px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                    <i class="fa-solid fa-envelope mr-2"></i> Email
                </button>
            </div>
        </div>

        <form id="shareEmailForm" method="POST" class="hidden pt-4 border-t">
            @csrf
            <input type="hidden" name="wishlist_id" id="shareWishlistId">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                <input type="email" name="email" placeholder="friend@example.com"
                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Permission</label>
                <select name="permission" class="w-full px-4 py-2 border rounded-lg">
                    <option value="view">View Only</option>
                    <option value="edit">Can Edit</option>
                </select>
            </div>
            <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                Send Invite
            </button>
        </form>
    </div>
</div>

@push('scripts')
<script>
let currentWishlistId = null;

function showCreateWishlistModal() {
    document.getElementById('modalTitle').textContent = 'Create Wishlist';
    document.getElementById('submitText').textContent = 'Create';
    document.getElementById('formMethod').value = 'POST';
    document.getElementById('wishlistForm').action = '{{ route("wishlist.store") }}';
    document.getElementById('wishlistName').value = '';
    document.getElementById('wishlistPublic').checked = false;
    document.getElementById('wishlistModal').classList.remove('hidden');
    document.getElementById('wishlistModal').classList.add('flex');
}

function editWishlist(id, name, isPublic) {
    document.getElementById('modalTitle').textContent = 'Edit Wishlist';
    document.getElementById('submitText').textContent = 'Update';
    document.getElementById('formMethod').value = 'PUT';
    document.getElementById('wishlistForm').action = `/wishlist/${id}`;
    document.getElementById('wishlistName').value = name;
    document.getElementById('wishlistPublic').checked = isPublic === '1';
    document.getElementById('wishlistModal').classList.remove('hidden');
    document.getElementById('wishlistModal').classList.add('flex');
}

function closeWishlistModal() {
    document.getElementById('wishlistModal').classList.add('hidden');
    document.getElementById('wishlistModal').classList.remove('flex');
}

function shareWishlist(id) {
    currentWishlistId = id;
    
    fetch(`/wishlist/${id}/share`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        document.getElementById('shareLink').value = data.share_url;
        document.getElementById('shareWishlistId').value = id;
        document.getElementById('shareModal').classList.remove('hidden');
        document.getElementById('shareModal').classList.add('flex');
    });
}

function closeShareModal() {
    document.getElementById('shareModal').classList.add('hidden');
    document.getElementById('shareModal').classList.remove('flex');
}

function copyShareLink() {
    const input = document.getElementById('shareLink');
    input.select();
    document.execCommand('copy');
    
    const btn = event.target.closest('button');
    btn.innerHTML = '<i class="fa-solid fa-check"></i>';
    setTimeout(() => {
        btn.innerHTML = '<i class="fa-solid fa-copy"></i>';
    }, 2000);
}

function shareVia(platform) {
    const link = document.getElementById('shareLink').value;
    const text = 'Check out my wishlist!';
    
    if (platform === 'whatsapp') {
        window.open(`https://wa.me/?text=${encodeURIComponent(text + ' ' + link)}`, '_blank');
    } else if (platform === 'email') {
        document.getElementById('shareEmailForm').classList.toggle('hidden');
    }
}

function deleteWishlist(id) {
    if (confirm('Are you sure you want to delete this wishlist?')) {
        fetch(`/wishlist/${id}`, {
            method: 'DELETE',
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
}
</script>
@endpush
@endsection
