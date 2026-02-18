@extends('frontend.main_master')

@section('main')
<div class="container mx-auto px-4 py-8">
    <!-- Wallet Header -->
    <div class="bg-gradient-to-r from-green-600 to-teal-600 rounded-2xl p-8 text-white mb-8">
        <div class="flex flex-wrap items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold mb-2">My Wallet</h1>
                <p class="text-green-100">Manage your wallet balance and transactions</p>
            </div>
            <div class="text-right mt-4 lg:mt-0">
                <div class="text-4xl font-bold">{{ $wallet->formatted_balance }}</div>
                <div class="text-green-100">Available Balance</div>
            </div>
        </div>
        
        <div class="mt-6 flex flex-wrap gap-4">
            <a href="{{ route('wallet.add-money') }}" class="px-6 py-3 bg-white text-green-600 rounded-lg font-semibold hover:bg-green-50 transition">
                <i class="fa-solid fa-plus mr-2"></i> Add Money
            </a>
            <button onclick="openStatementModal()" class="px-6 py-3 bg-white/20 text-white rounded-lg font-semibold hover:bg-white/30 transition">
                <i class="fa-solid fa-download mr-2"></i> Download Statement
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Left Column - Stats -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Summary Cards -->
            <div class="bg-white rounded-xl shadow-md p-6">
                <h3 class="text-lg font-semibold mb-4">Summary</h3>
                <div class="space-y-4">
                    <div class="flex justify-between items-center p-3 bg-green-50 rounded-lg">
                        <div class="flex items-center">
                            <i class="fa-solid fa-arrow-down text-green-600 mr-3"></i>
                            <span>Total Credited</span>
                        </div>
                        <span class="font-bold text-green-600">₹{{ number_format($summary['total_credited'], 2) }}</span>
                    </div>
                    <div class="flex justify-between items-center p-3 bg-red-50 rounded-lg">
                        <div class="flex items-center">
                            <i class="fa-solid fa-arrow-up text-red-600 mr-3"></i>
                            <span>Total Debited</span>
                        </div>
                        <span class="font-bold text-red-600">₹{{ number_format($summary['total_debited'], 2) }}</span>
                    </div>
                    <div class="flex justify-between items-center p-3 bg-blue-50 rounded-lg">
                        <div class="flex items-center">
                            <i class="fa-solid fa-calendar text-blue-600 mr-3"></i>
                            <span>This Month</span>
                        </div>
                        <span class="font-bold text-blue-600">+₹{{ number_format($summary['this_month_credited'], 2) }}</span>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-xl shadow-md p-6">
                <h3 class="text-lg font-semibold mb-4">Quick Actions</h3>
                <div class="space-y-3">
                    <a href="{{ route('wallet.add-money') }}" class="flex items-center p-3 rounded-lg border hover:bg-gray-50 transition">
                        <i class="fa-solid fa-credit-card text-blue-500 mr-3"></i>
                        <span>Add Money via Card</span>
                    </a>
                    <a href="{{ route('wallet.add-money') }}" class="flex items-center p-3 rounded-lg border hover:bg-gray-50 transition">
                        <i class="fa-solid fa-mobile-screen text-green-500 mr-3"></i>
                        <span>Add via UPI</span>
                    </a>
                    <a href="{{ route('froom.all') }}" class="flex items-center p-3 rounded-lg border hover:bg-gray-50 transition">
                        <i class="fa-solid fa-bed text-purple-500 mr-3"></i>
                        <span>Book with Wallet</span>
                    </a>
                </div>
            </div>

            <!-- Benefits -->
            <div class="bg-white rounded-xl shadow-md p-6">
                <h3 class="text-lg font-semibold mb-4">Wallet Benefits</h3>
                <ul class="space-y-3 text-sm">
                    <li class="flex items-start">
                        <i class="fa-solid fa-check-circle text-green-500 mr-3 mt-0.5"></i>
                        <span>Instant refunds credited to wallet</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fa-solid fa-check-circle text-green-500 mr-3 mt-0.5"></i>
                        <span>Earn extra cashback on wallet payments</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fa-solid fa-check-circle text-green-500 mr-3 mt-0.5"></i>
                        <span>No transaction fees on wallet payments</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fa-solid fa-check-circle text-green-500 mr-3 mt-0.5"></i>
                        <span>Faster checkout experience</span>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Right Column - Transactions -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-md p-6">
                <div class="flex flex-wrap justify-between items-center mb-6">
                    <h3 class="text-lg font-semibold">Transaction History</h3>
                    <div class="flex gap-2 mt-2 sm:mt-0">
                        <button onclick="filterTransactions('all')" class="filter-btn active px-3 py-1 rounded-full text-sm bg-blue-100 text-blue-600">All</button>
                        <button onclick="filterTransactions('credits')" class="filter-btn px-3 py-1 rounded-full text-sm bg-gray-100 text-gray-600 hover:bg-green-100 hover:text-green-600">Credits</button>
                        <button onclick="filterTransactions('debits')" class="filter-btn px-3 py-1 rounded-full text-sm bg-gray-100 text-gray-600 hover:bg-red-100 hover:text-red-600">Debits</button>
                    </div>
                </div>

                <div class="space-y-3" id="transactionsList">
                    @forelse($transactions as $transaction)
                    <div class="flex items-center justify-between p-4 border rounded-lg hover:bg-gray-50 transition">
                        <div class="flex items-center">
                            <div class="w-12 h-12 rounded-full flex items-center justify-center {{ $transaction->isCredit() ? 'bg-green-100' : 'bg-red-100' }}">
                                <i class="fa-solid {{ $transaction->type_icon }} {{ $transaction->isCredit() ? 'text-green-600' : 'text-red-600' }}"></i>
                            </div>
                            <div class="ml-4">
                                <div class="font-medium">{{ $transaction->transaction_type_display }}</div>
                                <div class="text-sm text-gray-500">{{ $transaction->description }}</div>
                                <div class="text-xs text-gray-400">{{ $transaction->created_at->format('M d, Y h:i A') }}</div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-lg font-bold {{ $transaction->isCredit() ? 'text-green-600' : 'text-red-600' }}">
                                {{ $transaction->formatted_amount }}
                            </div>
                            <div class="text-xs text-gray-400">Balance: ₹{{ number_format($transaction->balance_after, 2) }}</div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-12 text-gray-500">
                        <i class="fa-solid fa-wallet text-5xl mb-4"></i>
                        <p>No transactions yet</p>
                        <a href="{{ route('wallet.add-money') }}" class="mt-4 inline-block px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                            Add Money Now
                        </a>
                    </div>
                    @endforelse
                </div>

                @if($transactions->hasPages())
                <div class="mt-6">
                    {{ $transactions->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Statement Download Modal -->
<div id="statementModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-xl p-6 w-full max-w-md mx-4">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold">Download Statement</h3>
            <button onclick="closeStatementModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fa-solid fa-times"></i>
            </button>
        </div>
        <form action="{{ route('wallet.statement') }}" method="POST">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">From Date</label>
                    <input type="date" name="from" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-green-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">To Date</label>
                    <input type="date" name="to" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-green-500">
                </div>
            </div>
            <div class="mt-6 flex gap-3">
                <button type="button" onclick="closeStatementModal()" class="flex-1 px-4 py-2 border rounded-lg hover:bg-gray-50">
                    Cancel
                </button>
                <button type="submit" class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    Download PDF
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openStatementModal() {
    document.getElementById('statementModal').classList.remove('hidden');
    document.getElementById('statementModal').classList.add('flex');
}

function closeStatementModal() {
    document.getElementById('statementModal').classList.add('hidden');
    document.getElementById('statementModal').classList.remove('flex');
}

function filterTransactions(type) {
    // Update active button
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.classList.remove('active', 'bg-blue-100', 'text-blue-600');
        btn.classList.add('bg-gray-100', 'text-gray-600');
    });
    event.target.classList.add('active', 'bg-blue-100', 'text-blue-600');
    event.target.classList.remove('bg-gray-100', 'text-gray-600');
    
    // AJAX call to filter transactions
    fetch(`{{ route('wallet.transactions') }}?type=${type}`)
        .then(res => res.json())
        .then(data => {
            // Update transactions list
            console.log(data);
        });
}
</script>
@endsection
