@extends('layouts.app')
@section('title', 'Book Appointment — ' . $professional->user->name)

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="mb-6">
        <a href="{{ route('professionals.show', $professional) }}"
           class="flex items-center gap-2 text-sm text-gray-500 hover:text-indigo-600 transition-colors">
            <i data-lucide="arrow-left" class="w-4 h-4"></i> Back to Profile
        </a>
    </div>

    <!-- Professional mini-card -->
    <div class="flex items-center gap-4 bg-white rounded-2xl border border-gray-100 shadow-sm p-5 mb-6">
        <img src="{{ $professional->photo_url }}" alt="{{ $professional->user->name }}"
             class="w-16 h-16 rounded-xl object-cover flex-shrink-0">
        <div class="flex-1">
            <h2 class="font-bold text-gray-900 text-lg">{{ $professional->user->name }}</h2>
            <p class="text-sm text-indigo-600">{{ $professional->category->name }}</p>
            <p class="text-xs text-gray-500 mt-0.5 flex items-center gap-1">
                <i data-lucide="map-pin" class="w-3 h-3"></i> {{ $professional->location }}
            </p>
        </div>
        <div class="text-right">
            <p class="text-2xl font-bold text-gray-900">₹{{ number_format($professional->consultation_fee) }}</p>
            <p class="text-xs text-gray-400">{{ $professional->session_duration }} min session</p>
        </div>
    </div>

    <!-- Booking Form -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6" x-data="bookingForm()">
        <h1 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-2">
            <i data-lucide="calendar-plus" class="w-5 h-5 text-indigo-500"></i>
            Book Your Appointment
        </h1>

        @include('partials._flash')

        <form action="{{ route('appointments.store') }}" method="POST" id="booking-form">
            @csrf
            <input type="hidden" name="professional_id" value="{{ $professional->id }}">
            <input type="hidden" name="appointment_date" x-model="selectedDate" :class="{ 'border-red-400': !selectedDate && attempted }">
            <input type="hidden" name="time_slot" x-model="selectedSlot">

            <!-- Step 1: Date -->
            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    <span class="inline-flex items-center justify-center w-5 h-5 bg-indigo-100 text-indigo-700 text-xs font-bold rounded-full mr-1">1</span>
                    Select Date
                </label>
                <input type="date" x-model="selectedDate"
                       min="{{ now()->addDay()->format('Y-m-d') }}"
                       max="{{ now()->addDays(30)->format('Y-m-d') }}"
                       @change="fetchSlots()"
                       class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none transition-all"
                       value="{{ old('appointment_date', $date) }}">
                @error('appointment_date')
                    <p class="text-xs text-red-600 mt-1 flex items-center gap-1">
                        <i data-lucide="alert-circle" class="w-3 h-3"></i> {{ $message }}
                    </p>
                @enderror
            </div>

            <!-- Step 2: Time Slot -->
            <div class="mb-6" x-show="selectedDate" x-transition>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    <span class="inline-flex items-center justify-center w-5 h-5 bg-indigo-100 text-indigo-700 text-xs font-bold rounded-full mr-1">2</span>
                    Select Time Slot
                </label>

                <div x-show="loading" class="flex items-center gap-2 text-sm text-gray-400 py-4">
                    <div class="w-4 h-4 border-2 border-indigo-300 border-t-indigo-600 rounded-full animate-spin"></div>
                    Loading available slots...
                </div>

                <div x-show="!loading" class="grid grid-cols-3 sm:grid-cols-4 gap-2">
                    @foreach($slots as $slot)
                        <button type="button"
                                @click="selectedSlot = '{{ $slot }}'"
                                :class="selectedSlot === '{{ $slot }}' ? 'bg-indigo-600 text-white border-indigo-600 shadow-md' : 'bg-white text-gray-700 border-gray-200 hover:border-indigo-400 hover:text-indigo-600'"
                                class="px-3 py-2.5 border rounded-xl text-xs font-medium text-center transition-all duration-200">
                            {{ \Str::before($slot, '-') }}
                        </button>
                    @endforeach

                    <template x-for="slot in dynamicSlots" :key="slot">
                        <button type="button"
                                @click="selectedSlot = slot"
                                :class="selectedSlot === slot ? 'bg-indigo-600 text-white border-indigo-600 shadow-md' : 'bg-white text-gray-700 border-gray-200 hover:border-indigo-400 hover:text-indigo-600'"
                                class="px-3 py-2.5 border rounded-xl text-xs font-medium text-center transition-all duration-200"
                                x-text="slot.split('-')[0]">
                        </button>
                    </template>

                    <template x-if="!loading && dynamicSlots.length === 0 && {{ count($slots) === 0 ? 'true' : 'false' }}">
                        <div class="col-span-4 py-6 text-center text-sm text-gray-400">
                            <i data-lucide="calendar-x" class="w-6 h-6 mx-auto mb-1"></i>
                            No slots available for this date.
                        </div>
                    </template>
                </div>

                @error('time_slot')
                    <p class="text-xs text-red-600 mt-2 flex items-center gap-1">
                        <i data-lucide="alert-circle" class="w-3 h-3"></i> {{ $message }}
                    </p>
                @enderror
            </div>

            <!-- Step 3: Notes -->
            <div class="mb-6" x-show="selectedSlot" x-transition>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    <span class="inline-flex items-center justify-center w-5 h-5 bg-indigo-100 text-indigo-700 text-xs font-bold rounded-full mr-1">3</span>
                    Notes <span class="font-normal text-gray-400">(optional)</span>
                </label>
                <textarea name="notes" rows="3" maxlength="500"
                          class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none resize-none transition-all"
                          placeholder="Describe your concern or provide any relevant information...">{{ old('notes') }}</textarea>
                @error('notes') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Summary -->
            <div x-show="selectedDate && selectedSlot" x-transition class="mb-6">
                <div class="bg-gradient-to-br from-indigo-50 to-purple-50 border border-indigo-100 rounded-xl p-4">
                    <p class="text-sm font-semibold text-indigo-800 mb-3">Booking Summary</p>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Professional</span>
                            <span class="font-semibold text-gray-800">{{ $professional->user->name }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Date</span>
                            <span class="font-semibold text-gray-800" x-text="selectedDate ? new Date(selectedDate).toLocaleDateString('en-IN', {weekday:'short', day:'numeric', month:'long'}) : ''"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Time</span>
                            <span class="font-semibold text-gray-800" x-text="selectedSlot"></span>
                        </div>
                        <div class="flex justify-between border-t border-indigo-100 pt-2 mt-2">
                            <span class="text-gray-600 font-medium">Fee</span>
                            <span class="font-bold text-indigo-700 text-lg">₹{{ number_format($professional->consultation_fee) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit -->
            <button type="submit" id="book-btn"
                    @click="attempted = true"
                    :disabled="!selectedDate || !selectedSlot"
                    :class="(!selectedDate || !selectedSlot) ? 'opacity-50 cursor-not-allowed' : 'hover:from-indigo-700 hover:to-purple-700 shadow-lg hover:shadow-xl'"
                    class="w-full py-4 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-bold rounded-xl transition-all duration-200 flex items-center justify-center gap-2 group">
                <i data-lucide="calendar-check" class="w-5 h-5 group-hover:scale-110 transition-transform"></i>
                <span id="btn-text">Confirm Appointment</span>
            </button>

            <p class="text-xs text-gray-400 text-center mt-3">
                You can cancel your appointment anytime before the scheduled date.
            </p>
        </form>
    </div>
</div>

@push('scripts')
<script>
function bookingForm() {
    return {
        selectedDate: '{{ old('appointment_date', $date) }}',
        selectedSlot: '{{ old('time_slot') }}',
        dynamicSlots: [],
        loading: false,
        attempted: false,

        async fetchSlots() {
            if (!this.selectedDate) return;
            this.loading = true;
            this.selectedSlot = '';
            this.dynamicSlots = [];
            try {
                const resp = await fetch(`/api/professionals/{{ $professional->id }}?date=${this.selectedDate}`);
                const data = await resp.json();
                const dateSlots = data.slots?.[this.selectedDate] || [];
                this.dynamicSlots = dateSlots;
            } catch(e) {
                console.error(e);
            }
            this.loading = false;
        }
    }
}

// Loading state on submit
document.getElementById('booking-form')?.addEventListener('submit', function() {
    const btn = document.getElementById('book-btn');
    const text = document.getElementById('btn-text');
    if (btn) {
        btn.disabled = true;
        if (text) text.textContent = 'Booking...';
    }
});
</script>
@endpush
@endsection
