@extends('frontend.main_master')
@section('main')
<div class="container py-8">
    <div class="max-w-4xl mx-auto bg-white rounded-xl shadow-md p-8">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold">Messages</h2>
            <div class="flex gap-2">
                <a href="{{ route('messages.index', ['archived' => request('archived') ? 0 : 1]) }}" 
                   class="px-4 py-2 rounded {{ request('archived') ? 'bg-gray-200' : 'bg-blue-600 text-white' }}">
                    {{ request('archived') ? 'Show Active' : 'Show Archived' }}
                </a>
            </div>
        </div>

        @if(session('success'))
        <div class="mb-4 p-4 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
        @endif

        <div class="space-y-4">
            @forelse($conversations as $conversation)
            <a href="{{ route('messages.show', $conversation->id) }}" 
               class="block p-4 border rounded-lg hover:bg-gray-50 transition">
                <div class="flex justify-between items-start">
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-12 h-12 bg-gray-300 rounded-full flex items-center justify-center">
                                <span class="text-gray-600 font-semibold">
                                    {{ substr($conversation->other_user->name ?? 'User', 0, 1) }}
                                </span>
                            </div>
                            <div>
                                <h3 class="font-semibold">{{ $conversation->other_user->name ?? 'User' }}</h3>
                                @if($conversation->property)
                                <p class="text-sm text-gray-600">{{ $conversation->property->name }}</p>
                                @endif
                            </div>
                        </div>
                        @if($conversation->latestMessage)
                        <p class="text-gray-600 text-sm mt-2 line-clamp-2">
                            {{ $conversation->latestMessage->message }}
                        </p>
                        @endif
                    </div>
                    <div class="text-right">
                        @if($conversation->latestMessage)
                        <p class="text-sm text-gray-500">
                            {{ $conversation->latestMessage->created_at->diffForHumans() }}
                        </p>
                        @endif
                        @if($conversation->unread_count > 0)
                        <span class="inline-block mt-2 px-2 py-1 bg-blue-600 text-white text-xs rounded-full">
                            {{ $conversation->unread_count }}
                        </span>
                        @endif
                    </div>
                </div>
            </a>
            @empty
            <div class="text-center py-12 text-gray-500">
                <p class="text-lg">No conversations found.</p>
                <p class="text-sm mt-2">Start a conversation by messaging a host from a property page.</p>
            </div>
            @endforelse
        </div>

        @if($conversations->hasPages())
        <div class="mt-6">
            {{ $conversations->links() }}
        </div>
        @endif
    </div>
</div>
@endsection


