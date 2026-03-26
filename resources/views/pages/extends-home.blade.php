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
                <span class="font-bold text-gray-900 text-lg">Dashboard</span>
            </div>
            {{-- Close button for mobile only --}}
            <button onclick="toggleSidebar()" class="lg:hidden p-1 text-gray-500 hover:bg-gray-100 rounded-md">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </div>

        {{-- Nav Links --}}
        <nav class="flex-1 px-4 py-6 flex flex-col gap-1">

            <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest px-3 mb-2">Main</p>

            <a href="#" class="flex items-center gap-3 px-3 py-2.5 rounded-lg bg-black text-white text-sm font-semibold">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
                Overview
            </a>

            <a href="#" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-600 hover:bg-gray-100 text-sm font-semibold transition-all">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
                Tasks
            </a>

            <a href="#" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-600 hover:bg-gray-100 text-sm font-semibold transition-all">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                Users
            </a>

            <a href="#" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-600 hover:bg-gray-100 text-sm font-semibold transition-all">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                Reports
            </a>

            <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest px-3 mt-6 mb-2">System</p>

            <a href="#" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-600 hover:bg-gray-100 text-sm font-semibold transition-all">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                Settings
            </a>

        </nav>

        {{-- User + Logout --}}
        <div class="px-4 py-4 border-t border-gray-200">
            <div class="flex items-center gap-3 px-3 py-2 mb-2">
                <div class="h-8 w-8 rounded-full bg-black text-white flex items-center justify-center text-xs font-bold">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div class="flex flex-col">
                    <span class="text-sm font-semibold text-gray-900">{{ auth()->user()->name }}</span>
                    <span class="text-xs text-gray-400">{{ auth()->user()->role }}</span>
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
                    <h1 class="text-lg font-bold text-gray-900 leading-tight">Overview</h1>
                    <p class="text-xs text-gray-400">Welcome back, {{ auth()->user()->name }}</p>
                </div>
            </div>
            <span class="text-xs text-gray-400 hidden sm:block">{{ now()->format('F d, Y') }}</span>
        </header>

        {{-- Page Content --}}
        <main class="px-8 py-10 max-w-6xl mx-auto w-full">

            {{-- Stats Cards --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">

                <div class="bg-white rounded-xl border border-gray-200 p-6 flex flex-col gap-2">
                    <span class="text-xs font-semibold text-gray-400 uppercase tracking-widest">Total Users</span>
                    <span class="text-3xl font-bold text-gray-900">1,284</span>
                    <span class="text-xs text-green-500 font-semibold">↑ 12% this month</span>
                </div>

                <div class="bg-white rounded-xl border border-gray-200 p-6 flex flex-col gap-2">
                    <span class="text-xs font-semibold text-gray-400 uppercase tracking-widest">Active Sessions</span>
                    <span class="text-3xl font-bold text-gray-900">342</span>
                    <span class="text-xs text-green-500 font-semibold">↑ 8% this week</span>
                </div>

                <div class="bg-white rounded-xl border border-gray-200 p-6 flex flex-col gap-2">
                    <span class="text-xs font-semibold text-gray-400 uppercase tracking-widest">Pending Tasks</span>
                    <span class="text-3xl font-bold text-gray-900">57</span>
                    <span class="text-xs text-red-400 font-semibold">↓ 3 overdue</span>
                </div>

                <div class="bg-white rounded-xl border border-gray-200 p-6 flex flex-col gap-2">
                    <span class="text-xs font-semibold text-gray-400 uppercase tracking-widest">Completed</span>
                    <span class="text-3xl font-bold text-gray-900">921</span>
                    <span class="text-xs text-green-500 font-semibold">↑ 5% this week</span>
                </div>

            </div>

            {{-- Bottom Section --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- Recent Activity --}}
                <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200 p-6">
                    <h2 class="text-sm font-bold text-gray-900 uppercase tracking-widest mb-4">Recent Activity</h2>
                    <ul class="divide-y divide-gray-100">
                        @foreach([
                            ['User John registered', '2 mins ago'],
                            ['Task #42 completed', '15 mins ago'],
                            ['New report generated', '1 hour ago'],
                            ['User Jane logged in', '2 hours ago'],
                            ['System backup done', '5 hours ago'],
                        ] as [$activity, $time])
                        <li class="py-3 flex items-center justify-between">
                            <span class="text-sm text-gray-700">{{ $activity }}</span>
                            <span class="text-xs text-gray-400">{{ $time }}</span>
                        </li>
                        @endforeach
                    </ul>
                </div>

                {{-- Quick Actions --}}
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <h2 class="text-sm font-bold text-gray-900 uppercase tracking-widest mb-4">Quick Actions</h2>
                    <div class="flex flex-col gap-3">
                        <button class="w-full text-sm font-semibold text-white bg-black px-4 py-2.5 rounded-md hover:bg-indigo-600 transition-all duration-300 cursor-pointer">
                            + New Task
                        </button>
                        <button class="w-full text-sm font-semibold text-gray-900 bg-white border border-gray-200 px-4 py-2.5 rounded-md hover:bg-gray-50 transition-all duration-300 cursor-pointer">
                            View Reports
                        </button>
                        <button class="w-full text-sm font-semibold text-gray-900 bg-white border border-gray-200 px-4 py-2.5 rounded-md hover:bg-gray-50 transition-all duration-300 cursor-pointer">
                            Manage Users
                        </button>
                        <button class="w-full text-sm font-semibold text-gray-900 bg-white border border-gray-200 px-4 py-2.5 rounded-md hover:bg-gray-50 transition-all duration-300 cursor-pointer">
                            Settings
                        </button>
                    </div>
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