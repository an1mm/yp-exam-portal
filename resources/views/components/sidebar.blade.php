@php
    $user = auth()->user();
    $isLecturer = $user->isLecturer();
    $isStudent = $user->isStudent();
    
    $currentRoute = request()->route()->getName();
    
    $lecturerMenu = [
        [
            'name' => 'Dashboard',
            'route' => 'lecturer.dashboard',
            'icon' => 'home',
            'active' => $currentRoute === 'lecturer.dashboard'
        ],
        [
            'name' => 'My Subjects',
            'route' => 'lecturer.subjects.index',
            'icon' => 'book',
            'active' => str_starts_with($currentRoute, 'lecturer.subjects')
        ],
        [
            'name' => 'My Classes',
            'route' => 'lecturer.classes.index',
            'icon' => 'building',
            'active' => str_starts_with($currentRoute, 'lecturer.classes')
        ],
        [
            'name' => 'Exams',
            'icon' => 'document-text',
            'submenu' => [
                [
                    'name' => 'Create Exam',
                    'route' => 'lecturer.exams.create',
                    'active' => $currentRoute === 'lecturer.exams.create'
                ],
                [
                    'name' => 'My Exams',
                    'route' => 'lecturer.exams.index',
                    'active' => in_array($currentRoute, ['lecturer.exams.index', 'lecturer.exams.show', 'lecturer.exams.edit'])
                ]
            ],
            'active' => str_starts_with($currentRoute, 'lecturer.exams')
        ]
    ];
    
    $studentMenu = [
        [
            'name' => 'Dashboard',
            'route' => 'student.dashboard',
            'icon' => 'home',
            'active' => $currentRoute === 'student.dashboard'
        ],
        [
            'name' => 'My Subjects',
            'route' => 'student.subjects.index',
            'icon' => 'book',
            'active' => $currentRoute === 'student.subjects.index'
        ],
        [
            'name' => 'My Exams',
            'route' => 'student.exams.index',
            'icon' => 'document-text',
            'active' => in_array($currentRoute, ['student.exams.index', 'student.exams.show', 'student.exams.take'])
        ],
        [
            'name' => 'Exam History',
            'route' => 'student.exams.history',
            'icon' => 'clock',
            'active' => in_array($currentRoute, ['student.exams.history', 'student.exams.results', 'student.exams.detailed-results'])
        ]
    ];
    
    $menuItems = $isLecturer ? $lecturerMenu : $studentMenu;
    
    $openSubmenu = null;
    foreach($menuItems as $item) {
        if(isset($item['submenu']) && $item['active']) {
            $openSubmenu = $item['name'];
            break;
        }
    }
@endphp

<div x-data="{ openSubmenu: @js($openSubmenu), sidebarOpen: false }" class="flex h-screen bg-gray-100 overflow-hidden" style="height: 100vh;">
    <!-- Sidebar -->
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" 
           class="fixed top-0 bottom-0 left-0 z-50 bg-white shadow-lg transform transition-transform duration-300 ease-in-out lg:translate-x-0 lg:fixed lg:z-auto"
           style="height: 100vh; width: 300px;">
        <div class="flex flex-col h-full" style="min-height: 100vh;">
            <!-- Logo Header -->
            <div class="flex items-center h-16 px-6 border-b border-gray-200 flex-shrink-0">
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-2">
                    <img src="{{ asset('images/logo.jpg') }}" alt="Yayasan Peneraju" class="h-6 w-auto object-contain">
                    <span class="text-base font-bold text-gray-800">Exam Portal</span>
                </a>
            </div>

            <!-- Navigation Menu -->
            <nav class="flex-1 px-4 py-4 space-y-2 overflow-y-auto">
                @foreach($menuItems as $item)
                    @if(isset($item['submenu']))
                        <div>
                            <button @click="openSubmenu = openSubmenu === '{{ $item['name'] }}' ? null : '{{ $item['name'] }}'"
                                    class="w-full flex items-center justify-between px-4 py-3 text-sm font-medium rounded-lg transition-colors duration-200 {{ $item['active'] ? 'bg-indigo-50 text-indigo-600 border-l-4 border-indigo-600' : 'text-gray-700 hover:bg-gray-100' }}">
                                <div class="flex items-center space-x-3">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        @if($item['icon'] === 'document-text')
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        @endif
                                    </svg>
                                    <span>{{ $item['name'] }}</span>
                                </div>
                                <svg :class="openSubmenu === '{{ $item['name'] }}' ? 'rotate-180' : ''" 
                                     class="w-4 h-4 transition-transform duration-200" 
                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <div x-show="openSubmenu === '{{ $item['name'] }}'" 
                                 x-transition
                                 class="mt-2 ml-8 space-y-1">
                                @foreach($item['submenu'] as $subitem)
                                    <a href="{{ route($subitem['route']) }}"
                                       class="block px-4 py-2 text-sm rounded-lg transition-colors duration-200 {{ $subitem['active'] ? 'bg-indigo-50 text-indigo-600 font-medium' : 'text-gray-600 hover:bg-gray-100' }}">
                                        {{ $subitem['name'] }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <a href="{{ route($item['route']) }}"
                           class="flex items-center space-x-3 px-4 py-3 text-sm font-medium rounded-lg transition-colors duration-200 {{ $item['active'] ? 'bg-indigo-50 text-indigo-600 border-l-4 border-indigo-600' : 'text-gray-700 hover:bg-gray-100' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                @if($item['icon'] === 'home')
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                @elseif($item['icon'] === 'user')
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                @elseif($item['icon'] === 'document-text')
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                @elseif($item['icon'] === 'book')
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                @elseif($item['icon'] === 'building')
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                @elseif($item['icon'] === 'clock')
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                @endif
                            </svg>
                            <span>{{ $item['name'] }}</span>
                        </a>
                    @endif
                @endforeach
            </nav>
        </div>
    </aside>

    <!-- Mobile Overlay -->
    <div x-show="sidebarOpen" 
         @click="sidebarOpen = false"
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-40 bg-gray-600 bg-opacity-75 lg:hidden"></div>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col overflow-hidden" style="margin-left: 300px; height: 100vh;">
        <!-- Top Header -->
        <header class="bg-white shadow-sm border-b border-gray-200 flex-shrink-0 h-16">
            <div class="flex items-center justify-between h-full px-4 lg:px-6">
                <!-- Page Title -->
                <div class="flex-1 lg:ml-0">
                    <h1 class="text-xl font-semibold text-gray-800">
                        @yield('page-title', 'Dashboard')
                    </h1>
                </div>

                <!-- User Menu -->
                @include('partials.user-menu')
            </div>
        </header>

        <!-- Page Header (Optional - like template) -->
        @hasSection('content_header')
            <div class="bg-white border-b border-gray-200 flex-shrink-0">
                <div class="px-4 py-4 lg:px-6">
                    @yield('content_header')
                </div>
            </div>
        @endif

        <!-- Main Content -->
        <main class="flex-1 overflow-y-auto bg-gray-50" style="min-height: 0; height: 0;">
            <div class="max-w-7xl mx-auto px-4 py-6 lg:px-6">
                @if (session('success'))
                    <div class="mb-4 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-4 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
                        {{ session('error') }}
                    </div>
                @endif

                @yield('content')
            </div>
        </main>
    </div>
</div>
