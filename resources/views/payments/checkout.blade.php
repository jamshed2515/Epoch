@extends('layouts.app')
@section('title', 'Complete Payment')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-indigo-50 via-white to-purple-50 py-10 px-4">
<div class="max-w-2xl mx-auto">

    {{-- Header --}}
    <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-600 shadow-lg mb-4">
            <i data-lucide="shield-check" class="w-8 h-8 text-white"></i>
        </div>
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">
            {{ $demoMode ? 'Review & Confirm Booking' : 'Secure Payment' }}
        </h1>
        <p class="text-gray-500 mt-1 text-sm">
            {{ $demoMode ? 'Razorpay test mode — no real payment will be charged' : 'Your booking is one step away' }}
        </p>
    </div>

    {{-- Demo mode warning banner --}}
    @if($demoMode)
    <div class="bg-amber-50 border border-amber-200 rounded-2xl px-5 py-4 mb-6 flex items-start gap-3">
        <i data-lucide="alert-triangle" class="w-5 h-5 text-amber-500 flex-shrink-0 mt-0.5"></i>
        <div>
            <p class="text-sm font-semibold text-amber-800">Demo / Test Mode</p>
            <p class="text-xs text-amber-700 mt-0.5">
                Razorpay keys are not configured. Click <strong>"Confirm Demo Booking"</strong> to create a test appointment.
                To enable live payments, add your
                <a href="https://dashboard.razorpay.com/app/keys" target="_blank" class="underline">Razorpay API keys</a>
                to <code class="bg-amber-100 px-1 rounded">.env</code>.
            </p>
        </div>
    </div>
    @endif

    <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">

        {{-- Booking Summary Header --}}
        <div class="bg-gradient-to-r from-indigo-600 to-purple-700 px-6 py-5 text-white">
            <p class="text-indigo-200 text-xs uppercase font-semibold tracking-wider mb-3">Booking Summary</p>
            <div class="flex items-center gap-4">
                <img src="{{ $professional->photo_url }}" alt="{{ $professional->user->name }}"
                     class="w-14 h-14 rounded-xl object-cover ring-2 ring-white/30 flex-shrink-0">
                <div class="flex-1 min-w-0">
                    <p class="font-bold text-lg truncate">{{ $professional->user->name }}</p>
                    <p class="text-indigo-200 text-sm">{{ $professional->category->name }}</p>
                    <p class="text-indigo-200 text-xs mt-1 flex items-center gap-1">
                        <i data-lucide="map-pin" class="w-3 h-3"></i>
                        {{ $professional->location }}
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-3 gap-3 mt-4 bg-white/10 rounded-xl p-3">
                <div>
                    <p class="text-indigo-300 text-xs">Date</p>
                    <p class="text-white font-semibold text-sm">{{ \Carbon\Carbon::parse($appointmentDate)->format('d M Y') }}</p>
                </div>
                <div>
                    <p class="text-indigo-300 text-xs">Time</p>
                    <p class="text-white font-semibold text-sm">{{ $timeSlot }}</p>
                </div>
                <div>
                    <p class="text-indigo-300 text-xs">Fee</p>
                    <p class="text-white font-bold text-sm">₹{{ number_format($amount) }}</p>
                </div>
            </div>
        </div>

        {{-- Payment / Demo Section --}}
        <div class="p-6 space-y-4">

            {{-- Order ID --}}
            <div class="flex items-center justify-between bg-gray-50 rounded-xl px-4 py-3 border border-gray-100">
                <span class="text-sm text-gray-500">{{ $demoMode ? 'Reference ID' : 'Order ID' }}</span>
                <span class="text-xs font-mono text-gray-700 bg-gray-100 px-2 py-1 rounded truncate max-w-[180px]">
                    {{ $order->id }}
                </span>
            </div>

            {{-- Amount breakdown --}}
            <div class="border border-gray-100 rounded-xl overflow-hidden">
                <div class="flex items-center justify-between px-4 py-3">
                    <span class="text-sm text-gray-600">Consultation Fee</span>
                    <span class="text-sm font-semibold text-gray-800">₹{{ number_format($amount) }}</span>
                </div>
                <div class="flex items-center justify-between px-4 py-3 border-t border-gray-50">
                    <span class="text-sm text-gray-600">GST (18%)</span>
                    <span class="text-sm text-gray-500">Included</span>
                </div>
                <div class="flex items-center justify-between px-4 py-3 bg-indigo-50 border-t border-indigo-100">
                    <span class="font-bold text-gray-900">Total</span>
                    <span class="text-xl font-extrabold text-indigo-600">₹{{ number_format($amount) }}</span>
                </div>
            </div>

            @if($demoMode)
                {{-- ── DEMO MODE: simulate button ── --}}
                <form action="{{ route('payments.simulate') }}" method="POST">
                    @csrf
                    <button type="submit"
                            class="w-full py-4 bg-gradient-to-r from-amber-500 to-orange-500 text-white font-bold text-lg rounded-2xl hover:from-amber-600 hover:to-orange-600 shadow-lg hover:shadow-xl transition-all duration-200 flex items-center justify-center gap-3">
                        <i data-lucide="zap" class="w-5 h-5"></i>
                        Confirm Demo Booking (Test Mode)
                    </button>
                </form>

                <div class="bg-gray-50 border border-gray-100 rounded-xl px-4 py-3">
                    <p class="text-xs font-semibold text-gray-600 mb-2">To activate live Razorpay payments:</p>
                    <ol class="text-xs text-gray-500 space-y-1 list-decimal list-inside">
                        <li>Sign up at <a href="https://razorpay.com" target="_blank" class="text-indigo-600 underline">razorpay.com</a> (free)</li>
                        <li>Go to <strong>Settings → API Keys → Generate Test Key</strong></li>
                        <li>Copy <strong>Key ID</strong> and <strong>Key Secret</strong></li>
                        <li>Update <code class="bg-gray-100 px-1 rounded">.env</code>:
                            <pre class="mt-1 bg-gray-100 rounded p-2 text-xs overflow-x-auto">RAZORPAY_KEY_ID=rzp_test_xxxx
