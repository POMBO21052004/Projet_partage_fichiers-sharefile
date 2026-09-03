@extends('admin.layouts.app')

@section('title', 'Aperçu non supporté')

@section('content')
<div class="h-[60vh] flex flex-col items-center justify-center text-center space-y-8">
    <div class="relative">
        <div class="w-32 h-32 bg-amber-500/10 rounded-full flex items-center justify-center text-amber-500">
            <span class="material-symbols-outlined text-6xl">visibility_off</span>
        </div>
        <div class="absolute -bottom-2 -right-2 w-12 h-12 bg-white dark:bg-slate-900 rounded-2xl shadow-xl flex items-center justify-center text-amber-500 ring-4 ring-surface dark:ring-slate-950">
            <span class="material-symbols-outlined">warning</span>
        </div>
    </div>

    <div class="max-w-md space-y-2">
        <h2 class="text-2xl font-black text-on-surface dark:text-white tracking-tight">Aperçu indisponible</h2>
        <p class="text-sm text-outline font-medium leading-relaxed">
            Le format de fichier <span class="font-black text-on-surface dark:text-white uppercase">{{ $file->extension }}</span> 
            ({{ $mimeType }}) ne peut pas être visualisé directement dans votre navigateur.
        </p>
    </div>

    <div class="flex items-center gap-4">
        <a href="{{ route('admin.files.download', $file) }}" class="flex items-center gap-3 px-8 py-4 bg-primary text-white text-[10px] font-black uppercase tracking-[0.2em] rounded-2xl shadow-xl shadow-primary/20 hover:scale-105 transition-all active:scale-95">
            <span class="material-symbols-outlined">download</span>
            Télécharger pour voir
        </a>
        <a href="{{ url()->previous() }}" class="flex items-center gap-3 px-8 py-4 bg-white dark:bg-slate-900 ring-1 ring-black/5 dark:ring-white/10 text-outline text-[10px] font-black uppercase tracking-[0.2em] rounded-2xl hover:bg-slate-50 transition-all">
            Retour
        </a>
    </div>
</div>
@endsection
