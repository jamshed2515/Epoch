@extends('layouts.app')
@section('title', 'Appointment Details')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="mb-6">
        <a href="{{ route('appointments.index') }}" class="flex items-center gap-2 text-sm text-gray-500 hover:text-indigo-600 transition-colors">
            <i data-lucide="arrow-left" class="w-4 h-4"></i> Back to Appointments
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <!-- Header -->
        <div class="bg-gradient-to-r from-indigo-600 to-purple-700 px-6 py-5 text-white">
            <div class="flex items-center justify-between">
                <h1 class="text-xl font-bold">Appointment #{{ $appointment->id }}</h1>
                <div class="flex items-center gap-2">
                    @if($appointment->payment_status === 'paid')
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-green-400/20 border border-green-300/30 flex items-center gap-1">
                            <i data-lucide="check-circle" class="w-3 h-3"></i> Paid
                        </span>
                    @endif
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-white/20 border border-white/30">
                        {{ ucfirst($appointment->status) }}
                    </span>
                </div>
            </div>
            <p class="text-indigo-200 text-sm mt-1">
                {{ $appointment->appointment_date->format('l, d F Y') }} · {{ $appointment->time_slot }}
            </p>
        </div>

        <div class="p-6 space-y-6">
            <!-- Professional Info -->
            <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-xl">
                <img src="{{ $appointment->professional->photo_url }}" alt="{{ $appointment->professional->user->name }}"
                     class="w-16 h-16 rounded-xl object-cover flex-shrink-0">
                <div>
                    <p class="font-bold text-gray-900 text-lg">{{ $appointment->professional->user->name }}</p>
                    <p class="text-sm text-indigo-600 font-medium">{{ $appointment->professional->category->name }}</p>
                    <p class="text-xs text-gray-500 flex items-center gap-1 mt-1">
                        <i data-lucide="map-pin" class="w-3 h-3"></i>
                        {{ $appointment->professional->location }}
                    </p>
                </div>
                <a href="{{ route('professionals.show', $appointment->professional) }}" class="ml-auto text-sm text-indigo-600 hover:text-indigo-700 font-medium">
                    View Profile
                </a>
            </div>

            <!-- Details Grid -->
            <div class="grid grid-cols-2 gap-4">
                @foreach([
                    ['icon' => 'calendar', 'label' => 'Date', 'value' => $appointment->appointment_date->format('D, d M Y')],
                    ['icon' => 'clock', 'label' => 'Time Slot', 'value' => $appointment->time_slot],
                    ['icon' => 'hourglass', 'label' => 'Duration', 'value' => $appointment->professional->session_duration . ' minutes'],
                    ['icon' => 'indian-rupee', 'label' => 'Fee', 'value' => '₹' . number_format($appointment->fee)],
                ] as $detail)
                    <div class="p-4 bg-gray-50 rounded-xl">
                        <div class="flex items-center gap-2 text-gray-500 text-xs mb-1">
                            <i data-lucide="{{ $detail['icon'] }}" class="w-3.5 h-3.5"></i>
                            {{ $detail['label'] }}
                        </div>
                        <p class="font-bold text-gray-900">{{ $detail['value'] }}</p>
                    </div>
                @endforeach
            </div>

            <!-- Status Info -->
            @if($appointment->isCancelled())
                <div class="bg-red-50 border border-red-200 rounded-xl p-4">
                    <p class="text-sm font-semibold text-red-700 flex items-center gap-2">
                        <i data-lucide="x-circle" class="w-4 h-4"></i>
                        Appointment Cancelled
                    </p>
                    @if($appointment->cancellation_reason)
                        <p class="text-sm text-red-600 mt-1">Reason: {{ $appointment->cancellation_reason }}</p>
                    @endif
                    @if($appointment->cancelled_at)
                        <p class="text-xs text-red-400 mt-1">{{ $appointment->cancelled_at->format('d M Y H:i') }}</p>
                    @endif
                </div>
            @elseif($appointment->isConfirmed())
                <div class="bg-green-50 border border-green-200 rounded-xl p-4">
                    <p class="text-sm font-semibold text-green-700 flex items-center gap-2">
                        <i data-lucide="check-circle" class="w-4 h-4"></i>
                        Appointment Confirmed!
                        @if($appointment->confirmed_at)
                            <span class="text-xs text-green-500 font-normal ml-auto">{{ $appointment->confirmed_at->format('d M Y') }}</span>
                        @endif
                    </p>
                </div>
            @endif

            <!-- Notes -->
            @if($appointment->notes)
                <div>
                    <h3 class="text-sm font-semibold text-gray-600 mb-2">Your Notes</h3>
                    <p class="text-sm text-gray-700 bg-gray-50 rounded-xl p-4 leading-relaxed">{{ $appointment->notes }}</p>
                </div>
            @endif

            <!-- Actions -->
            <div class="flex gap-3 pt-4 border-t border-gray-100">
                @if($appointment->canBeCancelledByUser())
                    <div x-data="{ confirming: false }" class="w-full sm:w-auto">
                        {{-- Step 1: Show cancel button --}}
                        <button type="button"
                                x-show="!confirming"
                                @click="confirming = true"
                                class="w-full sm:w-auto px-6 py-2.5 bg-red-50 text-red-700 font-semibold text-sm rounded-xl border border-red-200 hover:bg-red-100 transition-colors flex items-center gap-2">
                            <i data-lucide="x-circle" class="w-4 h-4"></i>
                            Cancel Appointment
                        </button>

                        {{-- Step 2: Inline confirm --}}
                        <div x-show="confirming" x-cloak
                             class="flex items-center gap-2 bg-red-50 border border-red-200 rounded-xl px-4 py-2.5">
                            <span class="text-sm text-red-700 font-medium">Sure you want to cancel?</span>
                            <form action="{{ route('appointments.cancel', $appointment) }}" method="POST" class="inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                        class="px-3 py-1 bg-red-600 text-white text-xs font-bold rounded-lg hover:bg-red-700 transition-colors">
                                    Yes, Cancel
                                </button>
                            </form>
                            <button type="button" @click="confirming = false"
                                    class="px-3 py-1 bg-gray-100 text-gray-600 text-xs font-bold rounded-lg hover:bg-gray-200 transition-colors">
                                No, Keep
                            </button>
                        </div>
                    </div>
                @endif
                <a href="{{ route('professionals.show', $appointment->professional) }}"
                   class="px-6 py-2.5 bg-indigo-600 text-white font-semibold text-sm rounded-xl hover:bg-indigo-700 transition-colors flex items-center gap-2 ml-auto">
                    <i data-lucide="calendar-plus" class="w-4 h-4"></i>
                    Book Again
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
