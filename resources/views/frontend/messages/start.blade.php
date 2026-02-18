@extends('frontend.main_master')
@section('main')

<div class="container py-8">
    <div class="max-w-4xl mx-auto bg-white rounded-xl shadow-md p-8">
        <div class="mb-6">
            <h2 class="text-2xl font-bold mb-2">Message {{ $receiver->name }}</h2>
            @if($property)
            <p class="text-gray-600">About: {{ $property->name }}</p>
            @endif
            <a href="{{ route('messages.index') }}" class="text-blue-600 hover:underline mt-2 inline-block">← Back to Messages</a>
        </div>

        @if(session('error'))
        <div class="mb-4 p-4 bg-red-100 text-red-800 rounded">{{ session('error') }}</div>
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
                <label class="block text-gray-700 font-semibold mb-2">Message</label>
                <textarea name="message" 
                    rows="6" 
                    class="w-full border rounded-lg px-4 py-2 resize-none focus:outline-none focus:ring-2 focus:ring-blue-500" 
                    placeholder="Type your message here..."
                    required>{{ old('message') }}</textarea>
                @error('message')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex gap-3">
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                    Send Message
                </button>
                <a href="{{ route('messages.index') }}" class="bg-gray-200 text-gray-800 px-6 py-2 rounded-lg hover:bg-gray-300">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

@endsection


