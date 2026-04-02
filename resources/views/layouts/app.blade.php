<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? config('app.name', 'Briefd') }}</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="font-sans antialiased bg-navy">
        <div class="flex h-screen overflow-hidden"
             x-data="{
                collapsed: localStorage.getItem('sidebar_collapsed') === 'true',
                toggle() {
                    this.collapsed = !this.collapsed;
                    localStorage.setItem('sidebar_collapsed', this.collapsed);
                }
             }">

            {{-- Sidebar --}}
            <aside class="flex flex-col border-r transition-all duration-300 flex-shrink-0"
                   style="background-color: #0f0f18; border-color: #1e1e2e;"
                   :class="collapsed ? 'w-16' : 'w-56'">

                {{-- Logo --}}
                <div class="h-14 flex items-center border-b px-3 flex-shrink-0" style="border-color: #1e1e2e;">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2 overflow-hidden">
                        <div class="w-7 h-7 rounded-md flex items-center justify-center flex-shrink-0" style="background-color: #ff6b2b;">
                            <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <span class="text-white font-bold text-sm tracking-tight transition-opacity duration-200"
                              :class="collapsed ? 'opacity-0 w-0 overflow-hidden' : 'opacity-100'">briefd</span>
                    </a>
                </div>

                {{-- Nav --}}
                <nav class="flex-1 py-4 overflow-y-auto">
                    {{-- Main section --}}
                    <div class="px-2 mb-6">
                        <p class="text-xs font-semibold uppercase tracking-wider px-2 mb-2 transition-opacity duration-200"
                           style="color: #6b6b8a;"
                           :class="collapsed ? 'opacity-0 h-0 overflow-hidden mb-0' : 'opacity-100'">Main</p>

                        @php
                            $navMain = [
                                ['route' => 'dashboard', 'label' => 'Dashboard', 'path' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
                                ['route' => 'digests', 'label' => 'Digests', 'path' => 'M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z'],
                                ['route' => 'scheduled', 'label' => 'Scheduled', 'path' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
                            ];
                            $navManage = [
                                ['route' => 'workspaces', 'label' => 'Workspaces', 'path' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4'],
                                ['route' => 'sources', 'label' => 'Sources', 'path' => 'M6 5c7.18 0 13 5.82 13 13M6 11a7 7 0 017 7m-6 0a1 1 0 11-2 0 1 1 0 012 0z'],
                                ['route' => 'subscribers', 'label' => 'Subscribers', 'path' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z'],
                            ];
                        @endphp

                        @foreach($navMain as $item)
                            @php $active = request()->routeIs($item['route']); @endphp
                            <a href="{{ route($item['route']) }}"
                               class="flex items-center gap-3 px-2 py-2 rounded-lg mb-1 transition-all"
                               style="{{ $active ? 'background-color: #1f1008; color: #ff6b2b;' : 'color: #6b6b8a;' }}"
                               title="{{ $item['label'] }}">
                                <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['path'] }}" />
                                </svg>
                                <span class="text-sm font-medium whitespace-nowrap transition-all duration-200 overflow-hidden"
                                      :class="collapsed ? 'opacity-0 w-0' : 'opacity-100'">{{ $item['label'] }}</span>
                            </a>
                        @endforeach
                    </div>

                    {{-- Manage section --}}
                    <div class="px-2">
                        <p class="text-xs font-semibold uppercase tracking-wider px-2 mb-2 transition-opacity duration-200"
                           style="color: #6b6b8a;"
                           :class="collapsed ? 'opacity-0 h-0 overflow-hidden mb-0' : 'opacity-100'">Manage</p>

                        @foreach($navManage as $item)
                            @php $active = request()->routeIs($item['route']); @endphp
                            <a href="{{ route($item['route']) }}"
                               class="flex items-center gap-3 px-2 py-2 rounded-lg mb-1 transition-all"
                               style="{{ $active ? 'background-color: #1f1008; color: #ff6b2b;' : 'color: #6b6b8a;' }}"
                               title="{{ $item['label'] }}">
                                <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['path'] }}" />
                                </svg>
                                <span class="text-sm font-medium whitespace-nowrap transition-all duration-200 overflow-hidden"
                                      :class="collapsed ? 'opacity-0 w-0' : 'opacity-100'">{{ $item['label'] }}</span>
                            </a>
                        @endforeach
                    </div>
                </nav>

                {{-- Bottom --}}
                <div class="border-t flex-shrink-0" style="border-color: #1e1e2e;">
                    @php $settingsActive = request()->routeIs('settings'); @endphp
                    <a href="{{ route('settings') }}"
                       class="flex items-center gap-3 px-4 py-3 transition-all"
                       style="{{ $settingsActive ? 'color: #ff6b2b;' : 'color: #6b6b8a;' }}"
                       title="Settings">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span class="text-sm font-medium whitespace-nowrap overflow-hidden transition-all duration-200"
                              :class="collapsed ? 'opacity-0 w-0' : 'opacity-100'">Settings</span>
                    </a>

                    <div class="px-3 py-3 border-t flex items-center gap-2 overflow-hidden" style="border-color: #1e1e2e;">
                        <div class="w-7 h-7 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0"
                             style="background-color: #ff6b2b;">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                        <div class="overflow-hidden transition-all duration-200"
                             :class="collapsed ? 'opacity-0 w-0' : 'opacity-100 flex-1 min-w-0'">
                            <p class="text-white text-xs font-medium truncate">{{ auth()->user()->name }}</p>
                            <span class="text-xs px-1.5 py-0.5 rounded font-medium"
                                  style="{{ auth()->user()->plan === 'free' ? 'background-color: #1e1e2e; color: #6b6b8a;' : 'background-color: #1f1008; color: #ff6b2b;' }}">
                                {{ ucfirst(auth()->user()->plan) }}
                            </span>
                        </div>
                    </div>

                    <button @click="toggle()"
                            class="w-full flex items-center justify-center py-2 border-t transition-colors"
                            style="border-color: #1e1e2e; color: #6b6b8a;">
                        <svg class="w-4 h-4 transition-transform duration-300" :class="collapsed ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
                        </svg>
                    </button>
                </div>
            </aside>

            {{-- Main content --}}
            <div class="flex-1 flex flex-col overflow-hidden min-w-0">
                {{-- Topbar --}}
                <header class="h-14 flex items-center justify-between px-6 border-b flex-shrink-0"
                        style="background-color: #0f0f18; border-color: #1e1e2e;">
                    <h1 class="text-white font-semibold text-sm">{{ $title ?? config('app.name', 'Briefd') }}</h1>

                    <div class="flex items-center gap-3">
                        @auth
                        {{-- Workspace switcher --}}
                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open"
                                    class="flex items-center gap-2 px-3 py-1.5 rounded-lg text-sm border transition-colors"
                                    style="background-color: #0a0a0f; border-color: #1e1e2e; color: #6b6b8a;">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                                <span>{{ auth()->user()->currentWorkspace()?->name ?? 'No workspace' }}</span>
                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            <div x-show="open" @click.outside="open = false" x-transition
                                 class="absolute right-0 top-full mt-1 w-52 rounded-lg border shadow-xl z-50 py-1"
                                 style="background-color: #0f0f18; border-color: #1e1e2e;">
                                @foreach(auth()->user()->workspaces as $ws)
                                    <form method="POST" action="{{ route('workspace.switch') }}">
                                        @csrf
                                        <input type="hidden" name="workspace_id" value="{{ $ws->id }}">
                                        <button type="submit"
                                                class="w-full text-left px-3 py-2 text-sm flex items-center gap-2 transition-colors"
                                                style="{{ session('current_workspace_id') == $ws->id ? 'color: #ff6b2b;' : 'color: #6b6b8a;' }}">
                                            @if(session('current_workspace_id') == $ws->id)
                                                <svg class="w-3 h-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                </svg>
                                            @else
                                                <span class="w-3 flex-shrink-0"></span>
                                            @endif
                                            {{ $ws->name }}
                                        </button>
                                    </form>
                                @endforeach
                                <div class="border-t mt-1 pt-1" style="border-color: #1e1e2e;">
                                    <a href="{{ route('workspaces') }}" class="block px-3 py-2 text-sm transition-colors" style="color: #6b6b8a;">
                                        Manage workspaces
                                    </a>
                                </div>
                            </div>
                        </div>

                        {{-- User menu --}}
                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open"
                                    class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-bold"
                                    style="background-color: #ff6b2b;">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </button>
                            <div x-show="open" @click.outside="open = false" x-transition
                                 class="absolute right-0 top-full mt-1 w-44 rounded-lg border shadow-xl z-50 py-1"
                                 style="background-color: #0f0f18; border-color: #1e1e2e;">
                                <a href="{{ route('settings') }}" class="block px-3 py-2 text-sm transition-colors" style="color: #6b6b8a;">Settings</a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-3 py-2 text-sm border-t transition-colors" style="border-color: #1e1e2e; color: #6b6b8a;">Log out</button>
                                </form>
                            </div>
                        </div>
                        @endauth
                    </div>
                </header>

                {{-- Page content --}}
                <main class="flex-1 overflow-y-auto p-6">
                    {{ $slot }}
                </main>
            </div>
        </div>

        @livewireScripts
    </body>
</html>
