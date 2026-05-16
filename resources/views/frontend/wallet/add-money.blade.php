@extends('frontend.dashboard.account_master')

@section('account_title', __('frontend.account.title_add_money'))

@section('account_content')
<div class="w-full max-w-2xl mx-auto">
    <h2 class="text-2xl font-bold text-slate-900 mb-2">Add money to wallet</h2>
    <p class="text-slate-600 mb-6">Current balance: <strong>{{ $wallet->formatted_balance }}</strong></p>

    <div class="bg-white rounded-xl shadow-md p-6 border border-slate-200">
        <form method="post" action="{{ route('wallet.add-money.process') }}">
            @csrf
            <div class="mb-4">
                <label class="form-label" for="amount">Amount (₹)</label>
                <input type="number" name="amount" id="amount" class="form-control" min="100" max="100000" step="1" value="500" required>
            </div>
            <div class="mb-4">
                <label class="form-label" for="payment_method">Payment method</label>
                <select name="payment_method" id="payment_method" class="form-control" required>
                    @forelse($paymentMethods as $method)
                        <option value="{{ $method->id }}">{{ $method->name }}</option>
                    @empty
                        <option value="" disabled selected>No payment methods configured</option>
                    @endforelse
                </select>
            </div>
            <button type="submit" class="btn btn-primary w-100" @if($paymentMethods->isEmpty()) disabled @endif>
                Continue to pay
            </button>
        </form>
        <p class="small text-muted mt-3 mb-0">You will be redirected to the selected gateway to complete the top-up.</p>
    </div>
</div>
@endsection
