@extends('frontend.main_master')
@section('main')
<div class="container py-8">
    <div class="max-w-4xl mx-auto bg-white rounded-xl shadow-md p-8">
        <div class="flex items-center justify-between mb-6 pb-4 border-b">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-gray-300 rounded-full flex items-center justify-center">
                    <span class="text-gray-600 font-semibold">
                        {{ substr($otherUser->name ?? 'User', 0, 1) }}
                    </span>
                </div>
                <div>
                    <h2 class="text-xl font-bold">{{ $otherUser->name ?? 'User' }}</h2>
                    @if($conversation->property)
                    <p class="text-sm text-gray-600">{{ $conversation->property->name }}</p>
                    @endif
                </div>
            </div>
            <a href="{{ route('messages.index') }}" class="text-blue-600 hover:underline">← Back to Messages</a>
        </div>

        @if(session('success'))
        <div class="mb-4 p-4 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
        @endif

        <!-- Messages Container -->
        <div class="h-96 overflow-y-auto border rounded-lg p-4 mb-4 bg-gray-50" id="messages-container">
            @forelse($messages as $message)
            <div class="mb-4 {{ $message->sender_id === auth()->id() ? 'text-right' : 'text-left' }}">
                <div class="inline-block max-w-xs lg:max-w-md p-3 rounded-lg 
                    {{ $message->sender_id === auth()->id() ? 'bg-blue-600 text-white' : 'bg-white border' }}">
                    <p class="text-sm">{{ $message->message }}</p>
                    <p class="text-xs mt-1 opacity-75">
                        {{ $message->created_at->format('M d, Y h:i A') }}
                    </p>
                </div>
            </div>
            @empty
            <div class="text-center py-12 text-gray-500">
                <p>No messages yet. Start the conversation!</p>
            </div>
            @endforelse
        </div>

        <!-- Message Form -->
        <form action="{{ route('messages.send', $conversation->id) }}" method="POST" id="message-form">
            @csrf
            <div class="flex gap-2">
                <textarea name="message" 
                    rows="2" 
                    class="flex-1 border rounded-lg px-4 py-2 resize-none" 
                    placeholder="Type your message..."
                    required></textarea>
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                    Send
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Auto-scroll to bottom on load
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('messages-container');
    if (container) {
        container.scrollTop = container.scrollHeight;
    }
});

// Auto-refresh messages (optional - can be enhanced with WebSockets)
// setInterval(function() {
//     location.reload();
// }, 30000); // Refresh every 30 seconds
</script>
@endsection


