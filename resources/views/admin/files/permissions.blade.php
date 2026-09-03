@extends('admin.layouts.app')

@section('title', 'Permissions : ' . $file->name)

@section('content')
<section class="space-y-8">
    <!-- Header Section -->
    <div class="flex items-end justify-between gap-6">
        <div class="space-y-1">
            <div class="flex items-center gap-2 text-primary dark:text-blue-400 font-black text-[10px] tracking-[0.2em] uppercase">
                <span class="material-symbols-outlined text-[16px]">folder</span>
                <span>Coffre-fort / {{ $file->folder ? $file->folder->name : 'Racine' }}</span>
            </div>
            <h2 class="text-3xl font-black tracking-tight text-on-surface dark:text-white">{{ $file->name }}</h2>
            <p class="text-outline dark:text-slate-500 text-sm font-medium">Gestion des privilèges et des accès sécurisés</p>
        </div>
        <button onclick="document.getElementById('modal-add-access').classList.remove('hidden')" class="flex items-center gap-2 bg-gradient-to-r from-primary to-primary-container text-white px-6 py-3 rounded-xl font-black text-[10px] uppercase tracking-widest shadow-md hover:shadow-primary/20 transition-all active:scale-[0.98]">
            <span class="material-symbols-outlined text-lg">person_add</span>
            Inviter des collaborateurs
        </button>
    </div>

    <!-- Bento Grid Layout -->
    <div class="grid grid-cols-12 gap-6">
        <!-- Main Access List -->
        <div class="col-span-12 lg:col-span-8 space-y-6">
            <div class="bg-white dark:bg-slate-900 rounded-2xl overflow-hidden shadow-sm border border-outline-variant/10 dark:border-slate-800">
                <div class="bg-surface-container-low dark:bg-slate-800/50 px-8 py-5 flex justify-between items-center border-b border-black/5">
                    <h3 class="font-black text-on-surface dark:text-white text-base uppercase tracking-widest">Membres autorisés</h3>
                    <span class="bg-primary-fixed dark:bg-blue-900/30 text-primary dark:text-blue-400 text-[10px] font-black px-3 py-1 rounded-full uppercase tracking-widest">{{ $file->sharedWith->count() + 1 }} ACTIFS</span>
                </div>
                
                <div class="divide-y divide-black/5 dark:divide-white/5">
                    <!-- Owner (Default) -->
                    <div class="px-8 py-5 flex items-center group hover:bg-primary/5 transition-colors">
                        <div class="w-10 h-10 rounded-2xl bg-primary text-white flex items-center justify-center font-black text-xs shadow-lg shadow-primary/20">
                            {{ strtoupper(substr($file->user->name ?? 'S', 0, 2)) }}
                        </div>
                        <div class="ml-4 flex-1">
                            <div class="font-black text-sm text-on-surface dark:text-white">{{ $file->user->name ?? 'Système' }}</div>
                            <div class="text-outline dark:text-slate-500 text-[10px] font-bold uppercase tracking-wider">Propriétaire de l'actif</div>
                        </div>
                        <div class="flex items-center gap-6">
                            <span class="px-3 py-1 bg-surface-container-high dark:bg-slate-800 text-primary dark:text-blue-400 text-[9px] font-black uppercase rounded-lg tracking-widest">Root Access</span>
                        </div>
                    </div>

                    <!-- Shared Users -->
                    @foreach($file->sharedWith as $user)
                    <div class="px-8 py-5 flex items-center group hover:bg-primary/5 transition-colors">
                        <div class="w-10 h-10 rounded-2xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-500 dark:text-slate-400 font-black text-xs">
                            {{ strtoupper(substr($user->name, 0, 2)) }}
                        </div>
                        <div class="ml-4 flex-1">
                            <div class="font-black text-sm text-on-surface dark:text-white">{{ $user->name }}</div>
                            <div class="text-outline dark:text-slate-500 text-[10px] font-bold uppercase tracking-wider">{{ $user->email }}</div>
                        </div>
                        <div class="flex items-center gap-6">
                            <select class="bg-surface-container-low dark:bg-slate-800 border-none rounded-lg text-[10px] font-black uppercase tracking-widest py-1.5 px-4 focus:ring-1 focus:ring-primary/20 cursor-pointer outline-none">
                                <option selected>Lecteur</option>
                                <option disabled>Éditeur (Bientôt)</option>
                            </select>
                            
                            <form action="{{ route('admin.files.permissions.revoke', [$file, $user]) }}" method="POST" onsubmit="return confirm('Révoquer l\'accès ?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-outline hover:text-rose-600 transition-colors p-2 rounded-xl hover:bg-rose-50 dark:hover:bg-rose-900/20">
                                    <span class="material-symbols-outlined text-lg">person_remove</span>
                                </button>
                            </form>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

        </div>

        <!-- Sidebar Content (Info Cards) -->
        <div class="col-span-12 lg:col-span-4 space-y-6">
            <!-- Information Card (Simplified Activity) -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl p-8 shadow-sm border border-outline-variant/10 dark:border-slate-800">
                <div class="flex items-center gap-3 mb-8">
                    <span class="material-symbols-outlined text-primary dark:text-blue-400">info</span>
                    <h3 class="font-black text-on-surface dark:text-white text-sm uppercase tracking-widest">Résumé des Activités</h3>
                </div>
                
                <div class="space-y-6">
                    <!-- Activity Item 1 -->
                    <div class="flex items-start gap-4 p-4 bg-surface-container-low dark:bg-slate-800/50 rounded-2xl border border-black/5">
                        <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center text-primary">
                            <span class="material-symbols-outlined text-lg">person_add</span>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-on-surface dark:text-white">Dernier accès accordé</p>
                            <p class="text-[10px] text-outline dark:text-slate-500 font-black uppercase mt-1">Il y a 2 heures • par Admin</p>
                        </div>
                    </div>

                    <!-- Activity Item 2 -->
                    <div class="flex items-start gap-4 p-4 bg-surface-container-low dark:bg-slate-800/50 rounded-2xl border border-black/5">
                        <div class="w-10 h-10 rounded-xl bg-emerald-500/10 flex items-center justify-center text-emerald-500">
                            <span class="material-symbols-outlined text-lg">verified_user</span>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-on-surface dark:text-white">Vérification d'intégrité</p>
                            <p class="text-[10px] text-outline dark:text-slate-500 font-black uppercase mt-1">Ce matin • Système</p>
                        </div>
                    </div>
                </div>

                <div class="mt-8 pt-6 border-t border-black/5 dark:border-white/5">
                </div>
            </div>

            <!-- Security Alert Card -->
            <div class="bg-gradient-to-br from-primary to-primary-container p-8 rounded-2xl shadow-xl shadow-primary/20 relative overflow-hidden group">
                <div class="relative z-10 space-y-4">
                    <div class="flex items-center gap-3 text-white/90">
                        <span class="material-symbols-outlined text-2xl">verified_user</span>
                        <span class="font-black text-xs uppercase tracking-[0.2em]">Sécurité AES-256</span>
                    </div>
                    <p class="text-xs text-white/80 font-medium leading-relaxed">
                        Ce document est protégé par un chiffrement de bout en bout. Seuls les collaborateurs listés peuvent déchiffrer cet actif.
                    </p>
                </div>
                <!-- Decorative background element -->
                <div class="absolute -right-4 -bottom-4 opacity-10 pointer-events-none group-hover:scale-110 transition-transform duration-700">
                    <span class="material-symbols-outlined text-[120px] text-white">security</span>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Modale : Accorder un accès --}}
