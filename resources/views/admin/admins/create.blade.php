@extends('admin.layouts.app')

@section('title', 'Nommer un Admin')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="mb-8">
        <nav class="flex items-center gap-2 text-[10px] font-black uppercase tracking-widest text-outline dark:text-slate-500 mb-3">
            <a href="{{ route('admin.admins.index') }}" class="cursor-pointer hover:text-primary transition-colors">Équipe Admin</a>
            <span class="material-symbols-outlined text-[12px]">chevron_right</span>
            <span class="text-on-surface dark:text-white">Nouvelle Nomination</span>
        </nav>
        
        <div class="flex items-end gap-6">
            <div class="w-24 h-24 rounded-xl bg-primary/10 dark:bg-blue-900/30 flex items-center justify-center text-primary shadow-sm border-2 border-dashed border-primary/30">
                <span class="material-symbols-outlined text-4xl">admin_panel_settings</span>
            </div>
            <div class="flex-1">
                <h2 class="text-3xl font-black tracking-tight text-on-surface dark:text-white">Nommer un Administrateur</h2>
                <p class="text-outline dark:text-slate-500 text-sm font-medium">Attribuez des privilèges root à  un nouveau membre de la direction.</p>
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

    <form action="{{ route('admin.admins.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-8">
        @csrf
        
        <!-- Primary Form -->
        <div class="md:col-span-2 space-y-6">
            <div class="bg-white dark:bg-slate-900 p-8 rounded-xl shadow-sm border border-black/5 dark:border-white/5">
                <h3 class="text-xs font-black text-on-surface dark:text-white mb-8 flex items-center gap-2 uppercase tracking-widest">
                    <span class="material-symbols-outlined text-primary">security</span>
                    Identifiants de Sécurité Root
                </h3>
                
                <div class="space-y-6">
                    <div class="grid grid-cols-1 gap-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-outline dark:text-slate-500 px-1">Nom d'Administrateur</label>
                        <input name="name" class="w-full px-5 py-4 bg-surface-container-low dark:bg-slate-800 border-none rounded-xl focus:ring-2 focus:ring-primary/20 transition-all text-sm font-bold text-on-surface dark:text-white outline-none" type="text" placeholder="ex: Admin Principal" required value="{{ old('name') }}"/>
                    </div>
                    
                    <div class="grid grid-cols-1 gap-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-outline dark:text-slate-500 px-1">Email Privilégié</label>
                        <input name="email" class="w-full px-5 py-4 bg-surface-container-low dark:bg-slate-800 border-none rounded-xl focus:ring-2 focus:ring-primary/20 transition-all text-sm font-bold text-on-surface dark:text-white outline-none" type="email" placeholder="admin@fichiers.com" required value="{{ old('email') }}"/>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="grid grid-cols-1 gap-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-outline dark:text-slate-500 px-1">Mot de Passe Root</label>
                            <input name="password" class="w-full px-5 py-4 bg-surface-container-low dark:bg-slate-800 border-none rounded-xl focus:ring-2 focus:ring-primary/20 transition-all text-sm font-bold text-on-surface dark:text-white outline-none" type="password" required placeholder="••••••••"/>
                        </div>
                        <div class="grid grid-cols-1 gap-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-outline dark:text-slate-500 px-1">Confirmer Mot de Passe</label>
                            <input name="password_confirmation" class="w-full px-5 py-4 bg-surface-container-low dark:bg-slate-800 border-none rounded-xl focus:ring-2 focus:ring-primary/20 transition-all text-sm font-bold text-on-surface dark:text-white outline-none" type="password" required placeholder="••••••••"/>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-end gap-4 pt-4">
                <a href="{{ route('admin.admins.index') }}" class="px-8 py-4 rounded-xl text-[10px] font-black uppercase tracking-widest text-outline hover:bg-surface-container-high transition-colors">
                    Fermer
                </a>
                <button type="submit" class="px-10 py-4 bg-primary text-white rounded-xl text-[10px] font-black uppercase tracking-widest shadow-lg shadow-primary/20 hover:opacity-90 active:scale-95 transition-all">
                    Confirmer Nomination Root
                </button>
            </div>
        </div>

        <!-- Sidebar Config -->
        <div class="space-y-6">
            <div class="bg-primary p-8 rounded-xl shadow-xl shadow-primary/20 relative overflow-hidden group">
                <div class="relative z-10 space-y-4">
                    <div class="flex items-center gap-3 text-white/90">
                        <span class="material-symbols-outlined text-2xl">verified_user</span>
                        <span class="font-black text-xs uppercase tracking-[0.2em]">Full Root Access</span>
                    </div>
                    <p class="text-[10px] text-white/80 font-bold uppercase tracking-widest leading-relaxed">
                        Cette nomination accorde un contrôle total sur les fichiers, les membres et les journaux de sécurité.
                    </p>
                </div>
                <span class="material-symbols-outlined absolute -right-4 -bottom-4 text-7xl text-white/10 select-none">shield</span>
            </div>

            <div class="bg-surface-container-low dark:bg-slate-900/50 p-6 rounded-xl border border-black/5 dark:border-white/5">
                <h3 class="text-[10px] font-black uppercase tracking-widest text-outline dark:text-slate-500 mb-6">Autorisations par défaut</h3>
                <div class="space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="w-4 h-4 rounded-full bg-emerald-500"></div>
                        <span class="text-[10px] font-black text-on-surface dark:text-white uppercase tracking-widest">Édition Master</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-4 h-4 rounded-full bg-emerald-500"></div>
                        <span class="text-[10px] font-black text-on-surface dark:text-white uppercase tracking-widest">Audit Logs</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-4 h-4 rounded-full bg-emerald-500"></div>
                        <span class="text-[10px] font-black text-on-surface dark:text-white uppercase tracking-widest">Droit de suppression</span>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

