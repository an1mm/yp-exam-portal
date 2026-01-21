@php
    $user = auth()->user();
    $isLecturer = $user->isLecturer();
    $isStudent = $user->isStudent();
@endphp

<div class="flex items-center space-x-4">
    <x-dropdown align="right" width="48">
        <x-slot name="trigger">
            <button class="flex items-center space-x-2 text-sm font-medium text-gray-700 hover:text-gray-900 focus:outline-none transition-colors duration-200">
                <!-- User Name -->
                <span class="text-sm font-semibold text-gray-800">{{ $user->name }}</span>
                
                <!-- Dropdown Arrow -->
                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>
        </x-slot>

        <x-slot name="content">
            <!-- Role Badge -->
            <div class="px-4 py-2 border-b border-gray-200">
                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $isLecturer ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700' }}">
                    {{ ucfirst($user->role) }}
                </span>
            </div>
            
            <!-- Profile Link -->
            <x-dropdown-link :href="route('profile.edit')">
                {{ __('Profile') }}
            </x-dropdown-link>

            <!-- Logout Form -->
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <x-dropdown-link :href="route('logout')"
                        onclick="event.preventDefault(); this.closest('form').submit();">
                    {{ __('Log Out') }}
                </x-dropdown-link>
            </form>
        </x-slot>
    </x-dropdown>
</div>
