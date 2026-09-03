@extends('admin.layouts.app')

@section('title', 'Éditer Utilisateur : ' . $user->name)

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Breadcrumbs & Header -->
    <div class="mb-8">
        <nav class="flex items-center gap-2 text-[10px] font-black uppercase tracking-widest text-outline dark:text-slate-500 mb-3">
            <a href="{{ route('admin.users.index') }}" class="cursor-pointer hover:text-primary transition-colors">Utilisateurs</a>
            <span class="material-symbols-outlined text-[12px]">chevron_right</span>
            <span class="text-on-surface dark:text-white">Éditer Profil</span>
        </nav>
        
        <div class="flex items-end gap-6">
            <div class="relative group">
                <div class="w-24 h-24 rounded-xl overflow-hidden bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-400 font-black text-2xl shadow-sm ring-1 ring-black/5">
                    {{ strtoupper(substr($user->name, 0, 2)) }}
                </div>
                <button class="absolute -bottom-2 -right-2 w-8 h-8 bg-primary text-white rounded-lg flex items-center justify-center shadow-lg hover:scale-105 transition-transform">
                    <span class="material-symbols-outlined text-sm">photo_camera</span>
                </button>
            </div>
            <div class="flex-1">
                <h2 class="text-3xl font-black tracking-tight text-on-surface dark:text-white">{{ $user->name }}</h2>
                <p class="text-outline dark:text-slate-500 text-sm font-medium">Gérez les paramètres du compte et les permissions système.</p>
            </div>
        </div>
    </div>

    <!-- Error Messages -->
    @if($errors->any())
        <div class="mb-6 p-4 bg-rose-50 dark:bg-rose-900/10 border border-rose-200 dark:border-rose-500/20 rounded-xl">
            <ul class="list-disc list-inside text-rose-700 dark:text-rose-400 text-xs font-bold uppercase tracking-widest">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.users.update', $user) }}" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-8">
        @csrf
        @method('PUT')
        
        <!-- Primary Settings Card -->
        <div class="md:col-span-2 space-y-6">
            <div class="bg-white dark:bg-slate-900 p-8 rounded-xl shadow-sm border border-black/5 dark:border-white/5">
                <h3 class="text-xs font-black text-on-surface dark:text-white mb-8 flex items-center gap-2 uppercase tracking-widest">
                    <span class="material-symbols-outlined text-primary">person</span>
                    Informations de base
                </h3>
                
                <div class="space-y-6">
                    <div class="grid grid-cols-1 gap-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-outline dark:text-slate-500 px-1">Nom complet</label>
                        <input name="name" class="w-full px-5 py-4 bg-surface-container-low dark:bg-slate-800 border-none rounded-xl focus:ring-2 focus:ring-primary/20 transition-all text-sm font-bold text-on-surface dark:text-white outline-none" type="text" value="{{ old('name', $user->name) }}" required/>
                    </div>
                    
                    <div class="grid grid-cols-1 gap-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-outline dark:text-slate-500 px-1">Adresse Email</label>
                        <div class="relative">
                            <input name="email" class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-800/50 border-none rounded-xl text-sm font-bold text-outline dark:text-slate-600 cursor-not-allowed outline-none" readonly type="email" value="{{ old('email', $user->email) }}"/>
                            <span class="material-symbols-outlined absolute right-4 top-1/2 -translate-y-1/2 text-outline/40 text-sm">lock</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-2 pt-4">
                        <label class="text-[10px] font-black uppercase tracking-widest text-outline dark:text-slate-500 px-1">Réinitialiser le mot de passe (Laisser vide si inchangé)</label>
                        <input name="password" class="w-full px-5 py-4 bg-surface-container-low dark:bg-slate-800 border-none rounded-xl focus:ring-2 focus:ring-primary/20 transition-all text-sm font-bold text-on-surface dark:text-white outline-none" type="password" placeholder="••••••••"/>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-end gap-4 pt-4">
                <a href="{{ route('admin.users.index') }}" class="px-8 py-4 rounded-xl text-[10px] font-black uppercase tracking-widest text-outline hover:bg-surface-container-high transition-colors">
                    Annuler
                </a>
                <button type="submit" class="px-10 py-4 bg-primary text-white rounded-xl text-[10px] font-black uppercase tracking-widest shadow-lg shadow-primary/20 hover:opacity-90 active:scale-95 transition-all">
                    Enregistrer les modifications
                </button>
            </div>
        </div>

        <!-- Sidebar Configuration -->
        <div class="space-y-6">
            <!-- Status Toggle Card -->
            <div class="bg-surface-container-low dark:bg-slate-900/50 p-6 rounded-xl border border-black/5 dark:border-white/5">
                <h3 class="text-[10px] font-black uppercase tracking-widest text-outline dark:text-slate-500 mb-6">Statut du Compte</h3>
                <div class="flex items-center justify-between">
                    <span class="font-bold text-xs text-on-surface dark:text-white uppercase tracking-wider">État Actif</span>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="hidden" name="status" value="inactive">
                        <input type="checkbox" name="status" value="active" {{ old('status', $user->status) === 'active' ? 'checked' : '' }} class="sr-only peer">
                        <div class="w-11 h-6 bg-slate-200 dark:bg-slate-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                    </label>
                </div>
                <p class="text-[10px] text-outline dark:text-slate-500 mt-4 leading-relaxed font-medium">La désactivation révoque immédiatement tous les accès aux coffres-forts.</p>
            </div>

            <!-- Permissions Checklist -->
            <div class="bg-surface-container-low dark:bg-slate-900/50 p-6 rounded-xl border border-black/5 dark:border-white/5">
                <h3 class="text-[10px] font-black uppercase tracking-widest text-outline dark:text-slate-500 mb-6">Permissions Système</h3>
                <div class="space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="w-5 h-5 flex items-center justify-center rounded bg-primary text-white">
                            <span class="material-symbols-outlined text-[14px]">check</span>
                        </div>
                        <span class="text-xs font-bold text-on-surface dark:text-white uppercase tracking-widest">Accès Fichiers</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-5 h-5 flex items-center justify-center rounded bg-primary text-white">
                            <span class="material-symbols-outlined text-[14px]">check</span>
                        </div>
                        <span class="text-xs font-bold text-on-surface dark:text-white uppercase tracking-widest">Partage Interne</span>
                    </div>
                    <div class="flex items-center gap-3 opacity-40">
                        <div class="w-5 h-5 border-2 border-outline-variant rounded bg-transparent"></div>
                        <span class="text-xs font-bold text-outline uppercase tracking-widest">Panel Admin</span>
                    </div>
                </div>
            </div>

            <!-- Help Context -->
            <div class="p-6 bg-primary/10 rounded-xl overflow-hidden relative group border border-primary/10">
                <div class="relative z-10">
                    <h4 class="text-primary font-black text-xs mb-2 uppercase tracking-widest">Assistance</h4>
                    <p class="text-outline dark:text-slate-500 text-[10px] leading-relaxed mb-4 font-medium">La modification des permissions critiques nécessite une validation de niveau 4.</p>
                    <button type="button" class="text-[10px] font-black text-primary uppercase tracking-widest flex items-center gap-1 hover:underline">
                        Consulter la politique
                        <span class="material-symbols-outlined text-[14px]">open_in_new</span>
                    </button>
                </div>
                <span class="material-symbols-outlined absolute -right-4 -bottom-4 text-7xl text-primary/5 select-none">info</span>
            </div>
        </div>
    </form>
</div>
@endsection

