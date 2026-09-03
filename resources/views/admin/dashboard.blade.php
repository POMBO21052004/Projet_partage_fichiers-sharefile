@extends('admin.layouts.app')

@section('title', 'Tableau de bord')

@section('content')
<div class="space-y-6">
    <!-- Salutation Dynamique Admin (Sans Carte) -->
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
            Bienvenue dans votre console de contrôle. Voici le récapitulatif opérationnel et l'état général des archives et utilisateurs.
        </p>
    </div>

    <!-- KPI Cards Row (Hauteur réduite) -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <!-- Total Files -->
        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl shadow-sm ring-1 ring-black/5 dark:ring-white/10 transition-all hover:shadow-md">
            <div class="flex justify-between items-start mb-2">
                <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center text-primary">
                    <span class="material-symbols-outlined text-xl" style="font-variation-settings: 'FILL' 1;">description</span>
                </div>
                <span class="text-[9px] font-black text-emerald-600 bg-emerald-50 dark:bg-emerald-900/20 px-2 py-0.5 rounded-full uppercase tracking-widest">+12%</span>
            </div>
            <p class="text-[9px] uppercase tracking-widest text-outline dark:text-slate-500 font-black">Fichiers totaux</p>
            <h3 class="text-xl font-black text-on-surface dark:text-white tracking-tighter mt-0.5">{{ number_format($totalFiles) }}</h3>
        </div>
        <!-- Active Users -->
        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl shadow-sm ring-1 ring-black/5 dark:ring-white/10 transition-all hover:shadow-md">
            <div class="flex justify-between items-start mb-2">
                <div class="w-10 h-10 rounded-xl bg-emerald-500/10 flex items-center justify-center text-emerald-500">
                    <span class="material-symbols-outlined text-xl" style="font-variation-settings: 'FILL' 1;">group</span>
                </div>
                <span class="text-[9px] font-black text-emerald-600 bg-emerald-50 dark:bg-emerald-900/20 px-2 py-0.5 rounded-full uppercase tracking-widest">+4%</span>
            </div>
            <p class="text-[9px] uppercase tracking-widest text-outline dark:text-slate-500 font-black">Utilisateurs actifs</p>
            <h3 class="text-xl font-black text-on-surface dark:text-white tracking-tighter mt-0.5">{{ number_format($totalUsers) }}</h3>
        </div>
        <!-- Total Folders -->
        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl shadow-sm ring-1 ring-black/5 dark:ring-white/10 transition-all hover:shadow-md">
            <div class="flex justify-between items-start mb-2">
                <div class="w-10 h-10 rounded-xl bg-amber-500/10 flex items-center justify-center text-amber-500">
                    <span class="material-symbols-outlined text-xl" style="font-variation-settings: 'FILL' 1;">folder</span>
                </div>
                <span class="text-[9px] font-black text-blue-600 bg-blue-50 dark:bg-blue-900/20 px-2 py-0.5 rounded-full uppercase tracking-widest">Actifs</span>
            </div>
            <p class="text-[9px] uppercase tracking-widest text-outline dark:text-slate-500 font-black">Dossiers totaux</p>
            <h3 class="text-xl font-black text-on-surface dark:text-white tracking-tighter mt-0.5">{{ number_format($totalFolders) }}</h3>
        </div>
        <!-- Storage Used -->
        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl shadow-sm ring-1 ring-black/5 dark:ring-white/10 transition-all hover:shadow-md">
            <div class="flex justify-between items-start mb-2">
                <div class="w-10 h-10 rounded-xl bg-blue-500/10 flex items-center justify-center text-blue-500">
                    <span class="material-symbols-outlined text-xl" style="font-variation-settings: 'FILL' 1;">database</span>
                </div>
                <span class="text-[9px] font-black text-amber-600 bg-amber-50 dark:bg-amber-900/20 px-2 py-0.5 rounded-full uppercase tracking-widest">Capacité</span>
            </div>
            <p class="text-[9px] uppercase tracking-widest text-outline dark:text-slate-500 font-black">Stockage utilisé</p>
            <h3 class="text-xl font-black text-on-surface dark:text-white tracking-tighter mt-0.5">{{ $totalSize }}</h3>
        </div>
    </div>

    <!-- Main Analytics Grid -->
    <div class="grid grid-cols-12 gap-6">
        <!-- Modern Evolution Chart -->
        <div class="col-span-12 lg:col-span-8 bg-white dark:bg-slate-900 p-6 rounded-2xl shadow-sm ring-1 ring-black/5 dark:ring-white/5 flex flex-col">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
                <div>
                    <h4 class="text-lg font-black text-slate-900 dark:text-white tracking-tight">Croissance du stockage</h4>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Suivi mensuel des transferts et de l'occupation</p>
                </div>
                <form action="{{ route('admin.dashboard') }}" method="GET">
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
            <h4 class="text-lg font-black text-slate-900 dark:text-white tracking-tight mb-1">Répartition</h4>
            <p class="text-xs text-slate-500 dark:text-slate-400 mb-6">Actifs par catégorie</p>
            <div id="fileDistributionChart" class="flex-1 flex flex-col justify-center min-h-[300px]"></div>
        </div>
    </div>

    <!-- Bottom Row -->
    <div class="grid grid-cols-12 gap-6">
        <!-- Recent Table -->
        <div class="col-span-12 lg:col-span-8 bg-white dark:bg-slate-900 p-6 rounded-2xl shadow-sm ring-1 ring-black/5 dark:ring-white/5">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-lg font-black tracking-tight text-on-surface dark:text-white">Dépôts récents</h2>
                <a href="{{ route('admin.explorer') }}" class="text-[10px] font-black text-primary dark:text-blue-400 uppercase tracking-widest hover:underline">Voir tout</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="text-[9px] font-black uppercase tracking-[0.2em] text-outline dark:text-slate-500 border-b border-black/5 dark:border-white/5">
                            <th class="pb-4">Nom de l'actif</th>
                            <th class="pb-4 text-center">Propriétaire</th>
                            <th class="pb-4 text-right">Taille</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-black/5 dark:divide-white/5">
                        @foreach($recentFiles as $file)
                        <tr class="group hover:bg-primary/5 dark:hover:bg-blue-400/5 transition-colors">
                            <td class="py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-surface-container-low dark:bg-slate-800 flex items-center justify-center">
                                        <span class="material-symbols-outlined text-primary dark:text-blue-400 text-base">description</span>
                                    </div>
                                    <div>
                                        <p class="text-xs font-bold text-on-surface dark:text-slate-200">{{ $file->name }}</p>
                                        <p class="text-[9px] text-outline dark:text-slate-500 uppercase font-black">{{ $file->extension }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 text-center">
                                <span class="text-[10px] font-bold text-slate-600 dark:text-slate-400 bg-slate-100 dark:bg-slate-800 px-2 py-1 rounded-md">{{ $file->user->name ?? '-' }}</span>
                            </td>
                            <td class="py-4 text-right">
                                <span class="text-xs font-black text-on-surface dark:text-white">{{ number_format($file->size / 1024 / 1024, 2) }} Mo</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- System Health (Style Restauré) -->
        <div class="col-span-12 lg:col-span-4 glass-card p-8 rounded-2xl flex flex-col justify-between border border-white/20 dark:border-slate-800 shadow-xl overflow-hidden relative group">
            <div class="z-10 relative">
                <div class="flex items-center justify-between mb-8">
                    <div class="w-12 h-12 bg-white/40 dark:bg-slate-800/40 backdrop-blur-xl rounded-xl flex items-center justify-center text-primary dark:text-blue-400 shadow-sm">
                        <span class="material-symbols-outlined text-2xl" style="font-variation-settings: 'FILL' 1;">security</span>
                    </div>
                    <span class="px-3 py-1 bg-emerald-500 text-white text-[9px] font-black uppercase tracking-widest rounded-full shadow-lg">Actif</span>
                </div>
                <h3 class="text-xl font-black tracking-tight text-on-surface dark:text-white mb-2">Santé du Système</h3>
                <p class="text-sm text-outline dark:text-slate-400 font-medium leading-relaxed">Stockage chiffré et redondance opérationnelle à 100%.</p>
            </div>
            
            <div class="z-10 relative mt-8">
                <div class="flex justify-between items-end mb-3">
                    <span class="text-2xl font-black tracking-tighter text-on-surface dark:text-white">100.0%</span>
                    <span class="text-[9px] font-black uppercase tracking-widest text-emerald-600 dark:text-emerald-400">Synchronisé</span>
                </div>
                <div class="w-full bg-slate-200 dark:bg-slate-800 h-2 rounded-full overflow-hidden">
                    <div class="bg-emerald-500 h-full w-full"></div>
                </div>
            </div>

            <!-- Abstract Decorative Glow -->
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
            name: 'Occupation Totale (Mo)',
            data: @json($cumulativeStorage)
        }, {
            name: 'Fichiers Uploadés',
            data: @json($monthlyUploads)
        }],
        chart: {
            height: 320,
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
                opacityTo: 0,
                stops: [0, 90, 100]
            }
        },
        grid: {
            borderColor: isDark ? '#1e293b' : '#f1f5f9',
            strokeDashArray: 4,
            padding: { left: 0, right: 0 }
        },
        xaxis: {
            categories: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Août', 'Sep', 'Oct', 'Nov', 'Déc'],
            axisBorder: { show: false },
            axisTicks: { show: false },
            labels: { style: { colors: '#94a3b8', fontWeight: 600 } }
        },
        yaxis: {
            labels: { style: { colors: '#94a3b8', fontWeight: 600 } }
        },
        tooltip: {
            theme: isDark ? 'dark' : 'light',
            y: { 
                formatter: function(val, { seriesIndex }) {
                    if (seriesIndex === 0) return val + " Mo";
                    return val + " fichier(s)";
                }
            }
        },
        legend: {
            position: 'top',
            horizontalAlign: 'right',
            fontWeight: 800,
            markers: { radius: 12 },
            itemMargin: { horizontal: 10 },
            labels: { colors: isDark ? '#94a3b8' : '#64748b' }
        }
    };
    new ApexCharts(document.querySelector("#storageEvolutionChart"), evolutionOptions).render();

    // 2. Chart: File Distribution
    const distributionOptions = {
        series: [@json($distribution['docs']), @json($distribution['imgs']), @json($distribution['others'])],
        chart: { type: 'donut', height: 320, fontFamily: 'Inter, sans-serif' },
        labels: ['Documents', 'Images', 'Autres'],
        colors: ['#00488d', '#3b82f6', '#cbd5e1'],
        plotOptions: {
            pie: {
                donut: {
                    size: '75%',
                    labels: {
                        show: true,
                        total: {
                            show: true,
                            label: 'Total',
                            fontSize: '12px',
                            fontWeight: 800,
                            color: '#94a3b8',
                            formatter: () => @json($totalFiles) + ' Fichiers'
                        },
                        value: { fontSize: '24px', fontWeight: 900, color: isDark ? '#fff' : '#0d1c2e' }
                    }
                }
            }
        },
        stroke: { width: 0 },
        dataLabels: { enabled: false },
        legend: {
            position: 'bottom',
            fontWeight: 700,
            labels: { colors: isDark ? '#94a3b8' : '#64748b' }
        },
        tooltip: {
            theme: isDark ? 'dark' : 'light',
            y: { formatter: (val) => val + "%" }
        }
    };
    new ApexCharts(document.querySelector("#fileDistributionChart"), distributionOptions).render();
});
</script>
@endsection
