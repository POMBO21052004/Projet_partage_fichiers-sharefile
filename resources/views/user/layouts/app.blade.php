<!DOCTYPE html>
<html lang="fr" class="light icons-loading">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mon Espace - Share File</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        'primary':                    '#00488d',
                        'primary-container':          '#005fb8',
                        'primary-fixed':              '#d6e3ff',
                        'surface':                    '#f8f9ff',
                        'on-surface':                 '#0d1c2e',
                        'surface-container-low':      '#eff4ff',
                        'surface-container-lowest':   '#ffffff',
                        'surface-container-highest':  '#d5e3fc',
                        'outline':                    '#727783',
                        'outline-variant':            '#c2c6d4',
                        'secondary-container':        '#c0d5ff',
                    }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap"
        rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @livewireStyles

    <script>
        (function () {
            const savedTheme = localStorage.getItem('theme') || 'light';
            if (savedTheme === 'dark') {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }

            const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
            if (isCollapsed) {
                document.documentElement.classList.add('sidebar-is-collapsed');
            }
        })();
    </script>

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }

        .glass-card {
            background: rgba(213, 227, 252, 0.8);
            backdrop-filter: blur(12px);
        }

        .dark .glass-card {
            background: rgba(15, 23, 42, 0.8);
        }

        @keyframes slideInUp {
            from {
                transform: translateY(100%);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .toast-animate {
            animation: slideInUp 0.3s ease-out forwards;
        }

        @media (min-width: 1280px) {
            html.sidebar-is-collapsed aside#main-sidebar {
                width: 5rem !important;
            }

            html.sidebar-is-collapsed aside#main-sidebar [x-show="!sidebarCollapsed"] {
                display: none !important;
            }
        }

        [x-cloak] {
            display: none !important;
        }

        /* --- Icon loading skeleton --- */
        .icons-loading .material-symbols-outlined {
            font-size: 0 !important;
            display: inline-block;
            width: 18px;
            height: 18px;
            background: currentColor;
            border-radius: 50%;
            opacity: 0.25;
            vertical-align: middle;
            animation: icon-pulse 1.4s ease-in-out infinite;
        }

        @keyframes icon-pulse {
            0%, 100% { opacity: 0.25; transform: scale(1); }
            50%       { opacity: 0.1;  transform: scale(0.85); }
        }
    </style>
    <script>
        document.fonts.load("1em 'Material Symbols Outlined'").then(function() {
            document.documentElement.classList.remove('icons-loading');
        });
    </script>
</head>

