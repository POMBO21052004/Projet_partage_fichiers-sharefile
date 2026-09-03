@extends('admin.layouts.app')

@section('title', 'Nouvel Utilisateur')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="mb-8">
        <nav class="flex items-center gap-2 text-[10px] font-black uppercase tracking-widest text-outline dark:text-slate-500 mb-3">
            <a href="{{ route('admin.users.index') }}" class="cursor-pointer hover:text-primary transition-colors">Utilisateurs</a>
            <span class="material-symbols-outlined text-[12px]">chevron_right</span>
            <span class="text-on-surface dark:text-white">Nouvel Enregistrement</span>
        </nav>
        
        <div class="flex items-end gap-6">
            <div class="w-24 h-24 rounded-xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-300 shadow-sm border-2 border-dashed border-outline-variant/30">
                <span class="material-symbols-outlined text-4xl">person_add</span>
            </div>
            <div class="flex-1">
                <h2 class="text-3xl font-black tracking-tight text-on-surface dark:text-white">Créer un Utilisateur</h2>
                <p class="text-outline dark:text-slate-500 text-sm font-medium">Enregistrez un nouveau collaborateur dans le système.</p>
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

    <form action="{{ route('admin.users.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-8">
        @csrf
        
        <!-- Primary Form -->
        <div class="md:col-span-2 space-y-6">
            <div class="bg-white dark:bg-slate-900 p-8 rounded-xl shadow-sm border border-black/5 dark:border-white/5">
                <h3 class="text-xs font-black text-on-surface dark:text-white mb-8 flex items-center gap-2 uppercase tracking-widest">
                    <span class="material-symbols-outlined text-primary">badge</span>
                    Identité du collaborateur
                </h3>
                
                <div class="space-y-6">
                    <div class="grid grid-cols-1 gap-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-outline dark:text-slate-500 px-1">Nom Complet</label>
                        <input name="name" class="w-full px-5 py-4 bg-surface-container-low dark:bg-slate-800 border-none rounded-xl focus:ring-2 focus:ring-primary/20 transition-all text-sm font-bold text-on-surface dark:text-white outline-none" type="text" placeholder="ex: Jean Dupont" required value="{{ old('name') }}"/>
                    </div>
                    
                    <div class="grid grid-cols-1 gap-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-outline dark:text-slate-500 px-1">Adresse Email</label>
                        <input name="email" class="w-full px-5 py-4 bg-surface-container-low dark:bg-slate-800 border-none rounded-xl focus:ring-2 focus:ring-primary/20 transition-all text-sm font-bold text-on-surface dark:text-white outline-none" type="email" placeholder="jean.dupont@entreprise.com" required value="{{ old('email') }}"/>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="grid grid-cols-1 gap-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-outline dark:text-slate-500 px-1">Mot de Passe</label>
                            <input name="password" class="w-full px-5 py-4 bg-surface-container-low dark:bg-slate-800 border-none rounded-xl focus:ring-2 focus:ring-primary/20 transition-all text-sm font-bold text-on-surface dark:text-white outline-none" type="password" required placeholder="••••••••"/>
                        </div>
                        <div class="grid grid-cols-1 gap-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-outline dark:text-slate-500 px-1">Confirmation</label>
                            <input name="password_confirmation" class="w-full px-5 py-4 bg-surface-container-low dark:bg-slate-800 border-none rounded-xl focus:ring-2 focus:ring-primary/20 transition-all text-sm font-bold text-on-surface dark:text-white outline-none" type="password" required placeholder="••••••••"/>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-end gap-4 pt-4">
                <a href="{{ route('admin.users.index') }}" class="px-8 py-4 rounded-xl text-[10px] font-black uppercase tracking-widest text-outline hover:bg-surface-container-high transition-colors">
                    Annuler
                </a>
                <button type="submit" class="px-10 py-4 bg-primary text-white rounded-xl text-[10px] font-black uppercase tracking-widest shadow-lg shadow-primary/20 hover:opacity-90 active:scale-95 transition-all">
                    Finaliser l'inscription
                </button>
            </div>
        </div>

        <!-- Sidebar Config -->
        <div class="space-y-6">
            <div class="bg-surface-container-low dark:bg-slate-900/50 p-6 rounded-xl border border-black/5 dark:border-white/5">
                <h3 class="text-[10px] font-black uppercase tracking-widest text-outline dark:text-slate-500 mb-6">Paramètres Initiaux</h3>
                <div class="flex items-center justify-between mb-6">
                    <span class="font-bold text-xs text-on-surface dark:text-white uppercase tracking-wider">Compte Actif</span>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="status" value="active" checked class="sr-only peer">
                        <div class="w-11 h-6 bg-slate-200 dark:bg-slate-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                    </label>
                </div>
                <div class="p-4 bg-primary/5 rounded-xl border border-primary/10">
                    <p class="text-[10px] text-primary leading-relaxed font-bold uppercase tracking-widest">
                        Un email de bienvenue sera envoyé automatiquement au collaborateur.
                    </p>
                </div>
            </div>

            <div class="bg-surface-container-low dark:bg-slate-900/50 p-6 rounded-xl border border-black/5 dark:border-white/5">
                <h3 class="text-[10px] font-black uppercase tracking-widest text-outline dark:text-slate-500 mb-6">Héritage des Accès</h3>
                <p class="text-[10px] text-outline dark:text-slate-500 leading-relaxed font-medium">
                    Par défaut, ce nouvel utilisateur n'aura accès qu'à  ses propres fichiers et aux dossiers partagés en lecture seule.
                </p>
            </div>
        </div>
    </form>
</div>
@endsection

