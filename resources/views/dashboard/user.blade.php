@extends('layouts.dashboard')
@section('title', 'My Dashboard')
@section('page_title', 'My Dashboard')

@section('sidebar')
    <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
        <i data-lucide="layout-dashboard" class="w-4 h-4"></i> Dashboard
    </a>
    <a href="{{ route('appointments.index') }}" class="sidebar-link {{ request()->routeIs('appointments.*') ? 'active' : '' }}">
        <i data-lucide="calendar" class="w-4 h-4"></i> My Appointments
    </a>
    <a href="{{ route('professionals.index') }}" class="sidebar-link">
        <i data-lucide="search" class="w-4 h-4"></i> Find Professionals
    </a>
@endsection

@section('sidebar_icons')
    <a href="{{ route('dashboard') }}" title="Dashboard"
       class="w-10 h-10 mx-auto flex items-center justify-center rounded-xl text-gray-500 hover:bg-brand-50 hover:text-brand-600 transition-all {{ request()->routeIs('dashboard') ? 'bg-brand-50 text-brand-600' : '' }}">
        <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
    </a>
    <a href="{{ route('appointments.index') }}" title="My Appointments"
       class="w-10 h-10 mx-auto flex items-center justify-center rounded-xl text-gray-500 hover:bg-brand-50 hover:text-brand-600 transition-all {{ request()->routeIs('appointments.*') ? 'bg-brand-50 text-brand-600' : '' }}">
        <i data-lucide="calendar" class="w-5 h-5"></i>
    </a>
    <a href="{{ route('professionals.index') }}" title="Find Professionals"
       class="w-10 h-10 mx-auto flex items-center justify-center rounded-xl text-gray-500 hover:bg-brand-50 hover:text-brand-600 transition-all">
        <i data-lucide="search" class="w-5 h-5"></i>
    </a>
@endsection

@section('content')
<!-- Stats -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    @foreach([
        ['label' => 'Total Bookings', 'value' => $stats['total'], 'icon' => 'calendar', 'color' => 'indigo'],
        ['label' => 'Upcoming', 'value' => $stats['upcoming'], 'icon' => 'clock', 'color' => 'blue'],
        ['label' => 'Completed', 'value' => $stats['completed'], 'icon' => 'check-circle', 'color' => 'green'],
        ['label' => 'Cancelled', 'value' => $stats['cancelled'], 'icon' => 'x-circle', 'color' => 'red'],
    ] as $stat)
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-{{ $stat['color'] }}-100 flex items-center justify-center">
                    <i data-lucide="{{ $stat['icon'] }}" class="w-5 h-5 text-{{ $stat['color'] }}-600"></i>
                </div>
            </div>
            <p class="text-3xl font-extrabold text-gray-900">{{ $stat['value'] }}</p>
            <p class="text-sm text-gray-500 mt-0.5">{{ $stat['label'] }}</p>
        </div>
    @endforeach
</div>

<!-- Upcoming Appointments -->
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm mb-6">
    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
        <h2 class="font-bold text-gray-900 flex items-center gap-2">
            <i data-lucide="calendar-clock" class="w-4 h-4 text-indigo-500"></i>
            Upcoming Appointments
        </h2>
        <a href="{{ route('appointments.index') }}" class="text-sm text-indigo-600 font-medium hover:text-indigo-700">View all</a>
    </div>
    <div class="divide-y divide-gray-50">
        @forelse($upcomingAppointments as $apt)
            <div class="px-6 py-4 flex items-center gap-4 hover:bg-gray-50/50 transition-colors">
                <img src="{{ $apt->professional->photo_url }}" alt="{{ $apt->professional->user->name }}"
                     class="w-12 h-12 rounded-xl object-cover flex-shrink-0">
                <div class="flex-1 min-w-0">
                    <p class="font-semibold text-gray-900 truncate">{{ $apt->professional->user->name }}</p>
                    <p class="text-sm text-gray-500">{{ $apt->professional->category->name }}</p>
                </div>
                <div class="text-right flex-shrink-0">
                    <p class="text-sm font-semibold text-gray-800">{{ $apt->appointment_date->format('d M Y') }}</p>
                    <p class="text-xs text-gray-500">{{ $apt->time_slot }}</p>
                </div>
                <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $apt->status_badge_class }} flex-shrink-0">
                    {{ ucfirst($apt->status) }}
                </span>
                <a href="{{ route('appointments.show', $apt) }}" class="flex-shrink-0 p-1.5 text-gray-400 hover:text-indigo-600">
                    <i data-lucide="chevron-right" class="w-4 h-4"></i>
                </a>
            </div>
        @empty
            <div class="px-6 py-12 text-center">
                <i data-lucide="calendar" class="w-10 h-10 text-gray-200 mx-auto mb-3"></i>
                <p class="text-sm text-gray-400 mb-4">No upcoming appointments</p>
                <a href="{{ route('professionals.index') }}" class="px-4 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-700 transition-colors">
                    Book an Appointment
                </a>
            </div>
        @endforelse
    </div>
</div>

<!-- Past Appointments -->
@if($pastAppointments->count() > 0)
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm">
    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
        <h2 class="font-bold text-gray-900 flex items-center gap-2">
            <i data-lucide="history" class="w-4 h-4 text-gray-500"></i>
            Past Appointments
        </h2>
    </div>
    <div class="divide-y divide-gray-50">
        @foreach($pastAppointments as $apt)
            <div class="px-6 py-4 flex items-center gap-4">
                <img src="{{ $apt->professional->photo_url }}" alt="{{ $apt->professional->user->name }}"
                     class="w-10 h-10 rounded-xl object-cover flex-shrink-0 opacity-70">
                <div class="flex-1 min-w-0">
                    <p class="font-medium text-gray-700 truncate">{{ $apt->professional->user->name }}</p>
                    <p class="text-xs text-gray-400">{{ $apt->appointment_date->format('d M Y') }} · {{ $apt->time_slot }}</p>
                </div>
                <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $apt->status_badge_class }}">
                    {{ ucfirst($apt->status) }}
                </span>
                <a href="{{ route('appointments.show', $apt) }}" class="p-1.5 text-gray-400 hover:text-indigo-600">
                    <i data-lucide="eye" class="w-4 h-4"></i>
                </a>
            </div>
        @endforeach
    </div>
</div>
@endif
@endsection
