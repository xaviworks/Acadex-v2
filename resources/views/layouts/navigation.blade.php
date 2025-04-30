<header class="bg-indigo-600 text-white px-6 py-4 shadow-md flex justify-between items-center">
    <!-- Left: Title or brand -->
    <h1 class="text-lg font-semibold">Dashboard</h1>

    <!-- Right: Profile & Notifications -->
    <div class="flex items-center gap-4">
        <button class="relative">
            🔔
            <span class="absolute -top-1 -right-1 bg-red-500 text-xs px-1.5 py-0.5 rounded-full">3</span>
        </button>

        <div class="flex items-center gap-2">
            <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}"
                 alt="avatar"
                 class="h-8 w-8 rounded-full object-cover">
            <span class="text-sm">{{ Auth::user()->name }}</span>
        </div>
    </div>
</header>
