@extends('layouts.dashboard')
@section('title', 'Admin Dashboard')
@section('page_title', 'Admin Dashboard')

@section('sidebar')
    <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
        <i data-lucide="layout-dashboard" class="w-4 h-4"></i> Dashboard
    </a>
    <a href="{{ route('admin.users') }}" class="sidebar-link {{ request()->routeIs('admin.users') ? 'active' : '' }}">
        <i data-lucide="users" class="w-4 h-4"></i> Users
    </a>
    <a href="{{ route('admin.professionals') }}" class="sidebar-link {{ request()->routeIs('admin.professionals') ? 'active' : '' }}">
        <i data-lucide="briefcase" class="w-4 h-4"></i> Professionals
    </a>
    <a href="{{ route('admin.categories') }}" class="sidebar-link {{ request()->routeIs('admin.categories') ? 'active' : '' }}">
        <i data-lucide="grid" class="w-4 h-4"></i> Categories
    </a>
    <a href="{{ route('admin.appointments') }}" class="sidebar-link {{ request()->routeIs('admin.appointments') ? 'active' : '' }}">
        <i data-lucide="calendar" class="w-4 h-4"></i> Appointments
    </a>
@endsection

@section('sidebar_icons')
    <a href="{{ route('admin.dashboard') }}" title="Dashboard"
       class="w-10 h-10 mx-auto flex items-center justify-center rounded-xl text-gray-500 hover:bg-brand-50 hover:text-brand-600 transition-all {{ request()->routeIs('admin.dashboard') ? 'bg-brand-50 text-brand-600' : '' }}">
        <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
    </a>
    <a href="{{ route('admin.users') }}" title="Users"
       class="w-10 h-10 mx-auto flex items-center justify-center rounded-xl text-gray-500 hover:bg-brand-50 hover:text-brand-600 transition-all {{ request()->routeIs('admin.users') ? 'bg-brand-50 text-brand-600' : '' }}">
        <i data-lucide="users" class="w-5 h-5"></i>
    </a>
    <a href="{{ route('admin.professionals') }}" title="Professionals"
       class="w-10 h-10 mx-auto flex items-center justify-center rounded-xl text-gray-500 hover:bg-brand-50 hover:text-brand-600 transition-all {{ request()->routeIs('admin.professionals') ? 'bg-brand-50 text-brand-600' : '' }}">
        <i data-lucide="briefcase" class="w-5 h-5"></i>
    </a>
    <a href="{{ route('admin.categories') }}" title="Categories"
       class="w-10 h-10 mx-auto flex items-center justify-center rounded-xl text-gray-500 hover:bg-brand-50 hover:text-brand-600 transition-all {{ request()->routeIs('admin.categories') ? 'bg-brand-50 text-brand-600' : '' }}">
        <i data-lucide="grid" class="w-5 h-5"></i>
    </a>
    <a href="{{ route('admin.appointments') }}" title="Appointments"
       class="w-10 h-10 mx-auto flex items-center justify-center rounded-xl text-gray-500 hover:bg-brand-50 hover:text-brand-600 transition-all {{ request()->routeIs('admin.appointments') ? 'bg-brand-50 text-brand-600' : '' }}">
        <i data-lucide="calendar" class="w-5 h-5"></i>
    </a>
@endsection

@section('content')
<!-- Stats -->
<div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4 mb-8">
    @foreach([
        ['label' => 'Users', 'value' => $stats['users'], 'icon' => 'users', 'color' => 'indigo'],
        ['label' => 'Professionals', 'value' => $stats['professionals'], 'icon' => 'briefcase', 'color' => 'purple'],
        ['label' => 'Appointments', 'value' => $stats['appointments'], 'icon' => 'calendar', 'color' => 'blue'],
        ['label' => 'Categories', 'value' => $stats['categories'], 'icon' => 'grid', 'color' => 'green'],
        ['label' => 'Pending', 'value' => $stats['pending'], 'icon' => 'clock', 'color' => 'amber'],
        ['label' => 'Revenue', 'value' => '₹' . number_format($stats['revenue']), 'icon' => 'indian-rupee', 'color' => 'emerald'],
    ] as $stat)
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <div class="w-8 h-8 rounded-lg bg-{{ $stat['color'] }}-100 flex items-center justify-center mb-3">
                <i data-lucide="{{ $stat['icon'] }}" class="w-4 h-4 text-{{ $stat['color'] }}-600"></i>
            </div>
            <p class="text-2xl font-extrabold text-gray-900">{{ $stat['value'] }}</p>
            <p class="text-xs text-gray-500">{{ $stat['label'] }}</p>
        </div>
    @endforeach
</div>

<!-- Recent Appointments -->
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm">
    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
        <h2 class="font-bold text-gray-900">Recent Appointments</h2>
        <a href="{{ route('admin.appointments') }}" class="text-sm text-indigo-600 font-medium">View all</a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-100 bg-gray-50">
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Client</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Professional</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Fee</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($recentAppointments as $apt)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-3 font-medium text-gray-800">{{ $apt->user->name }}</td>
                        <td class="px-6 py-3 text-gray-600">{{ $apt->professional->user->name }}</td>
                        <td class="px-6 py-3 text-gray-500">{{ $apt->appointment_date->format('d M Y') }}</td>
                        <td class="px-6 py-3">
                            <span class="px-2 py-1 rounded-full text-xs font-bold {{ $apt->status_badge_class }}">{{ ucfirst($apt->status) }}</span>
                        </td>
                        <td class="px-6 py-3 text-gray-700 font-medium">₹{{ number_format($apt->fee) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