RAZORPAY_KEY_SECRET=xxxx</pre>
                        </li>
                        <li>Run <code class="bg-gray-100 px-1 rounded">php artisan config:clear</code></li>
                    </ol>
                </div>

            @else
                {{-- ── LIVE MODE: Razorpay JS button ── --}}
                <button id="rzp-button"
                        class="w-full py-4 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-bold text-lg rounded-2xl hover:from-indigo-700 hover:to-purple-700 shadow-lg hover:shadow-xl transition-all duration-200 flex items-center justify-center gap-3 group">
                    <i data-lucide="lock" class="w-5 h-5 group-hover:scale-110 transition-transform"></i>
                    Pay ₹{{ number_format($amount) }} Securely
                </button>

                <div class="flex items-center justify-center gap-6 pt-2">
                    <div class="flex items-center gap-1.5 text-xs text-gray-400">
                        <i data-lucide="shield" class="w-3.5 h-3.5 text-green-500"></i>
                        256-bit SSL
                    </div>
                    <div class="flex items-center gap-1.5 text-xs text-gray-400">
                        <i data-lucide="credit-card" class="w-3.5 h-3.5 text-blue-500"></i>
                        UPI / Cards / NetBanking
                    </div>
                    <div class="flex items-center gap-1.5 text-xs text-gray-400">
                        <i data-lucide="zap" class="w-3.5 h-3.5 text-amber-500"></i>
                        Instant Confirmation
                    </div>
                </div>

                <p class="text-center text-xs text-gray-400 flex items-center justify-center gap-1">
                    <i data-lucide="lock" class="w-3 h-3"></i>
                    Secured by <span class="font-semibold text-gray-500 ml-1">Razorpay</span>
                </p>
            @endif
        </div>
    </div>

    <p class="text-center text-xs text-gray-400 mt-4">
        <a href="{{ url()->previous() }}" class="hover:text-indigo-600 underline underline-offset-2">← Go back and change slot</a>
    </p>
</div>
</div>

{{-- Hidden form for Razorpay live callback --}}
@if(!$demoMode)
<form id="razorpay-form" action="{{ route('payments.success') }}" method="POST" class="hidden">
    @csrf
    <input type="hidden" name="razorpay_payment_id" id="razorpay_payment_id">
    <input type="hidden" name="razorpay_order_id" id="razorpay_order_id">
    <input type="hidden" name="razorpay_signature" id="razorpay_signature">
</form>
@endif
@endsection

@if(!$demoMode)
@push('scripts')
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
    var options = {
        key: "{{ $razorpayKey }}",
        amount: {{ $amountPaise }},
        currency: "INR",
        name: "AppointEase",
        description: "Appointment with {{ $professional->user->name }}",
        image: "https://ui-avatars.com/api/?name=AppointEase&background=6366f1&color=fff&size=128",
        order_id: "{{ $order->id }}",
        handler: function(response) {
            document.getElementById('razorpay_payment_id').value = response.razorpay_payment_id;
            document.getElementById('razorpay_order_id').value   = response.razorpay_order_id;
            document.getElementById('razorpay_signature').value  = response.razorpay_signature;
            document.getElementById('razorpay-form').submit();
        },
        prefill: {
            name:  "{{ $user->name }}",
            email: "{{ $user->email }}",
            contact: "{{ $user->phone ?? '' }}"
        },
        theme: { color: "#6366f1" },
        modal: {
            ondismiss: function() {
                var btn = document.getElementById('rzp-button');
                btn.disabled = false;
                btn.innerHTML = '<i data-lucide="lock" class="w-5 h-5"></i> Pay ₹{{ number_format($amount) }} Securely';
                lucide.createIcons();
            }
        }
    };

    var rzp = new Razorpay(options);

    document.getElementById('rzp-button').onclick = function(e) {
        this.disabled = true;
        this.textContent = 'Opening payment...';
        rzp.open();
        e.preventDefault();
    };
</script>
@endpush
@endif
