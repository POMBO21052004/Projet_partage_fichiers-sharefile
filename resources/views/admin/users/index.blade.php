@extends('admin.layouts.app')

@section('title', 'Gestion des Utilisateurs')

@section('content')
<div class="space-y-8">
    <!-- Résumé des statistiques -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white dark:bg-slate-900 p-6 rounded-xl ring-1 ring-black/5 dark:ring-white/10 flex items-center justify-between shadow-sm">
            <div>
                <p class="text-[10px] font-black uppercase tracking-widest text-outline dark:text-slate-500 mb-1">Total Utilisateurs</p>
                <h3 class="text-3xl font-extrabold tracking-tight text-on-background dark:text-white">{{ $stats['total'] }}</h3>
            </div>
            <div class="w-12 h-12 rounded-xl bg-primary/10 flex items-center justify-center text-primary">
                <span class="material-symbols-outlined text-2xl">groups</span>
            </div>
        </div>
        <div class="bg-white dark:bg-slate-900 p-6 rounded-xl ring-1 ring-black/5 dark:ring-white/10 flex items-center justify-between shadow-sm">
            <div>
                <p class="text-[10px] font-black uppercase tracking-widest text-outline dark:text-slate-500 mb-1">Comptes Actifs</p>
                <h3 class="text-3xl font-extrabold tracking-tight text-on-background dark:text-white">{{ $stats['active'] }}</h3>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-500/10 flex items-center justify-center text-emerald-500">
                <span class="material-symbols-outlined text-2xl">how_to_reg</span>
            </div>
        </div>
        <div class="bg-white dark:bg-slate-900 p-6 rounded-xl ring-1 ring-black/5 dark:ring-white/10 flex items-center justify-between shadow-sm">
            <div>
                <p class="text-[10px] font-black uppercase tracking-widest text-outline dark:text-slate-500 mb-1">Non vérifiés</p>
                <h3 class="text-3xl font-extrabold tracking-tight text-on-background dark:text-white">{{ $stats['pending'] }}</h3>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-500/10 flex items-center justify-center text-amber-500">
                <span class="material-symbols-outlined text-2xl">pending_actions</span>
            </div>
        </div>
    </div>

    <!-- Actions & Filtres -->
    <div class="flex flex-col md:flex-row justify-between items-center gap-4 bg-surface-container-low dark:bg-slate-900/50 p-4 rounded-xl ring-1 ring-black/5 dark:ring-white/10">
        <h3 class="text-xs font-black uppercase tracking-widest text-outline px-4">Utilisateurs Standards</h3>
        <a href="{{ route('admin.users.create') }}" class="flex items-center gap-2 bg-primary text-white px-6 py-3 rounded-xl font-black text-[10px] uppercase tracking-widest shadow-lg shadow-primary/20 hover:opacity-90 transition-all active:scale-95">
            <span class="material-symbols-outlined text-sm">person_add</span> Nouvel Utilisateur
        </a>
    </div>

    <!-- Table des Utilisateurs -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm overflow-hidden ring-1 ring-black/5 dark:ring-white/10">
        <div class="overflow-x-auto w-full">
            <table class="w-full text-left whitespace-nowrap min-w-max">
                <thead class="bg-surface-container-low dark:bg-slate-800/50">
                    <tr>
                        <th class="px-8 py-5 text-[10px] font-black uppercase tracking-widest text-outline">Identité</th>
                        <th class="px-8 py-5 text-[10px] font-black uppercase tracking-widest text-outline text-center">Vérification</th>
                        <th class="px-8 py-5 text-[10px] font-black uppercase tracking-widest text-outline">Statut</th>
                        <th class="px-8 py-5 text-[10px] font-black uppercase tracking-widest text-outline text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-black/5 dark:divide-white/5">
                    @forelse($users as $user)
                    <tr class="hover:bg-primary/5 dark:hover:bg-blue-400/5 transition-colors group">
                        <td class="px-8 py-6">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-500 dark:text-slate-400 font-bold text-xs shadow-sm ring-1 ring-black/5 dark:ring-white/5 shrink-0">
                                    {{ strtoupper(substr($user->name, 0, 2)) }}
                                </div>
                                <div class="min-w-0">
                                    <a href="{{ route('admin.users.show', $user) }}" class="font-bold text-on-surface dark:text-white hover:text-primary transition-colors block truncate max-w-[200px] sm:max-w-xs md:max-w-md" title="{{ $user->name }}">{{ $user->name }}</a>
                                    <p class="text-xs text-outline dark:text-slate-500 block truncate max-w-[200px] sm:max-w-xs md:max-w-md" title="{{ $user->email }}">{{ $user->email }}</p>
                                </div>
                            </div>
                        </td>
                    <td class="px-8 py-6 text-center">
                        @if($user->is_verified)
                            <span class="px-3 py-1.5 bg-emerald-50 text-emerald-600 dark:bg-emerald-950/20 dark:text-emerald-400 text-[9px] font-black uppercase tracking-[0.1em] rounded-lg border border-emerald-500/20 shadow-sm">Vérifié</span>
                        @else
                            <span class="px-3 py-1.5 bg-amber-50 text-amber-600 dark:bg-amber-950/20 dark:text-amber-400 text-[9px] font-black uppercase tracking-[0.1em] rounded-lg border border-amber-500/20 shadow-sm">Non vérifié</span>
                        @endif
                    </td>
                    <td class="px-8 py-6">
                        <form action="{{ route('admin.users.toggle-status', $user) }}" method="POST" class="m-0">
                            @csrf
                            <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border text-[9px] font-black uppercase tracking-[0.1em] transition-all hover:scale-105 active:scale-95 shadow-sm {{ $user->status === 'active' ? 'bg-emerald-500/10 text-emerald-600 border-emerald-500/20' : 'bg-slate-100 text-slate-500 border-slate-300 dark:bg-slate-800 dark:text-slate-400 dark:border-slate-700' }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $user->status === 'active' ? 'bg-emerald-500 animate-pulse' : 'bg-slate-400 dark:bg-slate-600' }}"></span>
                                {{ $user->status === 'active' ? 'Actif' : 'Inactif' }}
                            </button>
                        </form>
                    </td>
                    <td class="px-8 py-6 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('admin.users.show', $user) }}" class="p-2 text-slate-400 hover:text-primary dark:hover:text-blue-400 hover:bg-primary/5 rounded-xl transition-all" title="Détails">
                                <span class="material-symbols-outlined text-lg">visibility</span>
                            </a>
                            <a href="{{ route('admin.users.edit', $user) }}" class="p-2 text-slate-400 hover:text-primary dark:hover:text-blue-400 hover:bg-primary/5 rounded-xl transition-all" title="Modifier">
                                <span class="material-symbols-outlined text-lg">edit</span>
                            </a>
                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="m-0" onsubmit="return confirm('Supprimer cet utilisateur ?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-900/20 rounded-xl transition-all" title="Supprimer">
                                    <span class="material-symbols-outlined text-lg">person_remove</span>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-8 py-20 text-center text-outline italic text-sm">Aucun utilisateur standard trouvé.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>

    @if($users->hasPages())
        <div class="py-6">
            {{ $users->links() }}
        </div>
    @endif
</div>
@endsection
