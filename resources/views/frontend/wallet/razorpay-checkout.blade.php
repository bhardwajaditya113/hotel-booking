@extends('frontend.dashboard.account_master')

@section('account_title', 'Wallet top-up')

@section('account_content')
<div class="w-full max-w-2xl mx-auto">
    <div class="bg-white rounded-xl shadow-md p-6 border border-slate-200">
        <h2 class="text-2xl font-bold text-slate-900 mb-2">Wallet top-up</h2>
        <p class="text-slate-600 mb-4">Review the pending top-up before continuing.</p>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div class="rounded-lg bg-slate-50 p-4 border">
                <div class="text-sm text-slate-500">Transaction</div>
                <div class="font-semibold">{{ $transaction->transaction_id }}</div>
            </div>
            <div class="rounded-lg bg-slate-50 p-4 border">
                <div class="text-sm text-slate-500">Amount</div>
                <div class="font-semibold">₹{{ number_format($transaction->amount, 2) }}</div>
            </div>
        </div>

        <div class="flex flex-col gap-3">
            <form method="post" action="{{ route('wallet.test-topup') }}">
                @csrf
                <input type="hidden" name="transaction_id" value="{{ $transaction->id }}">
                <button type="submit" class="btn btn-success w-100">Test Wallet Top-up</button>
            </form>

            <a href="{{ route('wallet.index') }}" class="btn btn-outline-secondary w-100">Back to wallet</a>
        </div>

        <p class="small text-muted mt-3 mb-0">Development only: this bypass completes the top-up locally without contacting Razorpay.</p>
    </div>
</div>
@endsection
