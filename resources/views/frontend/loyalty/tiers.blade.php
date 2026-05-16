@extends('frontend.dashboard.account_master')

@section('account_breadcrumb')
    <li><a href="{{ route('loyalty.index') }}">Loyalty</a></li>
@endsection

@section('account_title', __('frontend.account.title_tiers'))

@section('account_content')
<div class="w-full">
    <h2 class="text-3xl font-bold text-slate-900 mb-2">Loyalty tiers</h2>
    <p class="text-slate-600 mb-8">Earn points on stays and move up for better perks.</p>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @foreach($tiers as $tier)
            <div class="bg-white rounded-xl shadow-md p-6 border border-slate-100">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-12 h-12 rounded-full flex items-center justify-center text-xl"
                         style="background: {{ $tier->color ?? '#64748b' }}22; color: {{ $tier->color ?? '#64748b' }}">
                        <i class="{{ $tier->icon ?? 'fa-solid fa-medal' }}"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-semibold text-slate-900">{{ $tier->name }}</h3>
                        <p class="text-sm text-slate-500">From {{ number_format($tier->min_points) }} points</p>
                    </div>
                </div>
                @if($userLoyalty->loyalty_tier_id === $tier->id)
                    <span class="inline-block text-xs font-semibold px-2 py-1 rounded bg-teal-100 text-teal-800 mb-2">Your tier</span>
                @endif
                <ul class="text-sm text-slate-600 space-y-2 mt-2">
                    @foreach(($tier->benefits ?? []) as $line)
                        <li class="flex gap-2"><i class="fa-solid fa-check text-green-600 mt-0.5"></i><span>{{ $line }}</span></li>
                    @endforeach
                </ul>
            </div>
        @endforeach
    </div>

    <div class="mt-8">
        <a href="{{ route('loyalty.index') }}" class="text-teal-700 hover:underline font-semibold">&larr; Back to loyalty home</a>
    </div>
</div>
@endsection