<body
    class="bg-surface dark:bg-slate-950 text-on-surface dark:text-slate-200 transition-colors duration-300 overflow-hidden"
    x-data="{ 
        sidebarOpen: false, 
        sidebarCollapsed: localStorage.getItem('sidebarCollapsed') === 'true',
        toggleSidebar() {
            this.sidebarCollapsed = !this.sidebarCollapsed;
            localStorage.setItem('sidebarCollapsed', this.sidebarCollapsed);
            if (this.sidebarCollapsed) document.documentElement.classList.add('sidebar-is-collapsed');
            else document.documentElement.classList.remove('sidebar-is-collapsed');
        }
      }">

    <!-- Conteneur de Toasts (Bas à Droite) -->
    <div id="toast-container" class="fixed bottom-6 right-6 z-[100] flex flex-col gap-3"></div>

    <div class="flex h-screen overflow-hidden">
        <!-- Mobile Sidebar Overlay -->
        <div x-show="sidebarOpen" x-cloak @click="sidebarOpen = false"
            x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-40 bg-slate-900/50 backdrop-blur-sm xl:hidden">
        </div>

        <!-- SideNavBar -->
        <aside id="main-sidebar"
            :class="[sidebarOpen ? 'translate-x-0' : '-translate-x-full', sidebarCollapsed ? 'xl:w-20' : 'xl:w-64']"
            class="fixed inset-y-0 left-0 z-50 h-full w-64 flex flex-col bg-surface-container-low dark:bg-slate-900 border-r border-outline-variant/10 dark:border-slate-800 shrink-0 transition-all duration-300 xl:static xl:translate-x-0 -translate-x-full shadow-lg xl:shadow-none">

            <!-- Sidebar Header -->
            <div class="px-6 py-4 flex items-center justify-between gap-3 border-b border-outline-variant/5">
                <div class="flex items-center gap-3 overflow-hidden">
                    <div
                        class="w-10 h-10 bg-gradient-to-br from-primary to-primary-container rounded-2xl flex items-center justify-center text-white shadow-lg shadow-primary/20 shrink-0">
                        <span class="material-symbols-outlined text-2xl"
                            style="font-variation-settings: 'FILL' 1;">shield</span>
                    </div>
                    <div x-show="!sidebarCollapsed" x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 -translate-x-2"
                        x-transition:enter-end="opacity-100 translate-x-0" class="flex flex-col min-w-0">
                        <h2
                            class="text-xl font-black text-on-surface dark:text-white tracking-tighter leading-none whitespace-nowrap">
                            Share File</h2>
                        <p
                            class="text-[9px] text-primary dark:text-blue-400 font-black tracking-[0.2em] uppercase mt-1 leading-none whitespace-nowrap">
                            Espace Utilisateur</p>
                    </div>
                </div>

                <button @click="toggleSidebar()"
                    class="hidden xl:flex items-center justify-center w-8 h-8 rounded-xl hover:bg-white dark:hover:bg-slate-800 transition-colors text-slate-400 hover:text-primary">
                    <span class="material-symbols-outlined text-lg"
                        :class="sidebarCollapsed ? '' : 'rotate-180'">menu_open</span>
                </button>
            </div>

            <nav class="flex-1 px-4 space-y-6 mt-4 overflow-y-auto custom-scrollbar flex flex-col">
                <!-- Groupe 1: Vue d'ensemble -->
                <div>
                    <h3 x-show="!sidebarCollapsed"
                        class="px-4 text-[10px] font-black uppercase tracking-[0.2em] text-outline/50 dark:text-slate-500 mb-3">
                        Vue d'ensemble</h3>
                    <div x-show="sidebarCollapsed" class="w-full h-px bg-outline-variant/10 my-3"></div>
                    <div class="space-y-1">
                        <a href="{{ route('user.dashboard') }}"
                            class="group flex items-center {{ request()->routeIs('user.dashboard') ? 'bg-primary text-white shadow-md shadow-primary/20' : 'text-slate-500 dark:text-slate-400 hover:bg-white dark:hover:bg-slate-800' }} transition-all duration-200 rounded-2xl"
                            :class="sidebarCollapsed ? 'justify-center p-3' : 'gap-3 px-4 py-3'">
                            <span
                                class="material-symbols-outlined text-lg {{ request()->routeIs('user.dashboard') ? '' : 'group-hover:text-primary dark:group-hover:text-blue-400' }}">dashboard</span>
                            <span x-show="!sidebarCollapsed"
                                class="font-bold text-xs uppercase tracking-widest whitespace-nowrap">Dashboard</span>
                        </a>
                    </div>
                </div>

                <!-- Groupe 2: Gestion des Fichiers -->
                <div>
                    <h3 x-show="!sidebarCollapsed"
                        class="px-4 text-[10px] font-black uppercase tracking-[0.2em] text-outline/50 dark:text-slate-500 mb-3">
                        Mes Fichiers</h3>
                    <div x-show="sidebarCollapsed" class="w-full h-px bg-outline-variant/10 my-3"></div>
                    <div class="space-y-1">
                        <a href="{{ route('user.explorer') }}"
                            class="group flex items-center {{ request()->routeIs('user.explorer*') ? 'bg-primary text-white shadow-md shadow-primary/20' : 'text-slate-500 dark:text-slate-400 hover:bg-white dark:hover:bg-slate-800' }} transition-all duration-200 rounded-2xl"
                            :class="sidebarCollapsed ? 'justify-center p-3' : 'gap-3 px-4 py-3'">
                            <span
                                class="material-symbols-outlined text-lg {{ request()->routeIs('user.explorer*') ? '' : 'group-hover:text-primary dark:group-hover:text-blue-400' }}">folder_shared</span>
                            <span x-show="!sidebarCollapsed"
                                class="font-bold text-xs uppercase tracking-widest whitespace-nowrap">Coffre-fort</span>
                        </a>
                    </div>
                </div>
            </nav>

            <!-- Carte Utilisateur -->
            <div class="p-4 mt-auto">
                <a href="{{ route('user.profile.show') }}"
                    class="block bg-white dark:bg-slate-800 rounded-2xl shadow-sm ring-1 ring-black/5 dark:ring-white/5 hover:ring-primary/50 transition-all group/card"
                    :class="sidebarCollapsed ? 'p-2' : 'p-4'">
                    <div class="flex items-center gap-3" :class="sidebarCollapsed ? 'justify-center' : ''">
                        <div
                            class="w-10 h-10 rounded-xl overflow-hidden ring-2 ring-primary-fixed dark:ring-blue-500 group-hover/card:scale-105 transition-transform shrink-0">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=00488d&color=fff"
                                class="w-full h-full object-cover">
                        </div>
                        <div x-show="!sidebarCollapsed" class="flex flex-col min-w-0">
                            <span
                                class="text-xs font-black text-on-surface dark:text-white truncate group-hover/card:text-primary transition-colors">{{ auth()->user()->name }}</span>
                            <span
                                class="text-[9px] text-outline dark:text-slate-500 uppercase font-bold tracking-widest">Mon
                                Profil</span>
                        </div>
                    </div>
                </a>
                <div class="mt-3">
                    <form action="{{ route('logout') }}" method="POST" class="m-0">
                        @csrf
                        <button type="submit"
                            class="w-full flex items-center justify-center gap-2 py-2.5 bg-rose-50 dark:bg-rose-900/10 text-rose-600 dark:text-rose-400 rounded-xl text-[10px] font-black uppercase tracking-[0.1em] hover:bg-rose-100 dark:hover:bg-rose-900/20 transition-all"
                            :class="sidebarCollapsed ? 'px-0' : ''">
                            <span class="material-symbols-outlined text-sm">logout</span>
                            <span x-show="!sidebarCollapsed">Déconnexion</span>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col h-full overflow-hidden bg-surface dark:bg-slate-950">
            <!-- TopNavBar (Réduite, Flottante, Sans Recherche) -->
            <div class="px-4 sm:px-8 pt-4 pb-2">
                <header
                    class="w-full bg-white dark:bg-slate-900 flex justify-between items-center px-4 sm:px-8 py-3 rounded-2xl shadow-sm ring-1 ring-black/5 dark:ring-white/10 shrink-0">

                    <div class="flex items-center gap-3 flex-1 min-w-0">
                        <!-- Hamburger (visible < xl) -->
                        <button @click="sidebarOpen = !sidebarOpen"
                            class="p-2 rounded-xl hover:bg-primary/5 dark:hover:bg-blue-900/20 transition-colors xl:hidden text-slate-500 dark:text-slate-400 shrink-0">
                            <span class="material-symbols-outlined text-xl">menu</span>
                        </button>

                        <!-- Mobile : avatar + nom complet (visible < xl) -->
                        <div class="flex xl:hidden items-center gap-2 min-w-0">
                            <div class="w-8 h-8 rounded-xl overflow-hidden ring-2 ring-white dark:ring-slate-900 shrink-0">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=00488d&color=fff"
                                    class="w-full h-full object-cover">
                            </div>
                            <span class="text-sm font-bold text-on-surface dark:text-white truncate max-w-[160px]">{{ auth()->user()->name }}</span>
                        </div>

                        <!-- Desktop : titre + sous-titre (visible >= xl) -->
                        <div class="hidden xl:block min-w-0">
                            <h1 class="text-base font-black text-on-surface dark:text-white tracking-tight truncate">
                                @yield('title')</h1>
                            <p class="text-[9px] font-black text-outline dark:text-slate-500 uppercase tracking-[0.2em] leading-none mt-0.5 truncate">
                                Espace Utilisateur</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 sm:gap-4">
                        <!-- Bouton Thème -->
                        <button onclick="toggleDarkMode()"
                            class="p-2 text-slate-500 dark:text-slate-400 hover:bg-primary/5 dark:hover:bg-blue-900/20 transition-all group rounded-xl">
                            <span class="material-symbols-outlined text-xl dark:hidden group-hover:text-primary">dark_mode</span>
                            <span class="material-symbols-outlined text-xl hidden dark:block group-hover:text-blue-400">light_mode</span>
                        </button>
                    </div>
                </header>
            </div>

            <!-- Page Content (Défilable verticalement) -->
            <main class="flex-1 overflow-y-auto px-8 py-6 custom-scrollbar">
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Scripts Généraux -->
    <script>
        function toggleDarkMode() {
            const html = document.documentElement;
            if (html.classList.contains('dark')) {
                html.classList.remove('dark');
                localStorage.setItem('theme', 'light');
            } else {
                html.classList.add('dark');
                localStorage.setItem('theme', 'dark');
            }
        }

        // Système global de notifications Toast (Stylisé avec animations physiques)
        function showToast(message, type = 'success') {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');

            const config = {
                success: { bg: 'bg-emerald-500', icon: 'check_circle' },
                error: { bg: 'bg-rose-500', icon: 'error' },
                warning: { bg: 'bg-amber-500', icon: 'warning' }
            }[type] || { bg: 'bg-blue-500', icon: 'info' };

            toast.className = `toast-animate flex items-center gap-3 px-4 py-3 rounded-2xl text-white shadow-xl ${config.bg} max-w-sm shrink-0`;
            toast.innerHTML = `
                <span class="material-symbols-outlined text-xl">${config.icon}</span>
                <span class="text-xs font-bold leading-tight">${message}</span>
            `;

            container.appendChild(toast);
            setTimeout(() => {
                toast.classList.add('opacity-0', 'transition-opacity', 'duration-300');
                setTimeout(() => toast.remove(), 300);
            }, 4000);
        }

        // Afficher les flash messages s'ils existent
        @if(session('success'))
            showToast("{{ session('success') }}", "success");
        @endif
        @if(session('error'))
            showToast("{{ session('error') }}", "error");
        @endif
    </script>
</body>

</html>