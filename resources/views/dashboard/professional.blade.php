@extends('layouts.dashboard')
@section('title', 'Professional Dashboard')
@section('page_title', 'Professional Dashboard')

@section('sidebar')
    <a href="{{ route('professional.dashboard') }}" class="sidebar-link {{ request()->routeIs('professional.dashboard') ? 'active' : '' }}">
        <i data-lucide="layout-dashboard" class="w-4 h-4"></i> Dashboard
    </a>
    @if(auth()->user()->professional)
        <a href="{{ route('professional.profile.edit', auth()->user()->professional) }}" class="sidebar-link">
            <i data-lucide="user-cog" class="w-4 h-4"></i> My Profile
        </a>
    @endif
    <a href="{{ route('professionals.index') }}" class="sidebar-link">
        <i data-lucide="globe" class="w-4 h-4"></i> Public Listing
    </a>
@endsection

@section('sidebar_icons')
    <a href="{{ route('professional.dashboard') }}" title="Dashboard"
       class="w-10 h-10 mx-auto flex items-center justify-center rounded-xl text-gray-500 hover:bg-brand-50 hover:text-brand-600 transition-all {{ request()->routeIs('professional.dashboard') ? 'bg-brand-50 text-brand-600' : '' }}">
        <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
    </a>
    @if(auth()->user()->professional)
        <a href="{{ route('professional.profile.edit', auth()->user()->professional) }}" title="My Profile"
           class="w-10 h-10 mx-auto flex items-center justify-center rounded-xl text-gray-500 hover:bg-brand-50 hover:text-brand-600 transition-all">
            <i data-lucide="user-cog" class="w-5 h-5"></i>
        </a>
    @endif
    <a href="{{ route('professionals.index') }}" title="Public Listing"
       class="w-10 h-10 mx-auto flex items-center justify-center rounded-xl text-gray-500 hover:bg-brand-50 hover:text-brand-600 transition-all">
        <i data-lucide="globe" class="w-5 h-5"></i>
    </a>
@endsection


@section('content')

<!-- Stats -->
<div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
    @foreach([
        ['label' => 'Pending', 'value' => $stats['pending'], 'icon' => 'clock', 'color' => 'amber'],
        ['label' => 'Confirmed', 'value' => $stats['confirmed'], 'icon' => 'check-circle', 'color' => 'blue'],
        ['label' => 'Completed', 'value' => $stats['completed'], 'icon' => 'check-check', 'color' => 'green'],
        ['label' => 'Total', 'value' => $stats['total'], 'icon' => 'calendar', 'color' => 'indigo'],
        ['label' => 'Revenue', 'value' => '₹' . number_format($stats['revenue']), 'icon' => 'indian-rupee', 'color' => 'emerald'],
    ] as $stat)
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 hover:shadow-md transition-shadow">
            <div class="w-9 h-9 rounded-xl bg-{{ $stat['color'] }}-100 flex items-center justify-center mb-3">
                <i data-lucide="{{ $stat['icon'] }}" class="w-4 h-4 text-{{ $stat['color'] }}-600"></i>
            </div>
            <p class="text-2xl font-extrabold text-gray-900">{{ $stat['value'] }}</p>
            <p class="text-xs text-gray-500 mt-0.5">{{ $stat['label'] }}</p>
        </div>
    @endforeach
</div>

<!-- Pending Bookings — Requires Action -->
<div class="bg-white rounded-2xl border border-amber-100 shadow-sm mb-6">
    <div class="px-6 py-4 border-b border-amber-100 bg-amber-50 rounded-t-2xl flex items-center gap-2">
        <div class="w-2 h-2 bg-amber-500 rounded-full animate-pulse"></div>
        <h2 class="font-bold text-amber-800">
            Pending Bookings — Action Required ({{ $pendingAppointments->count() }})
        </h2>
    </div>
    <div class="divide-y divide-gray-50">
        @forelse($pendingAppointments as $apt)
            <div class="px-6 py-4 flex flex-col sm:flex-row sm:items-center gap-4">
                <div class="flex items-center gap-3 flex-1 min-w-0">
                    <img src="{{ $apt->user->avatar_url }}" alt="{{ $apt->user->name }}" class="w-10 h-10 rounded-xl object-cover flex-shrink-0">
                    <div class="min-w-0">
                        <p class="font-semibold text-gray-900">{{ $apt->user->name }}</p>
                        <p class="text-xs text-gray-500">{{ $apt->appointment_date->format('D, d M Y') }} · {{ $apt->time_slot }}</p>
                        @if($apt->notes)
                            <p class="text-xs text-gray-400 mt-0.5 italic truncate">"{{ $apt->notes }}"</p>
                        @endif
                    </div>
                </div>
                <div class="flex items-center gap-2 flex-shrink-0">
                    <form action="{{ route('professional.appointments.confirm', $apt) }}" method="POST">
                        @csrf @method('PATCH')
                        <button class="px-3 py-1.5 bg-green-600 text-white text-xs font-bold rounded-lg hover:bg-green-700 transition-colors flex items-center gap-1">
                            <i data-lucide="check" class="w-3 h-3"></i> Confirm
                        </button>
                    </form>
                    <form action="{{ route('professional.appointments.reject', $apt) }}" method="POST" class="flex items-center gap-2">
                        @csrf @method('PATCH')
                        <input type="text" name="reason" placeholder="Reason (optional)" class="px-2 py-1.5 border border-gray-200 rounded-lg text-xs focus:outline-none focus:ring-1 focus:ring-red-400 w-32">
                        <button class="px-3 py-1.5 bg-red-100 text-red-700 text-xs font-bold rounded-lg hover:bg-red-200 transition-colors flex items-center gap-1">
                            <i data-lucide="x" class="w-3 h-3"></i> Reject
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="px-6 py-10 text-center">
                <i data-lucide="inbox" class="w-10 h-10 text-gray-200 mx-auto mb-2"></i>
                <p class="text-sm text-gray-400">No pending bookings. All caught up!</p>
            </div>
        @endforelse
    </div>
