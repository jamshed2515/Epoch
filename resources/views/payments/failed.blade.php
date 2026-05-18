@extends('layouts.app')
@section('title', 'Payment Failed')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-red-50 via-white to-orange-50 flex items-center justify-center px-4 py-10">
<div class="max-w-md w-full text-center">

    <div class="bg-white rounded-3xl shadow-xl border border-red-100 p-10">
        <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-red-100 mb-6">
            <i data-lucide="x-circle" class="w-10 h-10 text-red-500"></i>
        </div>
        <h1 class="text-2xl font-bold text-gray-900 mb-2">Payment Failed</h1>
        <p class="text-gray-500 text-sm mb-8">
            Your payment could not be processed. No amount has been deducted.<br>
            Please try again or use a different payment method.
        </p>

        @if(session('error'))
            <div class="bg-red-50 border border-red-200 rounded-xl px-4 py-3 mb-6 text-sm text-red-700">
                {{ session('error') }}
            </div>
        @endif

        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <a href="{{ route('professionals.index') }}"
               class="px-6 py-3 bg-indigo-600 text-white font-semibold rounded-xl hover:bg-indigo-700 transition-colors">
                Try Again
            </a>
            <a href="{{ route('dashboard') }}"
               class="px-6 py-3 bg-gray-100 text-gray-700 font-semibold rounded-xl hover:bg-gray-200 transition-colors">
                Go to Dashboard
            </a>
        </div>
    </div>
</div>
</div>
@endsection
