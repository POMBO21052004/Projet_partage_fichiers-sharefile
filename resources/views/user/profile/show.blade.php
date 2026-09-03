@extends('user.layouts.app')

@section('title', 'Mon Profil')

@section('content')
<div class="max-w-6xl mx-auto space-y-8">
    <!-- Header Section -->
    <div class="flex items-center justify-between">
        <h2 class="text-3xl font-black tracking-tight text-on-surface dark:text-white uppercase tracking-widest">Espace Personnel</h2>
        <a href="{{ route('user.profile.edit') }}" class="flex items-center gap-2 bg-primary text-white px-6 py-3 rounded-xl font-black text-[10px] uppercase tracking-widest shadow-lg shadow-primary/20 hover:opacity-90 transition-all">
            <span class="material-symbols-outlined text-sm">settings</span>
            Éditer mes accès
        </a>
    </div>

    @if(session('success'))
        <div class="p-4 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-500/20 rounded-xl text-emerald-700 dark:text-emerald-400 text-xs font-black uppercase tracking-widest flex items-center gap-3">
            <span class="material-symbols-outlined text-sm">check_circle</span>
            {{ session('success') }}
        </div>
    @endif

    <!-- 1. Profile Card -->
    <section class="flex flex-col md:flex-row items-center gap-8 p-10 rounded-xl bg-white dark:bg-slate-900 shadow-sm border border-outline-variant/10">
        <div class="relative">
            <div class="w-32 h-32 rounded-xl bg-primary/10 dark:bg-blue-900/30 flex items-center justify-center text-primary dark:text-blue-400 font-black text-4xl shadow-xl ring-1 ring-primary/20">
                {{ strtoupper(substr($user->name, 0, 2)) }}
            </div>
            <div class="absolute -bottom-2 -right-2 w-8 h-8 bg-emerald-500 border-4 border-white dark:border-slate-900 rounded-lg shadow-sm flex items-center justify-center">
                <span class="material-symbols-outlined text-white text-[16px]">verified</span>
            </div>
        </div>
        
        <div class="flex-1 text-center md:text-left space-y-2">
            <div class="flex flex-col md:flex-row items-center gap-3">
                <h3 class="text-4xl font-black tracking-tighter text-on-surface dark:text-white">{{ $user->name }}</h3>
                <span class="px-3 py-1 bg-primary text-white text-[9px] font-black uppercase tracking-[0.2em] rounded-full">Utilisateur</span>
            </div>
            <p class="text-outline dark:text-slate-500 font-medium text-lg">{{ $user->email }}</p>
            
            <div class="flex flex-wrap justify-center md:justify-start gap-6 pt-4">
                <div class="flex items-center gap-2 text-outline dark:text-slate-500 text-[10px] font-black uppercase tracking-widest">
                    <span class="material-symbols-outlined text-[18px]">security</span>
                    <span>Accès Sécurisé</span>
                </div>
                <div class="flex items-center gap-2 text-outline dark:text-slate-500 text-[10px] font-black uppercase tracking-widest">
                    <span class="material-symbols-outlined text-[18px]">calendar_today</span>
                    <span>Membre depuis {{ $user->created_at->format('M Y') }}</span>
                </div>
            </div>
        </div>
    </section>

    <!-- 2. Bento Stats -->
    <section class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="p-8 rounded-xl bg-white dark:bg-slate-900 border border-black/5 dark:border-white/5 shadow-sm space-y-4">
            <div class="flex items-center justify-between">
                <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center text-primary">
                    <span class="material-symbols-outlined">cloud_upload</span>
                </div>
                <span class="text-[9px] font-black uppercase tracking-widest text-emerald-500 bg-emerald-500/10 px-2 py-1 rounded">Actif</span>
            </div>
            <div>
                <p class="text-4xl font-black text-on-surface dark:text-white">{{ $user->files->count() }}</p>
                <h3 class="text-[10px] font-black tracking-widest uppercase text-outline mt-1">Mes Documents</h3>
            </div>
        </div>
        
        <div class="p-8 rounded-xl bg-white dark:bg-slate-900 border border-black/5 dark:border-white/5 shadow-sm space-y-4">
            <div class="flex items-center justify-between">
                <div class="w-10 h-10 rounded-lg bg-emerald-500/10 flex items-center justify-center text-emerald-500">
                    <span class="material-symbols-outlined">folder_shared</span>
                </div>
                <span class="text-[9px] font-black uppercase tracking-widest text-emerald-500 bg-emerald-500/10 px-2 py-1 rounded">Accès</span>
            </div>
            <div>
                <p class="text-4xl font-black text-on-surface dark:text-white">{{ $user->sharedFiles()->count() }}</p>
                <h3 class="text-[10px] font-black tracking-widest uppercase text-outline mt-1">Partagés avec Moi</h3>
            </div>
        </div>

        <div class="p-8 rounded-xl bg-white dark:bg-slate-900 border border-black/5 dark:border-white/5 shadow-sm space-y-4">
            <div class="flex items-center justify-between">
                <div class="w-10 h-10 rounded-lg bg-blue-500/10 flex items-center justify-center text-blue-500">
                    <span class="material-symbols-outlined">cloud_done</span>
                </div>
                <span class="text-[9px] font-black uppercase tracking-widest text-blue-500 bg-blue-500/10 px-2 py-1 rounded">Capacité</span>
            </div>
            <div>
                <p class="text-3xl font-black text-on-surface dark:text-white mt-1">
                    {{ number_format($user->files->sum('size') / 1048576, 2) }} <span class="text-sm font-bold text-outline">Mo</span>
                </p>
                <h3 class="text-[10px] font-black tracking-widest uppercase text-outline mt-1">Stockage Occupé</h3>
            </div>
        </div>
    </section>

    <!-- 3. Recent Activity List -->
    <section class="bg-white dark:bg-slate-900 rounded-xl border border-black/5 dark:border-white/5 shadow-sm overflow-hidden">
        <div class="px-8 py-6 border-b border-black/5 flex justify-between items-center">
            <h3 class="text-xs font-black uppercase tracking-widest text-on-surface dark:text-white">Registre de mes documents récents</h3>
            <span class="text-[9px] font-black uppercase tracking-widest text-outline">Coffre-fort sécurisé</span>
        </div>
        
        <div class="divide-y divide-black/5 dark:divide-white/5">
            @forelse($user->files()->latest()->take(5)->get() as $file)
            <div class="px-8 py-5 flex items-center gap-6 group hover:bg-primary/5 transition-colors">
                <div class="w-12 h-12 rounded-xl bg-surface-container-low dark:bg-slate-800 flex items-center justify-center text-primary shadow-inner">
                    <span class="material-symbols-outlined">insert_drive_file</span>
                </div>
                <div class="flex-1">
                    <div class="font-bold text-sm text-on-surface dark:text-white">Dépôt de {{ $file->name }}</div>
                    <div class="text-[10px] text-outline font-black uppercase tracking-widest mt-1">Chiffré • {{ $file->created_at->diffForHumans() }}</div>
                </div>
                <a href="{{ route('user.files.download', $file) }}" class="p-3 rounded-xl bg-surface-container-low dark:bg-slate-800 text-outline hover:text-primary transition-all">
                    <span class="material-symbols-outlined text-[18px]">download</span>
                </a>
            </div>
            @empty
            <div class="p-12 text-center text-outline italic text-sm">Aucun fichier importé dans votre coffre-fort pour le moment.</div>
            @endforelse
        </div>
    </section>
</div>
@endsection
