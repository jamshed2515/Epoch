@extends('layouts.app')
@section('title', 'Edit Professional Profile')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="flex items-center gap-3 mb-8">
        <a href="{{ route('professional.dashboard') }}" class="flex items-center gap-2 text-sm text-gray-500 hover:text-indigo-600 transition-colors">
            <i data-lucide="arrow-left" class="w-4 h-4"></i> Back to Dashboard
        </a>
    </div>

    <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Edit Your Profile</h1>
        <p class="text-gray-500 mt-2">Update your professional information and availability.</p>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8">
        <form action="{{ route('professional.profile.update', $professional) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Current Photo & Upload -->
            <div class="flex items-center gap-6">
                <div class="w-24 h-24 rounded-2xl overflow-hidden border-2 border-gray-200 flex-shrink-0">
                    <img src="{{ $professional->photo_url }}" alt="{{ $professional->user->name }}"
                         class="w-full h-full object-cover" id="photo-preview">
                </div>
                <div>
                    <label class="cursor-pointer px-4 py-2 bg-indigo-50 text-indigo-700 font-semibold text-sm rounded-lg hover:bg-indigo-100 transition-colors flex items-center gap-2">
                        <i data-lucide="upload" class="w-4 h-4"></i>
                        Change Photo
                        <input type="file" name="photo" accept="image/jpg,image/jpeg,image/png" class="sr-only"
                               onchange="document.getElementById('photo-preview').src = URL.createObjectURL(this.files[0])">
                    </label>
                    <p class="text-xs text-gray-400 mt-1">JPG, PNG. Max 2MB.</p>
                </div>
            </div>

            <!-- Category -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Specialty / Category *</label>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                    @foreach($categories as $cat)
                        <label class="relative cursor-pointer">
                            <input type="radio" name="category_id" value="{{ $cat->id }}"
                                   {{ old('category_id', $professional->category_id) == $cat->id ? 'checked' : '' }} class="sr-only peer">
                            <div class="p-3 border-2 border-gray-200 rounded-xl peer-checked:border-indigo-500 peer-checked:bg-indigo-50 hover:border-indigo-300 transition-all text-center">
                                <i data-lucide="{{ $cat->icon }}" class="w-5 h-5 mx-auto mb-1" style="color: {{ $cat->color }}"></i>
                                <p class="text-xs font-semibold text-gray-700">{{ $cat->name }}</p>
                            </div>
                        </label>
                    @endforeach
                </div>
                @error('category_id') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Bio -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Bio / About You *</label>
                <textarea name="bio" rows="4" minlength="50" maxlength="2000" required
                          class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none resize-none transition-all">{{ old('bio', $professional->bio) }}</textarea>
                @error('bio') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Location *</label>
                    <div class="relative">
                        <i data-lucide="map-pin" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                        <input type="text" name="location" value="{{ old('location', $professional->location) }}" required
                               class="w-full pl-9 pr-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                    </div>
                    @error('location') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Years of Experience *</label>
                    <input type="number" name="experience_years"
                           value="{{ old('experience_years', $professional->experience_years) }}" min="0" max="50" required
                           class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                    @error('experience_years') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Consultation Fee (₹) *</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 font-medium text-sm">₹</span>
                        <input type="number" name="consultation_fee"
                               value="{{ old('consultation_fee', $professional->consultation_fee) }}" min="0" required
                               class="w-full pl-8 pr-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                    </div>
                    @error('consultation_fee') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Session Duration *</label>
                    <select name="session_duration"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                        @foreach([15 => '15 minutes', 30 => '30 minutes', 45 => '45 minutes', 60 => '60 minutes'] as $val => $label)
                            <option value="{{ $val }}" {{ old('session_duration', $professional->session_duration) == $val ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Specializations -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Specializations</label>
                <input type="text" name="specializations"
                       value="{{ old('specializations', $professional->specializations ? implode(', ', $professional->specializations) : '') }}"
                       class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 outline-none"
                       placeholder="e.g. Cardiology, Hypertension (comma separated)">
            </div>

            <div class="flex gap-4 pt-4">
                <a href="{{ route('professional.dashboard') }}"
                   class="flex-1 py-3.5 border border-gray-200 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 transition-colors text-center text-sm">
                    Cancel
                </a>
                <button type="submit"
                        class="flex-1 py-3.5 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-bold rounded-xl hover:from-indigo-700 hover:to-purple-700 shadow-lg hover:shadow-xl transition-all duration-200 flex items-center justify-center gap-2 text-sm">
                    <i data-lucide="save" class="w-4 h-4"></i>
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
