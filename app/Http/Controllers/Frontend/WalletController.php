<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Models\PaymentMethod;
use App\Models\PaymentTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WalletController extends Controller
{
    /**
     * Display wallet dashboard
     */
    public function index()
    {
        $wallet = Auth::user()->getOrCreateWallet();
        
        $transactions = $wallet->transactions()
            ->latest()
            ->paginate(15);
        
        $summary = [
            'total_credited' => $wallet->transactions()->credits()->sum('amount'),
            'total_debited' => $wallet->transactions()->debits()->sum('amount'),
            'this_month_credited' => $wallet->transactions()
                ->credits()
                ->whereMonth('created_at', now()->month)
                ->sum('amount'),
            'pending_cashback' => $wallet->available_cashback,
        ];
        
        return view('frontend.wallet.index', compact('wallet', 'transactions', 'summary'));
    }

    /**
     * Show add money form
     */
    public function showAddMoney()
    {
        $wallet = Auth::user()->getOrCreateWallet();
        
        $paymentMethods = PaymentMethod::active()
            ->where('slug', '!=', 'wallet')
            ->where('slug', '!=', 'pay_at_hotel')
            ->ordered()
            ->get();
        
        return view('frontend.wallet.add-money', compact('wallet', 'paymentMethods'));
    }

    /**
     * Process add money
     */
    public function addMoney(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:100|max:100000',
            'payment_method' => 'required|exists:payment_methods,id',
        ]);
        
        $wallet = Auth::user()->getOrCreateWallet();
        $paymentMethod = PaymentMethod::findOrFail($validated['payment_method']);
        
        // Create pending payment transaction
        $transaction = PaymentTransaction::create([
            'user_id' => Auth::id(),
            'payment_method_id' => $paymentMethod->id,
            'amount' => $validated['amount'],
            'fee' => $paymentMethod->calculateFee($validated['amount']),
            'currency' => 'INR',
            'status' => 'pending',
            'type' => 'wallet_deposit',
            'description' => 'Wallet top-up',
        ]);
        
        // Redirect to payment gateway based on method
        switch ($paymentMethod->provider) {
            case 'stripe':
                return $this->processStripePayment($transaction);
            case 'razorpay':
                return $this->processRazorpayPayment($transaction);
            default:
                return back()->with('error', 'Payment method not supported');
        }
    }

    /**
     * Process Stripe payment
     */
    protected function processStripePayment($transaction)
    {
        try {
            \Stripe\Stripe::setApiKey(config('services.stripe.secret'));
            
            $session = \Stripe\Checkout\Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'inr',
                        'product_data' => [
                            'name' => 'Wallet Top-up',
                        ],
                        'unit_amount' => $transaction->amount * 100,
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => route('wallet.payment.success', ['transaction' => $transaction->id]),
                'cancel_url' => route('wallet.payment.cancel', ['transaction' => $transaction->id]),
                'metadata' => [
                    'transaction_id' => $transaction->id,
                ],
            ]);
            
            $transaction->update(['provider_reference' => $session->id]);
            
            return redirect($session->url);
        } catch (\Exception $e) {
            $transaction->markAsFailed($e->getMessage());
            return back()->with('error', 'Payment failed: ' . $e->getMessage());
        }
    }

    /**
     * Process Razorpay payment
     */
    protected function processRazorpayPayment($transaction)
    {
        // Return view with Razorpay checkout options
        return view('frontend.wallet.razorpay-checkout', [
            'transaction' => $transaction,
            'key' => config('services.razorpay.key'),
            'amount' => $transaction->amount * 100,
            'user' => Auth::user(),
        ]);
    }

    /**
     * Handle Razorpay callback
     */
    public function razorpayCallback(Request $request)
    {
        $transaction = PaymentTransaction::findOrFail($request->transaction_id);
        
        try {
            // Verify payment signature
            $api = new \Razorpay\Api\Api(
                config('services.razorpay.key'),
                config('services.razorpay.secret')
            );
            
            $attributes = [
                'razorpay_payment_id' => $request->razorpay_payment_id,
                'razorpay_order_id' => $request->razorpay_order_id,
                'razorpay_signature' => $request->razorpay_signature,
            ];
            
            $api->utility->verifyPaymentSignature($attributes);
            
            // Payment verified, credit wallet
            $this->creditWallet($transaction, $request->razorpay_payment_id);
            
            return redirect()->route('wallet.index')
                ->with('success', 'Wallet topped up successfully!');
        } catch (\Exception $e) {
            $transaction->markAsFailed($e->getMessage());
            return redirect()->route('wallet.index')
                ->with('error', 'Payment verification failed');
        }
    }

    /**
     * Payment success callback
     */
    public function paymentSuccess(Request $request, $transactionId)
    {
        $transaction = PaymentTransaction::findOrFail($transactionId);
        
        if ($transaction->status === 'completed') {
            return redirect()->route('wallet.index')
                ->with('success', 'Wallet topped up successfully!');
        }
        
        // Verify with Stripe
        try {
            \Stripe\Stripe::setApiKey(config('services.stripe.secret'));
            
            $session = \Stripe\Checkout\Session::retrieve($transaction->provider_reference);
            
            if ($session->payment_status === 'paid') {
                $this->creditWallet($transaction, $session->payment_intent);
                return redirect()->route('wallet.index')
                    ->with('success', 'Wallet topped up successfully!');
            }
        } catch (\Exception $e) {
            // Log error
        }
        
        return redirect()->route('wallet.index')
            ->with('error', 'Payment verification pending');
    }

    /**
     * Payment cancel callback
     */
    public function paymentCancel($transactionId)
    {
        $transaction = PaymentTransaction::findOrFail($transactionId);
        $transaction->update(['status' => 'cancelled']);
        
        return redirect()->route('wallet.add-money')
            ->with('info', 'Payment was cancelled');
    }

    /**
     * Credit wallet after successful payment
     */
    protected function creditWallet($paymentTransaction, $providerReference)
    {
        $paymentTransaction->markAsCompleted($providerReference);
        
        $wallet = Auth::user()->getOrCreateWallet();
        $wallet->credit(
            $paymentTransaction->amount,
            'deposit',
            'Wallet top-up via ' . $paymentTransaction->paymentMethod->name,
            null,
            null,
            ['payment_transaction_id' => $paymentTransaction->id]
        );
    }

    /**
     * Get transactions for AJAX loading
     */
    public function getTransactions(Request $request)
    {
        $wallet = Auth::user()->wallet;
        
        if (!$wallet) {
            return response()->json(['transactions' => []]);
        }
        
        $query = $wallet->transactions();
        
        // Filter by type
        if ($request->type === 'credits') {
            $query->credits();
        } elseif ($request->type === 'debits') {
            $query->debits();
        }
        
        // Filter by transaction type
        if ($request->transaction_type) {
            $query->ofType($request->transaction_type);
        }
        
        // Date range
        if ($request->from) {
            $query->whereDate('created_at', '>=', $request->from);
        }
        if ($request->to) {
            $query->whereDate('created_at', '<=', $request->to);
        }
        
        $transactions = $query->latest()->paginate(15);
        
        return response()->json([
            'transactions' => $transactions->items(),
            'hasMore' => $transactions->hasMorePages(),
        ]);
    }

    /**
     * Download statement
     */
    public function downloadStatement(Request $request)
    {
        $validated = $request->validate([
            'from' => 'required|date',
            'to' => 'required|date|after_or_equal:from',
        ]);
        
        $wallet = Auth::user()->wallet;
        
        if (!$wallet) {
            return back()->with('error', 'No wallet found');
        }
        
        $transactions = $wallet->transactions()
            ->whereDate('created_at', '>=', $validated['from'])
            ->whereDate('created_at', '<=', $validated['to'])
            ->orderBy('created_at')
            ->get();
        
        $pdf = \PDF::loadView('frontend.wallet.statement', [
            'wallet' => $wallet,
            'transactions' => $transactions,
            'from' => $validated['from'],
            'to' => $validated['to'],
            'user' => Auth::user(),
        ]);
        
        return $pdf->download('wallet-statement-' . now()->format('Y-m-d') . '.pdf');
    }
}
