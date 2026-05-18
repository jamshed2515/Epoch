@if (session('success') || session('error') || session('info') || session('warning'))
    <div class="fixed top-20 right-4 z-50 space-y-2 max-w-sm w-full" x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 5000)">
        @if (session('success'))
            <div class="flex items-start gap-3 bg-white border border-green-200 rounded-xl px-4 py-3 shadow-lg">
                <div class="flex-shrink-0 w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                    <i data-lucide="check-circle" class="w-4 h-4 text-green-600"></i>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-semibold text-green-800">Success</p>
                    <p class="text-sm text-green-700">{{ session('success') }}</p>
                </div>
                <button @click="show = false" class="text-green-400 hover:text-green-600">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
        @endif

        @if (session('error'))
            <div class="flex items-start gap-3 bg-white border border-red-200 rounded-xl px-4 py-3 shadow-lg">
                <div class="flex-shrink-0 w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                    <i data-lucide="alert-circle" class="w-4 h-4 text-red-600"></i>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-semibold text-red-800">Error</p>
                    <p class="text-sm text-red-700">{{ session('error') }}</p>
                </div>
                <button @click="show = false" class="text-red-400 hover:text-red-600">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
        @endif

        @if (session('info'))
            <div class="flex items-start gap-3 bg-white border border-blue-200 rounded-xl px-4 py-3 shadow-lg">
                <div class="flex-shrink-0 w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                    <i data-lucide="info" class="w-4 h-4 text-blue-600"></i>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-semibold text-blue-800">Info</p>
                    <p class="text-sm text-blue-700">{{ session('info') }}</p>
                </div>
                <button @click="show = false" class="text-blue-400 hover:text-blue-600">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
        @endif
    </div>
@endif

@if ($errors->any())
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4" x-data="{ show: true }" x-show="show" x-transition>
        <div class="bg-red-50 border border-red-200 rounded-xl p-4">
            <div class="flex items-center gap-2 mb-2">
                <i data-lucide="alert-triangle" class="w-4 h-4 text-red-600"></i>
                <p class="text-sm font-semibold text-red-800">Please fix the following errors:</p>
            </div>
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li class="text-sm text-red-700">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif
