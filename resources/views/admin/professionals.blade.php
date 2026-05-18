@extends('layouts.dashboard')
@section('title', 'Professionals Management')
@section('page_title', 'Professionals Management')

@section('sidebar')
    <a href="{{ route('admin.dashboard') }}" class="sidebar-link">
        <i data-lucide="layout-dashboard" class="w-4 h-4"></i> Dashboard
    </a>
    <a href="{{ route('admin.users') }}" class="sidebar-link">
        <i data-lucide="users" class="w-4 h-4"></i> Users
    </a>
    <a href="{{ route('admin.professionals') }}" class="sidebar-link active">
        <i data-lucide="briefcase" class="w-4 h-4"></i> Professionals
    </a>
    <a href="{{ route('admin.categories') }}" class="sidebar-link">
        <i data-lucide="grid" class="w-4 h-4"></i> Categories
    </a>
    <a href="{{ route('admin.appointments') }}" class="sidebar-link">
        <i data-lucide="calendar" class="w-4 h-4"></i> Appointments
    </a>
@endsection

@section('content')
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm">
    <div class="px-6 py-4 border-b border-gray-100 flex gap-3">
        <form method="GET" class="flex gap-3">
            <select name="status" onchange="this.form.submit()" class="px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none">
                <option value="">All Status</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-100 bg-gray-50">
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Professional</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Category</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Location</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Rating</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Fee</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($professionals as $professional)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-3">
                            <div class="flex items-center gap-3">
                                <img src="{{ $professional->photo_url }}" alt="{{ $professional->user->name }}"
                                     class="w-8 h-8 rounded-full object-cover">
                                <div>
                                    <p class="font-medium text-gray-800">{{ $professional->user->name }}</p>
                                    <p class="text-xs text-gray-400">{{ $professional->experience_years }}y exp</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-3 text-gray-600">{{ $professional->category->name }}</td>
                        <td class="px-6 py-3 text-gray-500 text-xs">{{ $professional->location }}</td>
                        <td class="px-6 py-3">
                            <div class="flex items-center gap-1">
                                <i data-lucide="star" class="w-3.5 h-3.5 text-amber-400 fill-amber-400"></i>
                                <span class="font-medium text-gray-700">{{ $professional->rating }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-3 font-medium text-gray-700">₹{{ number_format($professional->consultation_fee) }}</td>
                        <td class="px-6 py-3">
                            <span class="px-2 py-1 rounded-full text-xs font-bold {{ $professional->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                                {{ $professional->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-6 py-3">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('professionals.show', $professional) }}" class="text-indigo-500 hover:text-indigo-700 p-1 rounded hover:bg-indigo-50">
                                    <i data-lucide="eye" class="w-4 h-4"></i>
                                </a>
                                <form action="{{ route('admin.professionals.toggle', $professional) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <button class="{{ $professional->is_active ? 'text-red-500 hover:text-red-700 hover:bg-red-50' : 'text-green-500 hover:text-green-700 hover:bg-green-50' }} p-1 rounded transition-colors">
                                        <i data-lucide="{{ $professional->is_active ? 'toggle-right' : 'toggle-left' }}" class="w-4 h-4"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="px-6 py-4 border-t border-gray-100">
        {{ $professionals->withQueryString()->links() }}
    </div>
</div>
@endsection
