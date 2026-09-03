@extends('admin.layouts.app')

@section('title', 'Profil : ' . $user->name)

@section('content')
<div class="max-w-6xl mx-auto space-y-8">
    <!-- 1. Profile Header -->
    <section class="flex flex-col md:flex-row items-start md:items-end justify-between gap-6 p-8 rounded-xl bg-white dark:bg-slate-900 shadow-sm border border-outline-variant/10">
        <div class="flex items-center gap-6">
            <div class="relative">
                <div class="w-24 h-24 md:w-32 md:h-32 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-400 font-black text-4xl shadow-xl">
                    {{ strtoupper(substr($user->name, 0, 2)) }}
                </div>
                <div class="absolute bottom-2 right-2 w-6 h-6 {{ $user->status === 'active' ? 'bg-emerald-500' : 'bg-slate-300' }} border-4 border-white dark:border-slate-900 rounded-full shadow-sm" title="Status: {{ ucfirst($user->status) }}"></div>
            </div>
            <div>
                <div class="flex items-center gap-3">
                    <h2 class="text-3xl font-black tracking-tight text-on-surface dark:text-white">{{ $user->name }}</h2>
                    <span class="px-3 py-1 bg-primary/10 text-primary text-[10px] font-black uppercase tracking-widest rounded-full">Utilisateur</span>
                </div>
                <p class="text-outline dark:text-slate-500 font-medium mt-1">{{ $user->email }}</p>
                <div class="flex items-center gap-4 mt-4">
                    <div class="flex items-center gap-1.5 text-outline dark:text-slate-500 text-xs font-bold uppercase tracking-widest">
                        <span class="material-symbols-outlined text-[18px]">verified</span>
                        <span>{{ $user->is_verified ? 'Vérifié' : 'En attente' }}</span>
                    </div>
                    <div class="flex items-center gap-1.5 text-outline dark:text-slate-500 text-xs font-bold uppercase tracking-widest">
                        <span class="material-symbols-outlined text-[18px]">calendar_today</span>
                        <span>Inscrit en {{ $user->created_at->format('M Y') }}</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="flex gap-3 w-full md:w-auto">
            <a href="{{ route('admin.users.edit', $user) }}" class="flex-1 md:flex-none px-6 py-2.5 rounded-xl border border-outline-variant/30 text-on-surface dark:text-slate-300 font-black text-[10px] uppercase tracking-widest hover:bg-surface-container transition-all text-center">Éditer Profil</a>
            <form action="{{ route('admin.users.toggle-status', $user) }}" method="POST" class="flex-1 md:flex-none">
                @csrf
                <button type="submit" class="w-full px-6 py-2.5 rounded-xl bg-primary text-white font-black text-[10px] uppercase tracking-widest shadow-md shadow-primary/20 hover:opacity-90 transition-all">
                    {{ $user->status === 'active' ? 'Désactiver' : 'Activer' }}
                </button>
            </form>
        </div>
    </section>

    <!-- 2. Statistics Cards (Bento Style) -->
    <section class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="p-6 rounded-xl bg-white dark:bg-slate-900 border border-black/5 dark:border-white/5 hover:bg-primary/5 transition-colors cursor-default shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <span class="material-symbols-outlined text-primary">cloud_upload</span>
                <span class="text-[9px] font-black tracking-widest uppercase text-outline">Activité globale</span>
            </div>
            <p class="text-4xl font-black text-on-surface dark:text-white">{{ $user->files->count() }}</p>
            <h3 class="text-[10px] font-black tracking-widest uppercase text-outline mt-1">Fichiers Déposés</h3>
        </div>
        <div class="p-6 rounded-xl bg-white dark:bg-slate-900 border border-black/5 dark:border-white/5 hover:bg-primary/5 transition-colors cursor-default shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <span class="material-symbols-outlined text-secondary">database</span>
                <span class="text-[9px] font-black tracking-widest uppercase text-outline">Quota utilisé</span>
            </div>
            <p class="text-4xl font-black text-on-surface dark:text-white">{{ number_format($user->files->sum('size') / 1024 / 1024, 2) }} <span class="text-lg">Mo</span></p>
            <h3 class="text-[10px] font-black tracking-widest uppercase text-outline mt-1">Volume de Stockage</h3>
        </div>
        <div class="p-6 rounded-xl bg-white dark:bg-slate-900 border border-black/5 dark:border-white/5 hover:bg-primary/5 transition-colors cursor-default shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <span class="material-symbols-outlined text-tertiary">history</span>
                <span class="text-[9px] font-black tracking-widest uppercase text-outline">Dernier accès</span>
            </div>
            <p class="text-4xl font-black text-on-surface dark:text-white">{{ $user->updated_at->diffForHumans(null, true) }}</p>
            <h3 class="text-[10px] font-black tracking-widest uppercase text-outline mt-1">Temps écoulé</h3>
        </div>
    </section>

    <!-- 3 & 4: Activity Feed & Projects Asymmetric Grid -->
    <section class="grid grid-cols-1 lg:grid-cols-5 gap-8">
        <!-- Activity Feed (3/5 width) -->
        <div class="lg:col-span-3 space-y-6">
            <div class="flex items-center justify-between">
                <h3 class="text-xl font-black text-on-surface dark:text-white uppercase tracking-widest">Dernières Activités</h3>
                <button class="text-primary text-[10px] font-black uppercase tracking-widest hover:underline">Tout voir</button>
            </div>
            <div class="space-y-4">
                @forelse($user->files()->latest()->take(5)->get() as $file)
                <div class="group flex items-start gap-4 p-4 rounded-xl bg-white dark:bg-slate-900 border border-black/5 dark:border-white/5 hover:bg-primary/5 transition-all duration-200">
                    <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center text-primary shrink-0">
                        <span class="material-symbols-outlined text-[20px]">upload_file</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-on-surface dark:text-slate-300 text-sm leading-relaxed break-words">
                            A déposé <span class="font-bold text-primary dark:text-blue-400 break-all">{{ $file->name }}</span> dans le coffre-fort institutionnel.
                        </p>
                        <p class="text-outline dark:text-slate-500 text-[10px] font-bold uppercase tracking-widest mt-1">{{ $file->created_at->diffForHumans() }}</p>
                    </div>
                    <a href="{{ route('admin.files.show', $file) }}" class="opacity-0 group-hover:opacity-100 transition-opacity p-2 text-outline hover:text-primary">
                        <span class="material-symbols-outlined text-[18px]">visibility</span>
                    </a>
                </div>
                @empty
                <div class="p-8 text-center text-outline italic text-sm">Aucune activité récente.</div>
                @endforelse
            </div>
        </div>

        <!-- Access Summary (2/5 width) -->
        <div class="lg:col-span-2 space-y-6">
            <h3 class="text-xl font-black text-on-surface dark:text-white uppercase tracking-widest">Accès Partagés</h3>
            <div class="grid grid-cols-1 gap-4">
                @forelse($user->sharedFiles()->latest()->take(3)->get() as $sharedFile)
                <div class="p-5 rounded-xl bg-white dark:bg-slate-900 border border-black/5 dark:border-white/5 shadow-sm flex items-center gap-4 hover:shadow-md transition-shadow cursor-pointer group">
                    <div class="w-12 h-12 rounded-lg bg-surface-container-highest dark:bg-slate-800 flex items-center justify-center text-primary">
                        <span class="material-symbols-outlined">folder_shared</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h4 class="font-bold text-sm text-on-surface dark:text-white truncate" title="{{ $sharedFile->name }}">{{ $sharedFile->name }}</h4>
                        <p class="text-[9px] text-outline font-black tracking-widest uppercase">{{ number_format($sharedFile->size / 1024, 1) }} Ko • {{ $sharedFile->extension }}</p>
                    </div>
                    <span class="material-symbols-outlined text-outline-variant group-hover:text-primary transition-colors">chevron_right</span>
                </div>
                @empty
                <div class="p-8 text-center bg-slate-50 dark:bg-slate-800/50 rounded-xl border border-dashed border-outline-variant/30 text-outline text-xs italic">
                    Aucun accès spécifique accordé.
                </div>
                @endforelse
                
                <button class="p-5 rounded-xl border border-dashed border-outline-variant/30 hover:bg-primary/5 transition-colors flex items-center justify-center gap-2 group text-outline dark:text-slate-500">
                    <span class="material-symbols-outlined group-hover:scale-110 transition-transform">add_circle</span>
                    <span class="text-[10px] font-black uppercase tracking-widest">Attribuer Nouvel Accès</span>
                </button>
            </div>
        </div>
    </section>
</div>
@endsection
