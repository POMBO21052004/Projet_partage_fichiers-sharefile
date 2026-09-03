@extends('admin.layouts.app')

@section('title', 'Paramètres de mon Profil')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="mb-8">
        <nav class="flex items-center gap-2 text-[10px] font-black uppercase tracking-widest text-outline dark:text-slate-500 mb-3">
            <a href="{{ route('admin.profile.show') }}" class="cursor-pointer hover:text-primary transition-colors">Mon Profil</a>
            <span class="material-symbols-outlined text-[12px]">chevron_right</span>
            <span class="text-on-surface dark:text-white">Édition des accès</span>
        </nav>
        
        <div class="flex items-end gap-6">
            <div class="relative group">
                <div class="w-24 h-24 rounded-xl overflow-hidden bg-primary/10 dark:bg-blue-900/30 flex items-center justify-center text-primary dark:text-blue-400 font-black text-2xl shadow-sm ring-1 ring-primary/20">
                    {{ strtoupper(substr($admin->name, 0, 2)) }}
                </div>
                <button class="absolute -bottom-2 -right-2 w-8 h-8 bg-primary text-white rounded-lg flex items-center justify-center shadow-lg hover:scale-105 transition-transform">
                    <span class="material-symbols-outlined text-sm">photo_camera</span>
                </button>
            </div>
            <div class="flex-1">
                <h2 class="text-3xl font-black tracking-tight text-on-surface dark:text-white">Paramètres de sécurité</h2>
                <p class="text-outline dark:text-slate-500 text-sm font-medium">Mettez à jour vos informations personnelles et vos identifiants root.</p>
            </div>
        </div>
    </div>

    @if($errors->any())
        <div class="mb-6 p-4 bg-rose-50 dark:bg-rose-900/10 border border-rose-200 dark:border-rose-500/20 rounded-xl">
            <ul class="list-disc list-inside text-rose-700 dark:text-rose-400 text-xs font-bold uppercase tracking-widest">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.profile.update') }}" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-8">
        @csrf
        @method('PUT')
        
        <!-- Main Form -->
        <div class="md:col-span-2 space-y-6">
            <div class="bg-white dark:bg-slate-900 p-8 rounded-xl shadow-sm border border-black/5 dark:border-white/5">
                <h3 class="text-xs font-black text-on-surface dark:text-white mb-8 flex items-center gap-2 uppercase tracking-widest">
                    <span class="material-symbols-outlined text-primary">badge</span>
                    Identifiants de profil
                </h3>
                
                <div class="space-y-6">
                    <div class="grid grid-cols-1 gap-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-outline dark:text-slate-500 px-1">Nom d'Administrateur</label>
                        <input name="name" class="w-full px-5 py-4 bg-surface-container-low dark:bg-slate-800 border-none rounded-xl focus:ring-2 focus:ring-primary/20 transition-all text-sm font-bold text-on-surface dark:text-white outline-none" type="text" value="{{ old('name', $admin->name) }}" required/>
                    </div>
                    
                    <div class="grid grid-cols-1 gap-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-outline dark:text-slate-500 px-1">Adresse Email (Non modifiable)</label>
                        <div class="relative">
                            <input class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-800/50 border-none rounded-xl text-sm font-bold text-outline dark:text-slate-600 cursor-not-allowed outline-none" readonly type="email" value="{{ $admin->email }}"/>
                            <span class="material-symbols-outlined absolute right-4 top-1/2 -translate-y-1/2 text-outline/40 text-sm">lock</span>
                        </div>
                    </div>

                    <div class="pt-6 border-t border-black/5 dark:border-white/5 space-y-6">
                        <div class="grid grid-cols-1 gap-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-outline dark:text-slate-500 px-1">Nouveau Mot de Passe Root</label>
                            <input name="password" class="w-full px-5 py-4 bg-surface-container-low dark:bg-slate-800 border-none rounded-xl focus:ring-2 focus:ring-primary/20 transition-all text-sm font-bold text-on-surface dark:text-white outline-none" type="password" placeholder="••••••••"/>
                        </div>
                        <div class="grid grid-cols-1 gap-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-outline dark:text-slate-500 px-1">Confirmer Mot de Passe Root</label>
                            <input name="password_confirmation" class="w-full px-5 py-4 bg-surface-container-low dark:bg-slate-800 border-none rounded-xl focus:ring-2 focus:ring-primary/20 transition-all text-sm font-bold text-on-surface dark:text-white outline-none" type="password" placeholder="••••••••"/>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-end gap-4 pt-4">
                <a href="{{ route('admin.profile.show') }}" class="px-8 py-4 rounded-xl text-[10px] font-black uppercase tracking-widest text-outline hover:bg-surface-container-high transition-colors">
                    Annuler
                </a>
                <button type="submit" class="px-10 py-4 bg-primary text-white rounded-xl text-[10px] font-black uppercase tracking-widest shadow-lg shadow-primary/20 hover:opacity-90 active:scale-95 transition-all">
                    Enregistrer les accès
                </button>
            </div>
        </div>

        <!-- Sidebar Info -->
        <div class="space-y-6">
            <div class="bg-primary p-8 rounded-xl shadow-xl shadow-primary/20 relative overflow-hidden group">
                <div class="relative z-10 space-y-4">
                    <div class="flex items-center gap-3 text-white/90">
                        <span class="material-symbols-outlined text-2xl">shield</span>
                        <span class="font-black text-xs uppercase tracking-[0.2em]">Sécurité Maître</span>
                    </div>
                    <p class="text-[10px] text-white/80 font-bold uppercase tracking-widest leading-relaxed">
                        Toute modification de mot de passe sera enregistrée dans le registre d'audit global du système.
                    </p>
                </div>
                <span class="material-symbols-outlined absolute -right-4 -bottom-4 text-7xl text-white/10 select-none group-hover:scale-110 transition-transform duration-700">admin_panel_settings</span>
            </div>

            <div class="bg-surface-container-low dark:bg-slate-900/50 p-6 rounded-xl border border-black/5 dark:border-white/5">
                <h3 class="text-[10px] font-black uppercase tracking-widest text-outline dark:text-slate-500 mb-6">Résumé des privilèges</h3>
                <div class="space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="w-4 h-4 rounded-full bg-emerald-500"></div>
                        <span class="text-[10px] font-black text-on-surface dark:text-white uppercase tracking-widest">Root Console</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-4 h-4 rounded-full bg-emerald-500"></div>
                        <span class="text-[10px] font-black text-on-surface dark:text-white uppercase tracking-widest">File Management</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-4 h-4 rounded-full bg-emerald-500"></div>
                        <span class="text-[10px] font-black text-on-surface dark:text-white uppercase tracking-widest">User Governance</span>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
