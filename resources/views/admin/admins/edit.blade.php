@extends('admin.layouts.app')

@section('title', 'Éditer Admin : ' . $admin->name)

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Breadcrumbs & Header -->
    <div class="mb-8">
        <nav class="flex items-center gap-2 text-[10px] font-black uppercase tracking-widest text-outline dark:text-slate-500 mb-3">
            <a href="{{ route('admin.admins.index') }}" class="cursor-pointer hover:text-primary transition-colors">Administrateurs</a>
            <span class="material-symbols-outlined text-[12px]">chevron_right</span>
            <span class="text-on-surface dark:text-white">Paramètres Privilégiés</span>
        </nav>
        
        <div class="flex items-end gap-6">
            <div class="relative group">
                <div class="w-24 h-24 rounded-xl overflow-hidden bg-primary/10 dark:bg-blue-900/30 flex items-center justify-center text-primary dark:text-blue-400 font-black text-2xl shadow-sm ring-1 ring-primary/20">
                    {{ strtoupper(substr($admin->name, 0, 2)) }}
                </div>
                <button class="absolute -bottom-2 -right-2 w-8 h-8 bg-primary text-white rounded-lg flex items-center justify-center shadow-lg hover:scale-105 transition-transform">
                    <span class="material-symbols-outlined text-sm">shield</span>
                </button>
            </div>
            <div class="flex-1">
                <h2 class="text-3xl font-black tracking-tight text-on-surface dark:text-white">{{ $admin->name }}</h2>
                <p class="text-outline dark:text-slate-500 text-sm font-medium">Configuration des accès root et des identifiants de sécurité.</p>
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

    <form action="{{ route('admin.admins.update', $admin) }}" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-8">
        @csrf
        @method('PUT')
        
        <!-- Primary Settings Card -->
        <div class="md:col-span-2 space-y-6">
            <div class="bg-white dark:bg-slate-900 p-8 rounded-xl shadow-sm border border-black/5 dark:border-white/5">
                <h3 class="text-xs font-black text-on-surface dark:text-white mb-8 flex items-center gap-2 uppercase tracking-widest">
                    <span class="material-symbols-outlined text-primary">admin_panel_settings</span>
                    Profil Administrateur
                </h3>
                
                <div class="space-y-6">
                    <div class="grid grid-cols-1 gap-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-outline dark:text-slate-500 px-1">Identifiant Public</label>
                        <input name="name" class="w-full px-5 py-4 bg-surface-container-low dark:bg-slate-800 border-none rounded-xl focus:ring-2 focus:ring-primary/20 transition-all text-sm font-bold text-on-surface dark:text-white outline-none" type="text" value="{{ old('name', $admin->name) }}" required/>
                    </div>
                    
                    <div class="grid grid-cols-1 gap-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-outline dark:text-slate-500 px-1">Email Institutionnel</label>
                        <div class="relative">
                            <input class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-800/50 border-none rounded-xl text-sm font-bold text-outline dark:text-slate-600 cursor-not-allowed outline-none" readonly type="email" value="{{ $admin->email }}"/>
                            <span class="material-symbols-outlined absolute right-4 top-1/2 -translate-y-1/2 text-outline/40 text-sm">verified</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-2 pt-4 border-t border-black/5 dark:border-white/5 mt-4">
                        <label class="text-[10px] font-black uppercase tracking-widest text-outline dark:text-slate-500 px-1">Modifier Mot de Passe</label>
                        <input name="password" class="w-full px-5 py-4 bg-surface-container-low dark:bg-slate-800 border-none rounded-xl focus:ring-2 focus:ring-primary/20 transition-all text-sm font-bold text-on-surface dark:text-white outline-none" type="password" placeholder="Conserver l'actuel"/>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-end gap-4 pt-4">
                <a href="{{ route('admin.admins.index') }}" class="px-8 py-4 rounded-xl text-[10px] font-black uppercase tracking-widest text-outline hover:bg-surface-container-high transition-colors">
                    Fermer
                </a>
                <button type="submit" class="px-10 py-4 bg-primary text-white rounded-xl text-[10px] font-black uppercase tracking-widest shadow-lg shadow-primary/20 hover:opacity-90 active:scale-95 transition-all">
                    Appliquer les changements
                </button>
            </div>
        </div>

        <!-- Sidebar Configuration -->
        <div class="space-y-6">
            <!-- Account Type Info -->
            <div class="bg-primary p-8 rounded-xl shadow-xl shadow-primary/20 relative overflow-hidden group">
                <div class="relative z-10 space-y-4">
                    <div class="flex items-center gap-3 text-white/90">
                        <span class="material-symbols-outlined text-2xl">verified_user</span>
                        <span class="font-black text-xs uppercase tracking-[0.2em]">Compte Root</span>
                    </div>
                    <p class="text-[10px] text-white/80 font-bold uppercase tracking-widest leading-relaxed">
                        Ce compte possède les privilèges maximum. Les actions sont auditées en temps réel.
                    </p>
                </div>
                <span class="material-symbols-outlined absolute -right-4 -bottom-4 text-7xl text-white/10 select-none group-hover:scale-110 transition-transform duration-700">security</span>
            </div>

            <!-- Permissions Checklist -->
            <div class="bg-surface-container-low dark:bg-slate-900/50 p-6 rounded-xl border border-black/5 dark:border-white/5">
                <h3 class="text-[10px] font-black uppercase tracking-widest text-outline dark:text-slate-500 mb-6">Niveau d'Autorisation</h3>
                <div class="space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="w-5 h-5 flex items-center justify-center rounded bg-emerald-500 text-white">
                            <span class="material-symbols-outlined text-[14px]">check</span>
                        </div>
                        <span class="text-xs font-black text-on-surface dark:text-white uppercase tracking-widest">Full CRUD Access</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-5 h-5 flex items-center justify-center rounded bg-emerald-500 text-white">
                            <span class="material-symbols-outlined text-[14px]">check</span>
                        </div>
                        <span class="text-xs font-black text-on-surface dark:text-white uppercase tracking-widest">User Management</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-5 h-5 flex items-center justify-center rounded bg-emerald-500 text-white">
                            <span class="material-symbols-outlined text-[14px]">check</span>
                        </div>
                        <span class="text-xs font-black text-on-surface dark:text-white uppercase tracking-widest">System Logs</span>
                    </div>
                </div>
            </div>

            <!-- Danger Zone (if not self) -->
            @if($admin->id !== auth()->id())
            <div class="bg-rose-500/10 p-6 rounded-xl border border-rose-500/20">
                <h3 class="text-[10px] font-black uppercase tracking-widest text-rose-600 mb-4">Zone de Danger</h3>
                <p class="text-[10px] text-rose-600/70 mb-4 font-bold uppercase tracking-widest">La suppression d'un compte admin est irréversible.</p>
                <button type="button" class="w-full py-3 bg-rose-600 text-white text-[10px] font-black uppercase tracking-widest rounded-lg hover:bg-rose-700 transition-colors">
                    Révoquer Admin
                </button>
            </div>
            @endif
        </div>
    </form>
</div>
@endsection
