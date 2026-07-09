<!DOCTYPE html>
<html lang="pt-BR" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Precificação') — Módulo 7</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full">

    <div class="flex h-full">

        {{-- ═══════ Sidebar ═══════ --}}
        <aside id="sidebar" class="sidebar w-64 flex flex-col shrink-0 relative overflow-hidden transition-all duration-300">
            {{-- Logo --}}
            <div class="sidebar-logo relative z-10 h-[72px] flex items-center px-6 border-b border-surface-200/60 transition-all duration-300">
                <div class="flex items-center gap-3 w-full">
                    <div class="w-9 h-9 shrink-0 rounded-xl flex items-center justify-center font-extrabold text-sm text-white" style="background: linear-gradient(135deg, #2563eb 0%, #60a5fa 100%); box-shadow: 0 2px 10px rgba(37,99,235,0.4);">
                        M7
                    </div>
                    <div class="sidebar-text whitespace-nowrap overflow-hidden transition-opacity duration-300">
                        <p class="text-surface-900 font-semibold text-sm tracking-tight">Precificação</p>
                        <p class="text-[10px] text-surface-500 font-medium tracking-wider uppercase">Módulo 7</p>
                    </div>
                </div>
            </div>

            {{-- Seção --}}
            <div class="sidebar-text relative z-10 px-4 pt-6 pb-2 transition-opacity duration-300 whitespace-nowrap overflow-hidden">
                <p class="px-3 text-[10px] font-bold text-surface-400 uppercase tracking-[0.15em]">Menu</p>
            </div>

            {{-- Nav --}}
            <nav class="relative z-10 flex-1 px-4 space-y-1">
                @php
                    $nav = [
                        ['propostas.index',  'Propostas',           'propostas'],
                        ['bdi.index',        'Calculadora BDI',     'bdi'],
                        ['historico.index',  'Histórico de Custos', 'historico'],
                    ];
                @endphp
                @foreach($nav as [$route, $label, $iconKey])
                    @php
                        $isActive = request()->routeIs($route) || request()->routeIs(str_replace('.index', '.*', $route));
                    @endphp
                    <a href="{{ route($route) }}"
                       class="sidebar-nav-item {{ $isActive ? 'active' : '' }}">
                        <span class="sidebar-nav-icon">
                            @switch($iconKey)
                                @case('propostas')
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" />
                                    </svg>
                                @break
                                @case('bdi')
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 15.75V18m-7.5-6.75h.008v.008H8.25v-.008zm0 2.25h.008v.008H8.25V13.5zm0 2.25h.008v.008H8.25v-.008zm0 2.25h.008v.008H8.25V18zm2.498-6.75h.007v.008h-.007v-.008zm0 2.25h.007v.008h-.007V13.5zm0 2.25h.007v.008h-.007v-.008zm0 2.25h.007v.008h-.007V18zm2.504-6.75h.008v.008h-.008v-.008zm0 2.25h.008v.008h-.008V13.5zm0 2.25h.008v.008h-.008v-.008zm0 2.25h.008v.008h-.008V18zm2.498-6.75h.008v.008h-.008v-.008zm0 2.25h.008v.008h-.008V13.5zM8.25 6h7.5v2.25h-7.5V6zM12 2.25c-1.892 0-3.758.11-5.593.322C5.307 2.7 4.5 3.65 4.5 4.757V19.5a2.25 2.25 0 002.25 2.25h10.5a2.25 2.25 0 002.25-2.25V4.757c0-1.108-.806-2.057-1.907-2.185A48.507 48.507 0 0012 2.25z" />
                                    </svg>
                                @break
                                @case('historico')
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                @break
                            @endswitch
                        </span>
                        <span class="sidebar-text whitespace-nowrap overflow-hidden transition-opacity duration-300" title="{{ $label }}">{{ $label }}</span>
                    </a>
                @endforeach
            </nav>

            {{-- Footer --}}
            <div class="sidebar-footer relative z-10 px-5 py-4 border-t border-surface-200/60 mt-auto flex items-center gap-3 transition-all duration-300">
                <div class="w-8 h-8 shrink-0 rounded-lg flex items-center justify-center text-[10px] font-bold text-primary-700 bg-primary-50">
                    M7
                </div>
                <div class="sidebar-text whitespace-nowrap overflow-hidden transition-opacity duration-300">
                    <p class="text-xs text-surface-700 font-medium">Precificação</p>
                    <p class="text-[10px] text-surface-500">Módulo 7</p>
                </div>
            </div>
        </aside>

        {{-- ═══════ Main ═══════ --}}
        <main class="flex-1 flex flex-col overflow-auto bg-surface-100">
            {{-- Top bar --}}
            <header class="topbar h-[72px] flex items-center px-6 lg:px-8 sticky top-0 z-30">
                <button id="toggle-sidebar" class="mr-5 p-2 rounded-lg text-surface-500 hover:text-surface-800 hover:bg-surface-100 transition-colors focus:outline-none">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>
                </button>
                <div>
                    <h1 class="text-base font-bold text-surface-800 tracking-tight">@yield('page-title', 'Dashboard')</h1>
                    @hasSection('page-subtitle')
                        <p class="text-xs text-surface-400 mt-0.5">@yield('page-subtitle')</p>
                    @endif
                </div>
                <div class="ml-auto flex items-center gap-3">
                    @yield('header-actions')
                </div>
            </header>

            {{-- Flash messages --}}
            @if(session('success'))
                <div class="mx-8 mt-5">
                    <div class="flash-success">
                        <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        {{ session('success') }}
                    </div>
                </div>
            @endif
            @if($errors->has('geral'))
                <div class="mx-8 mt-5">
                    <div class="flash-error">
                        <svg class="w-5 h-5 text-red-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                        </svg>
                        {{ $errors->first('geral') }}
                    </div>
                </div>
            @endif

            {{-- Content --}}
            <div class="flex-1 p-8 dot-pattern">
                @yield('content')
            </div>
        </main>
    </div>

    @stack('scripts')
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const toggleBtn = document.getElementById('toggle-sidebar');
            
            // Check local storage for preference
            if (localStorage.getItem('sidebar-collapsed') === 'true') {
                sidebar.classList.add('collapsed');
                sidebar.classList.remove('w-64');
            }

            toggleBtn.addEventListener('click', function() {
                sidebar.classList.toggle('collapsed');
                
                if (sidebar.classList.contains('collapsed')) {
                    sidebar.classList.remove('w-64');
                    localStorage.setItem('sidebar-collapsed', 'true');
                } else {
                    sidebar.classList.add('w-64');
                    localStorage.setItem('sidebar-collapsed', 'false');
                }
            });
        });
    </script>
</body>
</html>
