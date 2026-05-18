@extends('layouts.dashboard')
@section('title', 'User Management')
@section('page_title', 'User Management')

@section('sidebar')
    <a href="{{ route('admin.dashboard') }}" class="sidebar-link">
        <i data-lucide="layout-dashboard" class="w-4 h-4"></i> Dashboard
    </a>
    <a href="{{ route('admin.users') }}" class="sidebar-link active">
        <i data-lucide="users" class="w-4 h-4"></i> Users
    </a>
    <a href="{{ route('admin.professionals') }}" class="sidebar-link">
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
    <div class="px-6 py-4 border-b border-gray-100 flex flex-col sm:flex-row gap-3">
        <form method="GET" class="flex gap-3 flex-1">
            <div class="relative flex-1">
                <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search users..."
                       class="w-full pl-9 pr-4 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none">
            </div>
            <select name="role" onchange="this.form.submit()" class="px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none">
                <option value="">All Roles</option>
                <option value="user" {{ request('role') === 'user' ? 'selected' : '' }}>Users</option>
                <option value="professional" {{ request('role') === 'professional' ? 'selected' : '' }}>Professionals</option>
                <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admins</option>
            </select>
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-100 bg-gray-50">
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">User</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Role</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Phone</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Joined</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($users as $user)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-3">
                            <div class="flex items-center gap-3">
                                <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-8 h-8 rounded-full object-cover">
                                <span class="font-medium text-gray-800">{{ $user->name }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-3 text-gray-600">{{ $user->email }}</td>
                        <td class="px-6 py-3">
                            <span class="px-2 py-1 rounded-full text-xs font-bold
                                {{ $user->role === 'admin' ? 'bg-purple-100 text-purple-700' :
                                   ($user->role === 'professional' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-700') }}">
                                {{ ucfirst($user->role) }}
                            </span>
                        </td>
                        <td class="px-6 py-3 text-gray-500">{{ $user->phone ?? '—' }}</td>
                        <td class="px-6 py-3 text-gray-500">{{ $user->created_at->format('d M Y') }}</td>
                        <td class="px-6 py-3">
                            @if($user->role !== 'admin')
                                <div x-data="{ confirming: false }" class="flex items-center">
                                    {{-- Step 1: delete button --}}
                                    <button type="button"
                                            x-show="!confirming"
                                            @click="confirming = true"
                                            class="text-red-500 hover:text-red-700 text-xs font-medium flex items-center gap-1 hover:bg-red-50 px-2 py-1 rounded transition-colors">
                                        <i data-lucide="trash-2" class="w-3.5 h-3.5"></i> Delete
                                    </button>

                                    {{-- Step 2: inline confirm --}}
                                    <div x-show="confirming" x-cloak
                                         class="flex items-center gap-1.5 bg-red-50 border border-red-200 rounded-lg px-2 py-1.5">
                                        <span class="text-xs text-red-700 font-medium whitespace-nowrap">Sure?</span>
                                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                    class="px-2 py-0.5 bg-red-600 text-white text-xs font-bold rounded hover:bg-red-700 transition-colors">
                                                Yes
                                            </button>
                                        </form>
                                        <button type="button" @click="confirming = false"
                                                class="px-2 py-0.5 bg-gray-100 text-gray-600 text-xs font-bold rounded hover:bg-gray-200 transition-colors">
                                            No
                                        </button>
                                    </div>
                                </div>
                            @else
                                <span class="text-xs text-gray-300">Protected</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="px-6 py-4 border-t border-gray-100">
        {{ $users->withQueryString()->links() }}
    </div>
</div>
@endsection