<div id="modal-add-access" class="hidden fixed inset-0 bg-on-surface/40 backdrop-blur-sm z-[100] flex items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-900 rounded-2xl p-10 w-full max-w-md shadow-2xl ring-1 ring-black/5 dark:ring-white/5">
        <div class="w-16 h-16 bg-primary/10 rounded-2xl flex items-center justify-center text-primary mb-6 shadow-inner">
            <span class="material-symbols-outlined text-3xl">add_moderator</span>
        </div>
        <h3 class="text-2xl font-black text-on-surface dark:text-white tracking-tight mb-2">Inviter un membre</h3>
        <p class="text-sm text-outline dark:text-slate-500 font-medium mb-8">Autorisez un nouvel utilisateur à  consulter cet actif sécurisé.</p>
        
        <form action="{{ route('admin.files.permissions.grant', $file) }}" method="POST" class="space-y-6">
            @csrf
            <div>
                <label class="text-[10px] font-black uppercase tracking-[0.2em] text-outline dark:text-slate-500 mb-3 block">Sélectionner le membre</label>
                <select name="user_id" required class="w-full bg-surface-container-low dark:bg-slate-800 border-none ring-1 ring-black/5 dark:ring-white/10 rounded-2xl py-4 px-5 text-xs font-black text-on-surface dark:text-white focus:ring-2 focus:ring-primary/20 outline-none transition-all uppercase tracking-widest cursor-pointer">
                    <option value="" disabled selected>Rechercher...</option>
                    @foreach($allUsers as $user)
                        <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                    @endforeach
                </select>
            </div>

            <div class="flex gap-4 pt-4">
                <button type="button" onclick="document.getElementById('modal-add-access').classList.add('hidden')" class="flex-1 py-4 text-[10px] font-black uppercase tracking-widest text-outline hover:bg-slate-100 dark:hover:bg-slate-800 rounded-2xl transition-all">
                    Annuler
                </button>
                <button type="submit" class="flex-1 py-4 bg-primary dark:bg-blue-600 text-white text-[10px] font-black uppercase tracking-widest rounded-2xl shadow-lg shadow-primary/20 transition-all hover:scale-[0.98] active:scale-95">
                    Confirmer
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.getElementById('modal-add-access').addEventListener('click', function(e) {
        if (e.target === this) this.classList.add('hidden');
    });
</script>
@endsection

