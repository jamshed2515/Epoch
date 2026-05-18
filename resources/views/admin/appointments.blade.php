@extends('layouts.dashboard')
@section('title', 'Appointments')
@section('page_title', 'All Appointments')

@section('sidebar')
    <a href="{{ route('admin.dashboard') }}" class="sidebar-link">
        <i data-lucide="layout-dashboard" class="w-4 h-4"></i> Dashboard
    </a>
    <a href="{{ route('admin.users') }}" class="sidebar-link">
        <i data-lucide="users" class="w-4 h-4"></i> Users
    </a>
    <a href="{{ route('admin.professionals') }}" class="sidebar-link">
        <i data-lucide="briefcase" class="w-4 h-4"></i> Professionals
    </a>
    <a href="{{ route('admin.categories') }}" class="sidebar-link">
        <i data-lucide="grid" class="w-4 h-4"></i> Categories
    </a>
    <a href="{{ route('admin.appointments') }}" class="sidebar-link active">
        <i data-lucide="calendar" class="w-4 h-4"></i> Appointments
    </a>
@endsection

@section('content')
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm">
    <div class="px-6 py-4 border-b border-gray-100">
        <form method="GET" class="flex gap-3">
            <select name="status" onchange="this.form.submit()" class="px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none">
                <option value="">All Statuses</option>
                @foreach(['pending','confirmed','cancelled','completed'] as $s)
                    <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-100 bg-gray-50">
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">#</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Client</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Professional</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Slot</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Payment</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Fee</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($appointments as $apt)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-3 text-gray-400 text-xs">#{{ $apt->id }}</td>
                        <td class="px-6 py-3 font-medium text-gray-800">{{ $apt->user->name }}</td>
                        <td class="px-6 py-3 text-gray-600">{{ $apt->professional->user->name }}</td>
                        <td class="px-6 py-3 text-gray-500">{{ $apt->appointment_date->format('d M Y') }}</td>
                        <td class="px-6 py-3 text-gray-500 font-mono text-xs">{{ $apt->time_slot }}</td>
                        <td class="px-6 py-3">
                            <span class="px-2 py-1 rounded-full text-xs font-bold {{ $apt->status_badge_class }}">{{ ucfirst($apt->status) }}</span>
                        </td>
                        <td class="px-6 py-3">
                            @if($apt->payment_status === 'paid')
                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-bold bg-green-100 text-green-700">
                                    <i data-lucide="check-circle" class="w-3 h-3"></i> Paid
                                </span>
                            @else
                                <span class="px-2 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-500">Unpaid</span>
                            @endif
                        </td>
                        <td class="px-6 py-3 font-medium text-gray-700">₹{{ number_format($apt->fee) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-400 text-sm">No appointments found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="px-6 py-4 border-t border-gray-100">
        {{ $appointments->withQueryString()->links() }}
    </div>
</div>
@endsection
