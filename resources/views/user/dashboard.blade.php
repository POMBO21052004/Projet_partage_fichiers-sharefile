@extends('user.layouts.app')

@section('title', 'Tableau de bord')

@section('content')
<div class="space-y-6">

    <!-- Salutation Dynamique -->
    @php
        $hour = date('H');
        if ($hour >= 0 && $hour < 12) {
            $greeting = "Bonjour";
        } elseif ($hour >= 12 && $hour < 14) {
            $greeting = "Bon après-midi";
        } else {
            $greeting = "Bonsoir";
        }
    @endphp
    <div>
        <h2 class="text-2xl font-black text-on-surface dark:text-white tracking-tight">
            {{ $greeting }}, {{ auth()->user()->name }} !
        </h2>
        <p class="text-xs text-outline dark:text-slate-400 font-medium mt-1">
            Bienvenue dans votre coffre-fort hautement sécurisé. Voici le récapitulatif de votre espace de stockage et de vos partages actifs.
        </p>
    </div>

    {{-- Stats Cards (Même dimension et design que l'admin) --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Mes Fichiers -->
        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl shadow-sm ring-1 ring-black/5 dark:ring-white/10 transition-all hover:shadow-md hover:ring-primary/50 group">
            <div class="flex justify-between items-start mb-2">
                <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center text-primary group-hover:scale-110 transition-transform">
                    <span class="material-symbols-outlined text-xl" style="font-variation-settings: 'FILL' 1;">description</span>
                </div>
                <a href="{{ route('user.explorer') }}" class="w-8 h-8 rounded-lg bg-surface-container-low dark:bg-slate-800 flex items-center justify-center text-primary hover:bg-primary hover:text-white transition-all shadow-sm" title="Explorer">
                    <span class="material-symbols-outlined text-sm">arrow_forward</span>
                </a>
            </div>
            <p class="text-[9px] uppercase tracking-widest text-outline dark:text-slate-500 font-black">Mes Fichiers</p>
            <h3 class="text-xl font-black text-on-surface dark:text-white tracking-tighter mt-0.5">{{ number_format($stats['my_files_count']) }}</h3>
        </div>

        <!-- Partagés avec Moi -->
        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl shadow-sm ring-1 ring-black/5 dark:ring-white/10 transition-all hover:shadow-md hover:ring-emerald-500/50 group">
            <div class="flex justify-between items-start mb-2">
                <div class="w-10 h-10 rounded-xl bg-emerald-500/10 flex items-center justify-center text-emerald-500 group-hover:scale-110 transition-transform">
                    <span class="material-symbols-outlined text-xl" style="font-variation-settings: 'FILL' 1;">folder_shared</span>
                </div>
                <span class="text-[9px] font-black text-emerald-600 bg-emerald-50 dark:bg-emerald-900/20 px-2 py-0.5 rounded-full uppercase tracking-widest">Reçu</span>
            </div>
            <p class="text-[9px] uppercase tracking-widest text-outline dark:text-slate-500 font-black">Partagés avec Moi</p>
            <h3 class="text-xl font-black text-on-surface dark:text-white tracking-tighter mt-0.5">{{ number_format($stats['shared_with_me_count']) }}</h3>
        </div>

        <!-- Stockage utilisé -->
        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl shadow-sm ring-1 ring-black/5 dark:ring-white/10 transition-all hover:shadow-md hover:ring-blue-500/50 group">
            <div class="flex justify-between items-start mb-2">
                <div class="w-10 h-10 rounded-xl bg-blue-500/10 flex items-center justify-center text-blue-500 group-hover:scale-110 transition-transform">
                    <span class="material-symbols-outlined text-xl" style="font-variation-settings: 'FILL' 1;">cloud_done</span>
                </div>
                <span class="text-[9px] font-black text-blue-600 bg-blue-50 dark:bg-blue-900/20 px-2 py-0.5 rounded-full uppercase tracking-widest">Capacité</span>
            </div>
            <p class="text-[9px] uppercase tracking-widest text-outline dark:text-slate-500 font-black">Stockage utilisé</p>
            <h3 class="text-xl font-black text-on-surface dark:text-white tracking-tighter mt-0.5">
                {{ number_format($stats['storage_used'] / 1048576, 2) }} <span class="text-xs font-bold text-outline">Mo</span>
            </h3>
        </div>
    </div>

    <!-- Main Analytics Grid -->
    <div class="grid grid-cols-12 gap-6">
        <!-- Modern Evolution Chart -->
        <div class="col-span-12 lg:col-span-8 bg-white dark:bg-slate-900 p-6 rounded-2xl shadow-sm ring-1 ring-black/5 dark:ring-white/5 flex flex-col">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
                <div>
                    <h4 class="text-lg font-black text-slate-900 dark:text-white tracking-tight">Activité & Occupation</h4>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Suivi annuel des transferts et croissance du coffre-fort</p>
                </div>
                <form action="{{ route('user.dashboard') }}" method="GET">
                    <select name="year" onchange="this.form.submit()" class="bg-surface-container-low dark:bg-slate-800 border-none rounded-lg text-[10px] font-black uppercase tracking-widest text-primary dark:text-blue-400 px-4 py-2 cursor-pointer outline-none ring-1 ring-black/5 dark:ring-white/5">
                        @foreach($years as $y)
                            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>Année {{ $y }}</option>
                        @endforeach
                    </select>
                </form>
            </div>
            <div class="flex-1 min-h-[300px]">
                <div id="storageEvolutionChart"></div>
            </div>
        </div>

        <!-- File Distribution (Donut) -->
        <div class="col-span-12 lg:col-span-4 bg-white dark:bg-slate-900 p-6 rounded-2xl shadow-sm ring-1 ring-black/5 dark:ring-white/5 flex flex-col">
            <h4 class="text-lg font-black text-slate-900 dark:text-white tracking-tight mb-1">Typologie des Actifs</h4>
            <p class="text-xs text-slate-500 dark:text-slate-400 mb-6">Répartition par catégorie de document</p>
            <div id="fileDistributionChart" class="flex-1 flex flex-col justify-center min-h-[300px]"></div>
        </div>
    </div>

    <!-- Bottom Row -->
    <div class="grid grid-cols-12 gap-6">
        {{-- Mes Fichiers Récents --}}
        <div class="col-span-12 lg:col-span-4 bg-white dark:bg-slate-900 rounded-2xl shadow-sm ring-1 ring-black/5 dark:ring-white/5 overflow-hidden flex flex-col justify-between">
            <div>
                <div class="p-5 border-b border-outline-variant/10 dark:border-slate-800 flex items-center justify-between">
                    <h3 class="font-black text-on-surface dark:text-white uppercase tracking-wider text-xs">Mes Uploads Récents</h3>
                    <a href="{{ route('user.explorer') }}" class="text-[10px] font-black text-primary hover:underline uppercase tracking-widest transition-colors">Voir tout</a>
                </div>
                <ul class="divide-y divide-outline-variant/10 dark:divide-slate-800">
                    @forelse($myRecentFiles as $file)
                    <li class="flex items-center gap-4 px-6 py-4 hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-colors">
                        <div class="w-10 h-10 bg-primary/10 rounded-xl flex items-center justify-center text-primary shrink-0">
                            <span class="material-symbols-outlined text-lg">insert_drive_file</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-bold text-sm text-on-surface dark:text-slate-200 truncate" title="{{ $file->name }}">{{ $file->name }}</p>
                            <p class="text-[10px] text-outline dark:text-slate-500 font-bold uppercase tracking-widest mt-0.5">{{ $file->created_at->diffForHumans() }}</p>
                        </div>
                        <a href="{{ route('user.files.download', $file) }}" class="w-8 h-8 rounded-lg bg-surface-container-low dark:bg-slate-800 flex items-center justify-center text-slate-500 dark:text-slate-400 hover:bg-primary hover:text-white transition-all">
                            <span class="material-symbols-outlined text-sm">download</span>
                        </a>
                    </li>
                    @empty
                    <li class="px-6 py-12 text-center text-outline italic text-sm">
                        <span class="material-symbols-outlined text-4xl text-outline-variant/30 block mb-2">cloud_upload</span>
                        Aucun fichier importé pour l'instant.
                    </li>
                    @endforelse
                </ul>
            </div>
        </div>

        {{-- Fichiers Partagés avec Moi --}}
        <div class="col-span-12 lg:col-span-4 bg-white dark:bg-slate-900 rounded-2xl shadow-sm ring-1 ring-black/5 dark:ring-white/5 overflow-hidden flex flex-col justify-between">
            <div>
                <div class="p-5 border-b border-outline-variant/10 dark:border-slate-800 flex items-center justify-between">
                    <h3 class="font-black text-on-surface dark:text-white uppercase tracking-wider text-xs">Partagés avec Moi</h3>
                    <span class="text-[9px] font-black text-emerald-600 bg-emerald-50 dark:bg-emerald-900/20 px-2 py-0.5 rounded-full uppercase tracking-widest">Accès accordé</span>
                </div>
                <ul class="divide-y divide-outline-variant/10 dark:divide-slate-800">
                    @forelse($recentSharedFiles as $file)
                    <li class="flex items-center gap-4 px-6 py-4 hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-colors">
                        <div class="w-10 h-10 bg-emerald-500/10 rounded-xl flex items-center justify-center text-emerald-500 shrink-0">
                            <span class="material-symbols-outlined text-lg">folder_shared</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-bold text-sm text-on-surface dark:text-slate-200 truncate" title="{{ $file->name }}">{{ $file->name }}</p>
                            <p class="text-[10px] text-outline dark:text-slate-500 font-bold uppercase tracking-widest mt-0.5">Partagé par {{ $file->user->name ?? 'Admin' }}</p>
                        </div>
                        @if($file->permissions->where('user_id', Auth::id())->first()?->can_download)
                        <a href="{{ route('user.files.download', $file) }}" class="w-8 h-8 rounded-lg bg-surface-container-low dark:bg-slate-800 flex items-center justify-center text-slate-500 dark:text-slate-400 hover:bg-emerald-500 hover:text-white transition-all">
                            <span class="material-symbols-outlined text-sm">download</span>
                        </a>
                        @else
                        <div class="w-8 h-8 rounded-lg bg-surface-container-low dark:bg-slate-800 flex items-center justify-center text-outline-variant" title="Téléchargement non autorisé">
                            <span class="material-symbols-outlined text-sm">lock</span>
                        </div>
                        @endif
                    </li>
                    @empty
                    <li class="px-6 py-12 text-center text-outline italic text-sm">
                        <span class="material-symbols-outlined text-4xl text-outline-variant/30 block mb-2">share</span>
                        Aucun fichier partagé avec vous.
                    </li>
                    @endforelse
                </ul>
            </div>
        </div>

        <!-- System Security Health -->
        <div class="col-span-12 lg:col-span-4 glass-card p-6 rounded-2xl flex flex-col justify-between border border-white/20 dark:border-slate-800 shadow-xl overflow-hidden relative group">
            <div class="z-10 relative">
                <div class="flex items-center justify-between mb-6">
                    <div class="w-12 h-12 bg-white/40 dark:bg-slate-800/40 backdrop-blur-xl rounded-xl flex items-center justify-center text-primary dark:text-blue-400 shadow-sm">
                        <span class="material-symbols-outlined text-2xl" style="font-variation-settings: 'FILL' 1;">security</span>
                    </div>
                    <span class="px-3 py-1 bg-emerald-500 text-white text-[9px] font-black uppercase tracking-widest rounded-full shadow-lg">Actif</span>
                </div>
                <h3 class="text-lg font-black tracking-tight text-on-surface dark:text-white mb-2">Intégrité & Chiffrement</h3>
                <p class="text-xs text-outline dark:text-slate-400 font-medium leading-relaxed">Vos documents sont sécurisés, chiffrés et sauvegardés de manière transparente sur nos serveurs sécurisés.</p>
            </div>
            
            <div class="z-10 relative mt-6">
                <div class="flex justify-between items-end mb-3">
                    <span class="text-xl font-black tracking-tighter text-on-surface dark:text-white">100.0%</span>
                    <span class="text-[9px] font-black uppercase tracking-widest text-emerald-600 dark:text-emerald-400">Sécurisé</span>
                </div>
                <div class="w-full bg-slate-200 dark:bg-slate-800 h-2 rounded-full overflow-hidden">
                    <div class="bg-emerald-500 h-full w-full"></div>
                </div>
            </div>

            <!-- Decorative Glow -->
            <div class="absolute -bottom-16 -right-16 w-48 h-48 bg-primary/10 dark:bg-blue-500/10 rounded-full blur-3xl group-hover:scale-125 transition-transform duration-700"></div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const isDark = document.documentElement.classList.contains('dark');
    
    // 1. Chart: Storage Evolution
    const evolutionOptions = {
        series: [{
            name: 'Mon Stockage (Mo)',
            data: @json($cumulativeStorage)
        }, {
            name: 'Mes Fichiers Importés',
            data: @json($monthlyUploads)
        }],
        chart: {
            height: 300,
            type: 'area',
            toolbar: { show: false },
            zoom: { enabled: false },
            fontFamily: 'Inter, sans-serif'
        },
        dataLabels: { enabled: false },
        stroke: { curve: 'smooth', width: 3 },
        colors: ['#00488d', '#10b981'],
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.3,
                opacityTo: 0.05,
                stops: [0, 90, 100]
            }
        },
        xaxis: {
            categories: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Jun', 'Jul', 'Aoû', 'Sep', 'Oct', 'Nov', 'Déc'],
            labels: {
                style: {
                    colors: isDark ? '#64748b' : '#727783',
                    fontSize: '10px',
                    fontWeight: 700
                }
            },
            axisBorder: { show: false },
            axisTicks: { show: false }
        },
        yaxis: {
            labels: {
                style: {
                    colors: isDark ? '#64748b' : '#727783',
                    fontSize: '10px',
                    fontWeight: 700
                }
            }
        },
        grid: {
            borderColor: isDark ? '#334155' : '#eff4ff',
            strokeDashArray: 4
        },
        legend: {
            position: 'top',
            horizontalAlign: 'right',
            labels: {
                colors: isDark ? '#cbd5e1' : '#0d1c2e'
            }
        }
    };

    const evolutionChart = new ApexCharts(document.querySelector("#storageEvolutionChart"), evolutionOptions);
    evolutionChart.render();

    // 2. Chart: File Distribution (Donut)
    const distOptions = {
        series: [
            {{ $distribution['docs'] }},
            {{ $distribution['imgs'] }},
            {{ $distribution['zips'] }},
            {{ $distribution['others'] }}
        ],
        chart: {
            type: 'donut',
            height: 300,
            fontFamily: 'Inter, sans-serif'
        },
        labels: ['Documents', 'Images', 'Archives (Zip)', 'Autres'],
        colors: ['#00488d', '#8b5cf6', '#f59e0b', '#64748b'],
        plotOptions: {
            pie: {
                donut: {
                    size: '70%',
                    labels: {
                        show: true,
                        total: {
                            show: true,
                            label: 'Total',
                            formatter: function (w) {
                                return '{{ $stats['my_files_count'] }} Fich.';
                            },
                            style: {
                                fontSize: '14px',
                                fontWeight: 800,
                                color: isDark ? '#ffffff' : '#0d1c2e'
                            }
                        }
                    }
                }
            }
        },
        dataLabels: { enabled: false },
        legend: {
            position: 'bottom',
            labels: {
                colors: isDark ? '#cbd5e1' : '#0d1c2e'
            }
        }
    };

    const distChart = new ApexCharts(document.querySelector("#fileDistributionChart"), distOptions);
    distChart.render();
});
</script>
@endsection

