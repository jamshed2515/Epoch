@extends('layouts.dashboard')
@section('title', 'Categories')
@section('page_title', 'Category Management')

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
    <a href="{{ route('admin.categories') }}" class="sidebar-link active">
        <i data-lucide="grid" class="w-4 h-4"></i> Categories
    </a>
    <a href="{{ route('admin.appointments') }}" class="sidebar-link">
        <i data-lucide="calendar" class="w-4 h-4"></i> Appointments
    </a>
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Add Category Form -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 h-fit">
        <h2 class="font-bold text-gray-900 mb-5 flex items-center gap-2">
            <i data-lucide="plus-circle" class="w-4 h-4 text-indigo-500"></i>
            Add Category
        </h2>
        <form action="{{ route('admin.categories.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1">Name *</label>
                <input type="text" name="name" required value="{{ old('name') }}"
                       class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 outline-none"
                       placeholder="e.g. Nutritionists">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1">Description</label>
                <textarea name="description" rows="3"
                          class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 outline-none resize-none"
                          placeholder="Short description...">{{ old('description') }}</textarea>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1">Icon (Lucide)</label>
                    <input type="text" name="icon" value="{{ old('icon', 'briefcase') }}"
                           class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1">Color</label>
                    <input type="color" name="color" value="{{ old('color', '#6366f1') }}"
                           class="w-full h-10 border border-gray-200 rounded-xl cursor-pointer">
                </div>
            </div>
            <button type="submit" class="w-full py-2.5 bg-indigo-600 text-white font-semibold rounded-xl hover:bg-indigo-700 transition-colors text-sm">
                Create Category
            </button>
        </form>
    </div>

    <!-- Categories List -->
    <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="font-bold text-gray-900">All Categories ({{ $categories->count() }})</h2>
        </div>
        <div class="divide-y divide-gray-50">
            @foreach($categories as $cat)
                <div class="px-6 py-4 flex items-center gap-4">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0"
                         style="background-color: {{ $cat->color }}20">
                        <i data-lucide="{{ $cat->icon }}" class="w-5 h-5" style="color: {{ $cat->color }}"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-gray-900">{{ $cat->name }}</p>
                        <p class="text-xs text-gray-400">{{ $cat->professionals_count }} professionals · /{{ $cat->slug }}</p>
                    </div>
                    <a href="{{ route('professionals.index', ['category' => $cat->slug]) }}"
                       class="text-indigo-500 hover:text-indigo-700 p-1.5 rounded-lg hover:bg-indigo-50 transition-colors">
                        <i data-lucide="external-link" class="w-4 h-4"></i>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
