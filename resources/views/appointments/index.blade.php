@extends('layouts.app')
@section('title', 'My Appointments')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">My Appointments</h1>
            <p class="text-gray-500 mt-1">{{ $appointments->total() }} total appointments</p>
        </div>
        <a href="{{ route('professionals.index') }}"
           class="px-5 py-2.5 bg-indigo-600 text-white font-semibold rounded-xl hover:bg-indigo-700 transition-colors shadow-sm flex items-center gap-2">
            <i data-lucide="plus" class="w-4 h-4"></i>
            Book New
        </a>
    </div>

    @if($appointments->isEmpty())
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm py-20 text-center">
            <i data-lucide="calendar" class="w-16 h-16 text-gray-200 mx-auto mb-4"></i>
            <h2 class="text-lg font-semibold text-gray-500 mb-2">No appointments yet</h2>
            <p class="text-sm text-gray-400 mb-6">Start by booking an appointment with a professional.</p>
            <a href="{{ route('professionals.index') }}" class="px-6 py-3 bg-indigo-600 text-white font-semibold rounded-xl hover:bg-indigo-700 transition-colors">
                Browse Professionals
            </a>
        </div>
    @else
        <div class="space-y-4">
            @foreach($appointments as $apt)
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow p-5">
                    <div class="flex flex-col sm:flex-row gap-4">
                        <img src="{{ $apt->professional->photo_url }}" alt="{{ $apt->professional->user->name }}"
                             class="w-16 h-16 rounded-xl object-cover flex-shrink-0">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <h2 class="font-bold text-gray-900 text-lg">{{ $apt->professional->user->name }}</h2>
                                    <p class="text-sm text-indigo-600 font-medium">{{ $apt->professional->category->name }}</p>
                                </div>
                                <span class="px-3 py-1 rounded-full text-xs font-bold {{ $apt->status_badge_class }} flex-shrink-0">
                                    {{ ucfirst($apt->status) }}
                                </span>
                            </div>
                            <div class="flex flex-wrap gap-4 mt-3 text-sm text-gray-500">
                                <span class="flex items-center gap-1.5">
                                    <i data-lucide="calendar" class="w-4 h-4 text-indigo-400"></i>
                                    {{ $apt->appointment_date->format('D, d M Y') }}
                                </span>
                                <span class="flex items-center gap-1.5">
                                    <i data-lucide="clock" class="w-4 h-4 text-indigo-400"></i>
                                    {{ $apt->time_slot }}
                                </span>
                                <span class="flex items-center gap-1.5">
                                    <i data-lucide="indian-rupee" class="w-4 h-4 text-green-500"></i>
                                    {{ number_format($apt->fee) }}
                                </span>
                                <span class="flex items-center gap-1.5">
                                    <i data-lucide="map-pin" class="w-4 h-4 text-gray-400"></i>
                                    {{ $apt->professional->location }}
                                </span>
                            </div>
                            @if($apt->notes)
                                <p class="text-sm text-gray-400 mt-2 italic">{{ $apt->notes }}</p>
                            @endif
                        </div>
                        <div class="flex sm:flex-col gap-2 items-end justify-start flex-shrink-0">
                            <a href="{{ route('appointments.show', $apt) }}"
                               class="px-4 py-2 text-sm font-semibold bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors flex items-center gap-1.5">
                                <i data-lucide="eye" class="w-4 h-4"></i> View
                            </a>
                            @if($apt->canBeCancelledByUser())
                                <div x-data="{ confirming: false }">
                                    <button type="button"
                                            x-show="!confirming"
                                            @click="confirming = true"
                                            class="px-4 py-2 text-sm font-semibold bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition-colors flex items-center gap-1.5">
                                        <i data-lucide="x" class="w-4 h-4"></i> Cancel
                                    </button>
                                    <div x-show="confirming" x-cloak
                                         class="flex flex-col gap-1 bg-red-50 border border-red-200 rounded-lg p-2">
                                        <span class="text-xs text-red-700 font-medium text-center">Cancel?</span>
                                        <form action="{{ route('appointments.cancel', $apt) }}" method="POST">
                                            @csrf @method('PATCH')
                                            <button type="submit"
                                                    class="w-full px-2 py-1 bg-red-600 text-white text-xs font-bold rounded hover:bg-red-700 transition-colors">
                                                Yes
                                            </button>
                                        </form>
                                        <button type="button" @click="confirming = false"
                                                class="w-full px-2 py-1 bg-gray-100 text-gray-600 text-xs font-bold rounded hover:bg-gray-200 transition-colors">
                                            No
                                        </button>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6">{{ $appointments->links() }}</div>
    @endif
</div>
@endsection
