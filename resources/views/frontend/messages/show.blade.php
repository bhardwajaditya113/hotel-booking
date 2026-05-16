@extends('frontend.dashboard.account_master')

@section('account_breadcrumb')
    <li><a href="{{ route('messages.index') }}">{{ __('frontend.account.title_messages') }}</a></li>
@endsection

@section('account_title')
    {{ $otherUser->name ?? 'Conversation' }}
@endsection

@section('account_content')
<div class="w-full">
    <div class="max-w-4xl mx-auto bg-white rounded-xl shadow-md p-6 md:p-8">
        <div class="flex items-center justify-between mb-6 pb-4 border-b border-slate-200">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-slate-200 rounded-full flex items-center justify-center">
                    <span class="text-slate-700 font-semibold">
                        {{ substr($otherUser->name ?? 'User', 0, 1) }}
                    </span>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-slate-900">{{ $otherUser->name ?? 'User' }}</h2>
                    @if($conversation->property)
                        <p class="text-sm text-slate-600">{{ $conversation->property->name }}</p>
                    @endif
                </div>
            </div>
            <a href="{{ route('messages.index') }}" class="text-teal-700 hover:underline text-sm font-semibold">← Back</a>
        </div>

        @if(session('success'))
            <div class="mb-4 p-4 bg-emerald-50 text-emerald-900 rounded-lg border border-emerald-100">{{ session('success') }}</div>
        @endif

        <div class="h-96 overflow-y-auto border border-slate-200 rounded-xl p-4 mb-4 bg-slate-50" id="messages-container">
            @forelse($messages as $message)
                <div class="mb-4 {{ $message->sender_id === auth()->id() ? 'text-right' : 'text-left' }}">
                    <div class="inline-block max-w-xs lg:max-w-md p-3 rounded-lg
                        {{ $message->sender_id === auth()->id() ? 'bg-teal-600 text-white' : 'bg-white border border-slate-200 text-slate-800' }}">
                        <p class="text-sm">{{ $message->message }}</p>
                        <p class="text-xs mt-1 opacity-80">
                            {{ $message->created_at->format('M d, Y h:i A') }}
                        </p>
                    </div>
                </div>
            @empty
                <div class="text-center py-12 text-slate-500">
                    <p>No messages yet. Start the conversation!</p>
                </div>
            @endforelse
        </div>

        <form action="{{ route('messages.send', $conversation->id) }}" method="POST" id="message-form">
            @csrf
            <div class="flex gap-2">
                <textarea name="message"
                    rows="2"
                    class="flex-1 border border-slate-300 rounded-lg px-4 py-2 resize-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500"
                    placeholder="Type your message..."
                    required></textarea>
                <button type="submit" class="bg-teal-600 text-white px-6 py-2 rounded-lg hover:bg-teal-700 font-semibold shrink-0">
                    Send
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var container = document.getElementById('messages-container');
    if (container) {
        container.scrollTop = container.scrollHeight;
    }
});
</script>
@endpush