</div>

<!-- Upcoming Confirmed -->
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm mb-6">
    <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2">
        <i data-lucide="calendar-check" class="w-4 h-4 text-green-500"></i>
        <h2 class="font-bold text-gray-900">Upcoming Confirmed ({{ $upcomingAppointments->count() }})</h2>
    </div>
    <div class="divide-y divide-gray-50">
        @forelse($upcomingAppointments as $apt)
            <div class="px-6 py-4 flex items-center gap-4">
                <img src="{{ $apt->user->avatar_url }}" alt="{{ $apt->user->name }}" class="w-10 h-10 rounded-xl object-cover flex-shrink-0">
                <div class="flex-1 min-w-0">
                    <p class="font-semibold text-gray-900">{{ $apt->user->name }}</p>
                    <p class="text-xs text-gray-500">{{ $apt->appointment_date->format('D, d M Y') }} · {{ $apt->time_slot }}</p>
                </div>
                <form action="{{ route('professional.appointments.complete', $apt) }}" method="POST">
                    @csrf @method('PATCH')
                    <button class="px-3 py-1.5 bg-indigo-100 text-indigo-700 text-xs font-bold rounded-lg hover:bg-indigo-200 transition-colors flex items-center gap-1">
                        <i data-lucide="check-check" class="w-3 h-3"></i> Mark Complete
                    </button>
                </form>
            </div>
        @empty
            <div class="px-6 py-8 text-center">
                <p class="text-sm text-gray-400">No upcoming confirmed appointments.</p>
            </div>
        @endforelse
    </div>
</div>

<!-- Availability Manager -->
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm" x-data="availabilityManager()" x-init="init()">
    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
        <h2 class="font-bold text-gray-900 flex items-center gap-2">
            <i data-lucide="clock" class="w-4 h-4 text-indigo-500"></i>
            Manage Availability
        </h2>
        <button @click="addSlot()" class="text-xs bg-indigo-50 text-indigo-700 font-semibold px-3 py-1.5 rounded-lg hover:bg-indigo-100 transition-colors">
            + Add Day
        </button>
    </div>
    <div class="p-6">
        <form action="{{ route('professional.availability.update') }}" method="POST">
            @csrf
            <div class="space-y-3">
                <template x-for="(slot, index) in slots" :key="index">
                    <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl">
                        <select :name="`availabilities[${index}][day_of_week]`" x-model="slot.day"
                                class="flex-1 px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500">
                            <option value="1">Monday</option>
                            <option value="2">Tuesday</option>
                            <option value="3">Wednesday</option>
                            <option value="4">Thursday</option>
                            <option value="5">Friday</option>
                            <option value="6">Saturday</option>
                            <option value="0">Sunday</option>
                        </select>
                        <input type="time" :name="`availabilities[${index}][start_time]`" x-model="slot.start"
                               class="px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500 w-28">
                        <span class="text-gray-400 text-sm">to</span>
                        <input type="time" :name="`availabilities[${index}][end_time]`" x-model="slot.end"
                               class="px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500 w-28">
                        <button type="button" @click="slots.splice(index, 1)" class="text-red-400 hover:text-red-600 p-1">
                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                        </button>
                    </div>
                </template>
            </div>
            <button type="submit" class="mt-4 px-5 py-2.5 bg-indigo-600 text-white font-semibold rounded-xl hover:bg-indigo-700 transition-colors text-sm">
                Save Availability
            </button>
        </form>
    </div>
</div>

@push('scripts')
<script>
function availabilityManager() {
    return {
        slots: [],
        init() {
            const existing = {{ Js::from($availabilities->map(function($a) {
                return ['day' => (string)$a->day_of_week, 'start' => substr($a->start_time, 0, 5), 'end' => substr($a->end_time, 0, 5)];
            })->values()) }};
            this.slots = existing.length ? existing : [{ day: '1', start: '09:00', end: '17:00' }];
        },
        addSlot() {
            this.slots.push({ day: '1', start: '09:00', end: '17:00' });
        }
    }
}
</script>
@endpush
@endsection
