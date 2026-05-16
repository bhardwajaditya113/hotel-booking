@extends('frontend.dashboard.account_master')

@section('account_breadcrumb')
    <li><a href="{{ route('loyalty.index') }}">Loyalty</a></li>
@endsection

@section('account_title', __('frontend.account.title_referrals'))

@section('account_content')
<div class="w-full space-y-8">
    <h2 class="text-2xl font-bold text-slate-900">Invite friends</h2>

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Total</p>
            <p class="mt-1 text-2xl font-bold text-slate-900">{{ $stats['total'] ?? 0 }}</p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Completed</p>
            <p class="mt-1 text-2xl font-bold text-emerald-700">{{ $stats['completed'] ?? 0 }}</p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Pending</p>
            <p class="mt-1 text-2xl font-bold text-amber-700">{{ $stats['pending'] ?? 0 }}</p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Points earned</p>
            <p class="mt-1 text-2xl font-bold text-teal-700">{{ number_format((int) ($stats['total_earned'] ?? 0)) }}</p>
        </div>
    </div>

    <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-md">
        <p class="text-sm font-medium text-slate-700">Your referral code</p>
        <p class="mt-2 font-mono text-lg font-semibold tracking-wide text-slate-900">{{ $referralCode }}</p>
        <p class="mt-4 text-sm text-slate-600">Share this link — when someone registers with it, they are linked to your account.</p>
        <div class="mt-3 flex flex-wrap items-center gap-2">
            <input type="text" readonly value="{{ $referralUrl }}"
                class="min-w-[16rem] flex-1 rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-800"
                onclick="this.select()">
            <button type="button"
                class="rounded-lg bg-teal-600 px-4 py-2 text-sm font-semibold text-white hover:bg-teal-700"
                onclick="navigator.clipboard.writeText(@json($referralUrl))">Copy link</button>
        </div>
    </div>

    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-md">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50 text-left">
                <tr>
                    <th class="px-4 py-3">Invited</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">Your points</th>
                    <th class="px-4 py-3">Started</th>
                </tr>
            </thead>
            <tbody>
                @forelse($referrals as $ref)
                    <tr class="border-t border-slate-100">
                        <td class="px-4 py-3">
                            @if($ref->referred)
                                <span class="font-medium text-slate-900">{{ $ref->referred->name }}</span>
                                <span class="block text-xs text-slate-500">{{ $ref->referred->email }}</span>
                            @elseif($ref->referred_email)
                                <span class="text-slate-700">{{ $ref->referred_email }}</span>
                            @else
                                <span class="text-slate-400">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 capitalize">{{ str_replace('_', ' ', $ref->status) }}</td>
                        <td class="px-4 py-3 font-medium text-slate-800">{{ (int) $ref->referrer_points }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $ref->created_at?->format('M j, Y') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-8 text-center text-slate-500">No referrals yet. Share your link to get started.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div>{{ $referrals->links() }}</div>

    <a href="{{ route('loyalty.index') }}" class="inline-block text-teal-700 hover:underline font-semibold">&larr; Back to loyalty</a>
</div>
@endsection
