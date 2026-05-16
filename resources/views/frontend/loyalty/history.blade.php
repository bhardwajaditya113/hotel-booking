@extends('frontend.dashboard.account_master')

@section('account_breadcrumb')
    <li><a href="{{ route('loyalty.index') }}">Loyalty</a></li>
@endsection

@section('account_title', __('frontend.account.title_points_history'))

@section('account_content')
<div class="w-full">
    <h2 class="text-2xl font-bold text-slate-900 mb-6">Points history</h2>
    <div class="bg-white rounded-xl shadow-md overflow-hidden border border-slate-200">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50 text-left">
                <tr>
                    <th class="px-4 py-3">Date</th>
                    <th class="px-4 py-3">Type</th>
                    <th class="px-4 py-3">Points</th>
                    <th class="px-4 py-3">Description</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $tx)
                    <tr class="border-t border-slate-100">
                        <td class="px-4 py-3">{{ $tx->created_at?->format('M j, Y H:i') }}</td>
                        <td class="px-4 py-3">{{ $tx->type }}</td>
                        <td class="px-4 py-3 font-medium {{ $tx->points >= 0 ? 'text-green-700' : 'text-red-700' }}">{{ $tx->points >= 0 ? '+' : '' }}{{ $tx->points }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $tx->description }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-4 py-8 text-center text-slate-500">No transactions yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-6">{{ $transactions->links() }}</div>
    <a href="{{ route('loyalty.index') }}" class="inline-block mt-4 text-teal-700 hover:underline font-semibold">&larr; Back to loyalty</a>
</div>
@endsection
