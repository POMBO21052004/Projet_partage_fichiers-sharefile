@extends('admin.layouts.app')

@section('title', 'Journal d\'Audit')

@section('content')
<div class="space-y-8">
    <!-- Audit Stats Dashboard -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl ring-1 ring-black/5 dark:ring-white/10 flex items-center justify-between shadow-sm">
            <div>
                <p class="text-[10px] font-black uppercase tracking-widest text-outline dark:text-slate-500 mb-1">Total Événements</p>
                <h3 class="text-3xl font-extrabold tracking-tight text-on-surface dark:text-white">{{ number_format($stats['total']) }}</h3>
            </div>
            <div class="w-12 h-12 rounded-xl bg-primary/10 flex items-center justify-center text-primary">
                <span class="material-symbols-outlined text-2xl">history</span>
            </div>
        </div>
        <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl ring-1 ring-black/5 dark:ring-white/10 flex items-center justify-between shadow-sm">
            <div>
                <p class="text-[10px] font-black uppercase tracking-widest text-outline dark:text-slate-500 mb-1">Activités Aujourd'hui</p>
                <h3 class="text-3xl font-extrabold tracking-tight text-on-surface dark:text-white">{{ $stats['today'] }}</h3>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-500/10 flex items-center justify-center text-emerald-500">
                <span class="material-symbols-outlined text-2xl">today</span>
            </div>
        </div>
        <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl ring-1 ring-black/5 dark:ring-white/10 flex items-center justify-between shadow-sm">
            <div>
                <p class="text-[10px] font-black uppercase tracking-widest text-outline dark:text-slate-500 mb-1">Types d'Actions</p>
                <h3 class="text-3xl font-extrabold tracking-tight text-on-surface dark:text-white">{{ $stats['actions'] }}</h3>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-500/10 flex items-center justify-center text-amber-500">
                <span class="material-symbols-outlined text-2xl">category</span>
            </div>
        </div>
    </div>

    <!-- Audit Timeline -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-sm border border-black/5 dark:border-white/10 overflow-hidden">
        <div class="px-8 py-6 bg-surface-container-low dark:bg-slate-800/50 border-b border-black/5 dark:border-white/10 flex justify-between items-center">
            <h3 class="text-xs font-black uppercase tracking-[0.2em] text-outline">Flux d'activités système</h3>
            <button class="text-[10px] font-black uppercase tracking-widest text-primary dark:text-blue-400 hover:underline">Exporter CSV</button>
        </div>

        <div class="overflow-x-auto w-full">
            <table class="w-full text-left whitespace-nowrap min-w-max">
                <thead>
                    <tr class="text-[10px] font-black uppercase tracking-widest text-outline bg-slate-50/50 dark:bg-slate-900/50">
                        <th class="px-8 py-5">Horodatage</th>
                        <th class="px-8 py-5">Utilisateur</th>
                        <th class="px-8 py-5">Action</th>
                        <th class="px-8 py-5">Détails</th>
                        <th class="px-8 py-5">Adresse IP</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-black/5 dark:divide-white/5">
                    @forelse($logs as $log)
                    <tr class="hover:bg-primary/5 dark:hover:bg-blue-400/5 transition-all group">
                        <td class="px-8 py-6">
                            <div class="flex flex-col">
                                <span class="text-xs font-bold text-on-surface dark:text-white">{{ $log->created_at->format('d/m/Y') }}</span>
                                <span class="text-[10px] text-outline font-medium tracking-tighter">{{ $log->created_at->format('H:i:s') }}</span>
                            </div>
                        </td>
                        <td class="px-8 py-6">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-surface-container-high dark:bg-slate-800 flex items-center justify-center text-[10px] font-black text-primary dark:text-blue-400 ring-1 ring-black/5 shrink-0">
                                    {{ strtoupper(substr($log->user->name ?? 'S', 0, 2)) }}
                                </div>
                                <div class="min-w-0 flex flex-col">
                                    <span class="text-xs font-bold text-on-surface dark:text-white block truncate max-w-[200px]" title="{{ $log->user->name ?? 'Système' }}">{{ $log->user->name ?? 'Système' }}</span>
                                    <span class="text-[9px] text-outline uppercase font-black tracking-widest block truncate">{{ $log->user->role ?? 'Bot' }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-8 py-6">
                            @php
                                $badgeColor = match($log->action) {
                                    'file_upload' => 'bg-emerald-50 text-emerald-600 dark:bg-emerald-900/20 dark:text-emerald-400',
                                    'file_delete' => 'bg-rose-50 text-rose-600 dark:bg-rose-900/20 dark:text-rose-400',
                                    'login' => 'bg-blue-50 text-blue-600 dark:bg-blue-900/20 dark:text-blue-400',
                                    'update_permission' => 'bg-amber-50 text-amber-600 dark:bg-amber-900/20 dark:text-amber-400',
                                    default => 'bg-slate-50 text-slate-600 dark:bg-slate-800 dark:text-slate-400'
                                };
                            @endphp
                            <span class="px-3 py-1.5 {{ $badgeColor }} text-[9px] font-black uppercase tracking-widest rounded-lg border border-current opacity-80">
                                {{ str_replace('_', ' ', $log->action) }}
                            </span>
                        </td>
                        <td class="px-8 py-6">
                            <p class="text-xs text-on-surface-variant dark:text-slate-400 max-w-md truncate font-medium" title="{{ $log->description }}">
                                {{ $log->description }}
                            </p>
                        </td>
                        <td class="px-8 py-6">
                            <div class="flex items-center gap-2 text-outline dark:text-slate-500">
                                <span class="material-symbols-outlined text-sm">public</span>
                                <span class="text-[10px] font-black tracking-widest">{{ $log->ip_address }}</span>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-8 py-20 text-center text-outline italic text-sm">Aucun événement enregistré dans le journal.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
        <div class="px-8 py-6 bg-slate-50/50 dark:bg-slate-900/50 border-t border-black/5 dark:border-white/10">
            {{ $logs->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

