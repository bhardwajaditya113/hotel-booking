@extends('frontend.dashboard.account_master')

@section('account_breadcrumb')
    <li><a href="{{ route('messages.index') }}">{{ __('frontend.account.title_messages') }}</a></li>
@endsection

@section('account_title', __('frontend.account.title_new_message'))

@section('account_content')
<div class="w-full">
    <div class="max-w-4xl mx-auto bg-white rounded-xl shadow-md p-6 md:p-8">
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-slate-900 mb-2">Message {{ $receiver->name }}</h2>
            @if($property)
                <p class="text-slate-600">About: {{ $property->name }}</p>
            @endif
            <a href="{{ route('messages.index') }}" class="text-teal-700 hover:underline mt-2 inline-block text-sm font-semibold">← Back to Messages</a>
        </div>

        @if(session('error'))
            <div class="mb-4 p-4 bg-red-50 text-red-900 rounded-lg border border-red-100">{{ session('error') }}</div>
        @endif

        <form method="POST" action="{{ route('messages.start.post') }}">
            @csrf
            <input type="hidden" name="receiver_id" value="{{ $receiver->id }}">
            @if($property)
                <input type="hidden" name="property_id" value="{{ $property->id }}">
            @endif
            @if($booking)
                <input type="hidden" name="booking_id" value="{{ $booking->id }}">
            @endif

            <div class="mb-4">
                <label class="block text-slate-800 font-semibold mb-2">Message</label>
                <textarea name="message"
                    rows="6"
                    class="w-full border border-slate-300 rounded-lg px-4 py-2 resize-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500"
                    placeholder="Type your message here..."
                    required>{{ old('message') }}</textarea>
                @error('message')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex gap-3 flex-wrap">
                <button type="submit" class="bg-teal-600 text-white px-6 py-2 rounded-lg hover:bg-teal-700 font-semibold">
                    Send Message
                </button>
                <a href="{{ route('messages.index') }}" class="bg-slate-200 text-slate-800 px-6 py-2 rounded-lg hover:bg-slate-300 font-semibold inline-flex items-center">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
