@extends('layouts.app')

@section('title', 'Paystack Payment')

@section('content')
<div class="max-w-md mx-auto mt-8 bg-white shadow-lg rounded-lg p-6">
    <div class="text-center mb-6">
        <div class="mx-auto w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mb-4">
            <svg class="w-8 h-8 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
            </svg>
        </div>
        <h2 class="text-2xl font-bold text-gray-900">Complete Payment</h2>
        <p class="text-gray-600 mt-2">You will be redirected to Paystack to complete your payment</p>
    </div>

    <div class="bg-gray-50 rounded-lg p-4 mb-6">
        <div class="flex justify-between items-center mb-2">
            <span class="text-gray-600">Amount:</span>
            <span class="font-semibold text-lg">₦{{ number_format($payment->amount, 2) }}</span>
        </div>
        <div class="flex justify-between items-center mb-2">
            <span class="text-gray-600">Reference:</span>
            <span class="text-sm font-mono">{{ $payment->reference }}</span>
        </div>
        <div class="flex justify-between items-center">
            <span class="text-gray-600">Payment Method:</span>
            <span class="capitalize">{{ $payment->gateway }}</span>
        </div>
    </div>

    <!-- Simulated Payment Form -->
    <form action="{{ route('payment.callback') }}" method="POST" id="paymentForm">
        @csrf
        <input type="hidden" name="reference" value="{{ $payment->reference }}">
        <input type="hidden" name="status" value="success">

        <div class="mb-4">
            <p class="text-sm text-gray-600 mb-4">
                <strong>Note:</strong> This is a demo payment. In production, you would be redirected to the actual Paystack payment page.
            </p>

            <div class="space-y-3">
                <button type="button" onclick="simulatePayment('success')" class="w-full bg-green-600 text-white py-3 px-4 rounded-lg hover:bg-green-700 transition duration-200">
                    ✓ Simulate Successful Payment
                </button>

                <button type="button" onclick="simulatePayment('failed')" class="w-full bg-red-600 text-white py-3 px-4 rounded-lg hover:bg-red-700 transition duration-200">
                    ✗ Simulate Failed Payment
                </button>
            </div>
        </div>
    </form>

    <div class="text-center">
        <a href="{{ route('wallet') }}" class="text-blue-600 hover:text-blue-800 text-sm">← Back to Wallet</a>
    </div>
</div>

<script>
function simulatePayment(status) {
    const form = document.getElementById('paymentForm');
    form.querySelector('input[name="status"]').value = status;

    // Show loading state
    const buttons = form.querySelectorAll('button');
    buttons.forEach(btn => {
        btn.disabled = true;
        btn.innerHTML = 'Processing...';
    });

    // Simulate delay
    setTimeout(() => {
        form.submit();
    }, 2000);
}
</script>
@endsection
