@extends('admin.layouts.app')

@section('title', 'Profil Admin : ' . $admin->name)

@section('content')
<div class="max-w-6xl mx-auto space-y-8">
    <!-- 1. Profile Header -->
    <section class="flex flex-col md:flex-row items-start md:items-end justify-between gap-6 p-8 rounded-xl bg-white dark:bg-slate-900 shadow-sm border border-outline-variant/10">
        <div class="flex items-center gap-6">
            <div class="relative">
                <div class="w-24 h-24 md:w-32 md:h-32 rounded-full bg-primary/10 dark:bg-blue-900/30 flex items-center justify-center text-primary dark:text-blue-400 font-black text-4xl shadow-xl">
                    {{ strtoupper(substr($admin->name, 0, 2)) }}
                </div>
                <div class="absolute bottom-2 right-2 w-6 h-6 bg-emerald-500 border-4 border-white dark:border-slate-900 rounded-full shadow-sm" title="Status: Admin Root"></div>
            </div>
            <div>
                <div class="flex items-center gap-3">
                    <h2 class="text-3xl font-black tracking-tight text-on-surface dark:text-white">{{ $admin->name }}</h2>
                    <span class="px-3 py-1 bg-primary text-white text-[10px] font-black uppercase tracking-widest rounded-full">Root Admin</span>
                </div>
                <p class="text-outline dark:text-slate-500 font-medium mt-1">{{ $admin->email }}</p>
                <div class="flex items-center gap-4 mt-4">
                    <div class="flex items-center gap-1.5 text-outline dark:text-slate-500 text-xs font-bold uppercase tracking-widest">
                        <span class="material-symbols-outlined text-[18px]">admin_panel_settings</span>
                        <span>Contrôle Total</span>
                    </div>
                    <div class="flex items-center gap-1.5 text-outline dark:text-slate-500 text-xs font-bold uppercase tracking-widest">
                        <span class="material-symbols-outlined text-[18px]">calendar_today</span>
                        <span>Nommé en {{ $admin->created_at->format('M Y') }}</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="flex gap-3 w-full md:w-auto">
            <a href="{{ route('admin.admins.edit', $admin) }}" class="flex-1 md:flex-none px-6 py-2.5 rounded-xl border border-outline-variant/30 text-on-surface dark:text-slate-300 font-black text-[10px] uppercase tracking-widest hover:bg-surface-container transition-all text-center">Paramètres Compte</a>
            <button disabled class="flex-1 md:flex-none px-6 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-400 font-black text-[10px] uppercase tracking-widest cursor-not-allowed">
                Statut Permanent
            </button>
        </div>
    </section>

    <!-- 2. Statistics Cards (Bento Style) -->
    <section class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="p-6 rounded-xl bg-white dark:bg-slate-900 border border-black/5 dark:border-white/5 hover:bg-primary/5 transition-colors cursor-default shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <span class="material-symbols-outlined text-primary">cloud_upload</span>
                <span class="text-[9px] font-black tracking-widest uppercase text-outline">Gestion Fichiers</span>
            </div>
            <p class="text-4xl font-black text-on-surface dark:text-white">{{ $admin->files->count() }}</p>
            <h3 class="text-[10px] font-black tracking-widest uppercase text-outline mt-1">Éléments Importés</h3>
        </div>
        <div class="p-6 rounded-xl bg-white dark:bg-slate-900 border border-black/5 dark:border-white/5 hover:bg-primary/5 transition-colors cursor-default shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <span class="material-symbols-outlined text-secondary">verified_user</span>
                <span class="text-[9px] font-black tracking-widest uppercase text-outline">Sécurité</span>
            </div>
            <p class="text-4xl font-black text-on-surface dark:text-white">Active</p>
            <h3 class="text-[10px] font-black tracking-widest uppercase text-outline mt-1">Audit Trail</h3>
        </div>
        <div class="p-6 rounded-xl bg-white dark:bg-slate-900 border border-black/5 dark:border-white/5 hover:bg-primary/5 transition-colors cursor-default shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <span class="material-symbols-outlined text-tertiary">history</span>
                <span class="text-[9px] font-black tracking-widest uppercase text-outline">Dernière Action</span>
            </div>
            <p class="text-4xl font-black text-on-surface dark:text-white">{{ $admin->updated_at->diffForHumans(null, true) }}</p>
            <h3 class="text-[10px] font-black tracking-widest uppercase text-outline mt-1">Activité Système</h3>
        </div>
    </section>

    <!-- 3 & 4: Activity Feed & Audit (Admin Specific) -->
    <section class="grid grid-cols-1 lg:grid-cols-5 gap-8">
        <!-- Audit Activity (3/5 width) -->
        <div class="lg:col-span-3 space-y-6">
            <div class="flex items-center justify-between">
                <h3 class="text-xl font-black text-on-surface dark:text-white uppercase tracking-widest">Registre d'Audit Admin</h3>
                <button class="text-primary text-[10px] font-black uppercase tracking-widest hover:underline">Accéder aux Logs</button>
            </div>
            <div class="space-y-4">
                @forelse($admin->files()->latest()->take(5)->get() as $file)
                <div class="group flex items-start gap-4 p-4 rounded-xl bg-white dark:bg-slate-900 border border-black/5 dark:border-white/5 hover:bg-primary/5 transition-all duration-200">
                    <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center text-primary shrink-0">
                        <span class="material-symbols-outlined text-[20px]">security</span>
                    </div>
                    <div class="flex-1">
                        <p class="text-on-surface dark:text-slate-300 text-sm leading-relaxed">
                            A archivé un nouvel actif critique : <span class="font-bold text-primary dark:text-blue-400">{{ $file->name }}</span>.
                        </p>
                        <p class="text-outline dark:text-slate-500 text-[10px] font-bold uppercase tracking-widest mt-1">{{ $file->created_at->diffForHumans() }}</p>
                    </div>
                    <a href="{{ route('admin.files.show', $file) }}" class="opacity-0 group-hover:opacity-100 transition-opacity p-2 text-outline hover:text-primary">
                        <span class="material-symbols-outlined text-[18px]">visibility</span>
                    </a>
                </div>
                @empty
                <div class="p-8 text-center text-outline italic text-sm">Aucune archive créée par cet admin.</div>
                @endforelse
            </div>
        </div>

        <!-- System Assignments (2/5 width) -->
        <div class="lg:col-span-2 space-y-6">
            <h3 class="text-xl font-black text-on-surface dark:text-white uppercase tracking-widest">Privilèges</h3>
            <div class="grid grid-cols-1 gap-4">
                <div class="p-5 rounded-xl bg-surface-container-low dark:bg-slate-800/50 border border-primary/20 shadow-sm flex items-center gap-4 group">
                    <div class="w-12 h-12 rounded-lg bg-primary text-white flex items-center justify-center shadow-lg shadow-primary/20">
                        <span class="material-symbols-outlined">shield_with_house</span>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-bold text-sm text-primary dark:text-blue-400">Accès Maître</h4>
                        <p class="text-[9px] text-outline font-black tracking-widest uppercase">Tous les coffres-forts</p>
                    </div>
                </div>
                <div class="p-5 rounded-xl bg-white dark:bg-slate-900 border border-black/5 dark:border-white/5 shadow-sm flex items-center gap-4 group">
                    <div class="w-12 h-12 rounded-lg bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-400">
                        <span class="material-symbols-outlined">group_add</span>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-bold text-sm text-on-surface dark:text-white">Gestion RH</h4>
                        <p class="text-[9px] text-outline font-black tracking-widest uppercase">Édition des membres</p>
                    </div>
                </div>
                
                <div class="p-6 bg-tertiary-fixed/10 rounded-xl border border-tertiary/10">
                    <div class="flex items-start gap-4">
                        <span class="material-symbols-outlined text-tertiary">warning</span>
                        <div>
                            <h4 class="text-[10px] font-black text-tertiary uppercase tracking-widest mb-1">Alerte de Sécurité</h4>
                            <p class="text-[10px] text-on-tertiary-fixed-variant leading-relaxed font-medium">
                                Les comptes root ne peuvent être désactivés que par un autre administrateur de niveau identique.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

