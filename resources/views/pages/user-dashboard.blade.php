@extends('layouts.home')

@section('content')

<div id="dashboard-wrapper" class="min-h-screen w-full font-['Inter'] bg-gray-50 flex overflow-x-hidden">

    {{-- Backdrop for mobile --}}
    <div 
        id="sidebar-overlay"
        class="fixed inset-0 bg-black/50 z-40 lg:hidden hidden transition-opacity duration-300 pointer-events-none opacity-0"
        onclick="toggleSidebar()"
    ></div>

    {{-- Sidebar --}}
    <aside 
        id="sidebar"
        class="w-64 bg-white border-r border-gray-200 flex flex-col min-h-screen fixed top-0 left-0 z-50 transform lg:translate-x-0 -translate-x-full transition-transform duration-300 ease-in-out shadow-xl lg:shadow-none"
    >

        {{-- Logo --}}
        <div class="px-6 py-5 border-b border-gray-200 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <img src="https://images.vexels.com/media/users/3/137578/isolated/preview/c895a61e637f53ac91d5faf634c84794-cube-logo-geometric-polygonal.png" class="h-8 w-8" />
                <span class="font-bold text-gray-900 text-lg">User Portal</span>
            </div>
            {{-- Close button for mobile only --}}
            <button onclick="toggleSidebar()" class="lg:hidden p-1 text-gray-500 hover:bg-gray-100 rounded-md">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </div>

        {{-- Nav Links --}}
        <nav class="flex-1 px-4 py-6 flex flex-col gap-1">
            {{-- ... existing nav content stays the same, I'll keep the placeholders for now to not break the tool if it expects exact matches later --}}
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest px-3 mb-2">Workspace</p>
            <a href="#" class="flex items-center gap-3 px-3 py-2.5 rounded-lg bg-black text-white text-sm font-semibold shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
                My Dashboard
            </a>
            <a href="#" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-600 hover:bg-gray-100 text-sm font-semibold transition-all">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
                My Tasks
            </a>
            <a href="#" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-600 hover:bg-gray-100 text-sm font-semibold transition-all">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                Profile
            </a>
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest px-3 mt-6 mb-2">Account</p>
            <a href="#" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-600 hover:bg-gray-100 text-sm font-semibold transition-all">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                Settings
            </a>
        </nav>

        {{-- User + Logout --}}
        <div class="px-4 py-4 border-t border-gray-200">
            <div class="flex items-center gap-3 px-3 py-2 mb-2">
                <div class="h-8 w-8 rounded-full bg-black text-white flex items-center justify-center text-xs font-bold shadow-sm">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div class="flex flex-col">
                    <span class="text-sm font-semibold text-gray-900">{{ auth()->user()->name }}</span>
                    <span class="text-xs text-gray-400 capitalize">{{ auth()->user()->role }}</span>
                </div>
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full text-sm font-semibold text-red-500 border border-red-200 bg-red-50 px-4 py-2 rounded-md hover:bg-red-500 hover:text-white transition-all duration-300 cursor-pointer">
                    Log Out
                </button>
            </form>
        </div>

    </aside>

    {{-- Main Content --}}
    <div id="main-content" class="flex-1 flex flex-col lg:ml-64 transition-all duration-300">

        {{-- Top Navbar --}}
        <header class="bg-white border-b border-gray-200 px-4 lg:px-8 py-4 flex items-center justify-between sticky top-0 z-30">
            <div class="flex items-center gap-4">
                {{-- Hamburger Button --}}
                <button onclick="toggleSidebar()" class="p-2 -ml-2 text-gray-500 hover:bg-gray-100 rounded-lg transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>
                </button>
                <div>
                    <h1 class="text-lg font-bold text-gray-900 leading-tight">User Dashboard</h1>
                    <p class="text-xs text-gray-400">Welcome back, {{ auth()->user()->name }}</p>
                </div>
            </div>
            <span class="text-xs text-gray-400 hidden sm:block">{{ now()->format('F d, Y') }}</span>
        </header>

        {{-- Page Content --}}
        <main class="px-8 py-10 max-w-6xl mx-auto w-full">

            {{-- Welcome Section --}}
            <div class="mb-10">
                <div class="bg-black rounded-2xl p-8 text-white relative overflow-hidden shadow-lg">
                    <div class="relative z-10">
                        <h2 class="text-2xl font-bold mb-2">Hello, {{ auth()->user()->name }}!</h2>
                        <p class="text-gray-200 max-w-md">You are logged in as a regular user. Here you can manage your tasks, view your profile, and update your settings.</p>
                        <button class="mt-6 bg-white text-black px-6 py-2 rounded-lg font-bold text-sm shadow-sm hover:bg-gray-100 transition-all">Get Started</button>
                    </div>
                    <div class="absolute right-0 top-0 h-full w-1/3 bg-white/10 -skew-x-12 translate-x-10"></div>
                </div>
            </div>

            {{-- Stats Cards --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-10">

                <div class="bg-white rounded-xl border border-gray-200 p-6 flex flex-col gap-2 shadow-sm">
                    <div class="h-10 w-10 bg-gray-100 text-black flex items-center justify-center rounded-lg mb-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
                    </div>
                    <span class="text-xs font-semibold text-gray-400 uppercase tracking-widest">Active Tasks</span>
                    <span class="text-3xl font-bold text-gray-900">12</span>
                    <span class="text-xs text-black font-semibold">4 due today</span>
                </div>

                <div class="bg-white rounded-xl border border-gray-200 p-6 flex flex-col gap-2 shadow-sm">
                    <div class="h-10 w-10 bg-gray-100 text-black flex items-center justify-center rounded-lg mb-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                    </div>
                    <span class="text-xs font-semibold text-gray-400 uppercase tracking-widest">Completed</span>
                    <span class="text-3xl font-bold text-gray-900">48</span>
                    <span class="text-xs text-green-600 font-semibold truncate">↑ 3 this week</span>
                </div>

                <div class="bg-white rounded-xl border border-gray-200 p-6 flex flex-col gap-2 shadow-sm">
                    <div class="h-10 w-10 bg-gray-100 text-black flex items-center justify-center rounded-lg mb-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                    <span class="text-xs font-semibold text-gray-400 uppercase tracking-widest">Time Logged</span>
                    <span class="text-3xl font-bold text-gray-900">156h</span>
                    <span class="text-xs text-gray-500 font-semibold">Last 30 days</span>
                </div>

            </div>

            {{-- Recent Section --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                {{-- Recent Notifications --}}
                <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-sm font-bold text-gray-900 uppercase tracking-widest">Recent Notifications</h2>
                        <a href="#" class="text-xs font-semibold text-black hover:underline">View all</a>
                    </div>
                    <div class="flex flex-col gap-4">
                        @foreach([
                            ['Project "Redesign" was updated', '10 mins ago', 'bg-blue-100 text-blue-600'],
                            ['New task assigned to you', '2 hours ago', 'bg-gray-100 text-black'],
                            ['Meeting starts in 15 mins', '1 hour ago', 'bg-orange-100 text-orange-600'],
                            ['Password changed successfully', 'Yesterday', 'bg-green-100 text-green-600'],
                        ] as [$text, $time, $style])
                        <div class="flex items-start gap-4 p-3 rounded-lg hover:bg-gray-50 transition-all border border-transparent hover:border-gray-100">
                            <div class="h-8 w-8 rounded-full flex-shrink-0 flex items-center justify-center {{ $style }}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" /></svg>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-sm font-medium text-gray-900">{{ $text }}</span>
                                <span class="text-xs text-gray-400">{{ $time }}</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- My Profile Card --}}
                <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm flex flex-col items-center text-center">
                    <div class="h-24 w-24 rounded-full bg-black text-white flex items-center justify-center text-3xl font-bold mb-4 shadow-md">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <h2 class="text-xl font-bold text-gray-900">{{ auth()->user()->name }}</h2>
                    <p class="text-gray-500 text-sm mb-6">{{ auth()->user()->email }}</p>
                    
                    <div class="w-full grid grid-cols-2 gap-4 border-t border-gray-100 pt-6">
                        <div class="flex flex-col items-center">
                            <span class="text-xs font-semibold text-gray-400 uppercase tracking-widest">Role</span>
                            <span class="text-sm font-bold text-gray-900 bg-gray-100 text-black px-3 py-1 rounded-full mt-1">User</span>
                        </div>
                        <div class="flex flex-col items-center">
                            <span class="text-xs font-semibold text-gray-400 uppercase tracking-widest">Joined</span>
                            <span class="text-sm font-bold text-gray-900 mt-1">{{ auth()->user()->created_at->format('M Y') }}</span>
                        </div>
                    </div>

                    <button class="mt-8 w-full border border-gray-200 text-gray-700 bg-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-gray-50 transition-all">
                        Edit Profile
                    </button>
                </div>

            </div>
        </main>
    </div>

</div>

@endsection

<script>
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebar-overlay');
        const mainContent = document.getElementById('main-content');
        const isDesktop = window.innerWidth >= 1024; // lg breakpoint

        if (isDesktop) {
            // Desktop: Toggle between showing and hiding off-screen
            if (sidebar.classList.contains('lg:translate-x-0')) {
                sidebar.classList.remove('lg:translate-x-0');
                sidebar.classList.add('lg:-translate-x-full');
                mainContent.classList.remove('lg:ml-64');
            } else {
                sidebar.classList.add('lg:translate-x-0');
                sidebar.classList.remove('lg:-translate-x-full');
                mainContent.classList.add('lg:ml-64');
            }
        } else {
            // Mobile: Toggle between hidden and visible
            if (sidebar.classList.contains('-translate-x-full')) {
                sidebar.classList.remove('-translate-x-full');
                sidebar.classList.add('translate-x-0');
                overlay.classList.remove('hidden');
                overlay.classList.add('block', 'opacity-100');
                overlay.classList.remove('pointer-events-none');
            } else {
                sidebar.classList.add('-translate-x-full');
                sidebar.classList.remove('translate-x-0');
                overlay.classList.add('hidden');
                overlay.classList.remove('block', 'opacity-100');
                overlay.classList.add('pointer-events-none');
            }
        }
    }

    // Optional: Close sidebar on mobile when resizing to desktop
    window.addEventListener('resize', () => {
        if (window.innerWidth >= 1024) {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            const mainContent = document.getElementById('main-content');
            
            sidebar.classList.add('lg:translate-x-0');
            sidebar.classList.remove('lg:-translate-x-full', '-translate-x-full', 'translate-x-0');
            overlay.classList.add('hidden');
            mainContent.classList.add('lg:ml-64');
        }
    });
</script>
