@extends('admin.layouts.app')

@section('title', $file->name)

@section('content')
<div class="space-y-8">
    <!-- Breadcrumb & Title -->
    <div class="flex flex-col md:flex-row justify-between items-end gap-6">
        <div>
            <nav class="flex items-center space-x-2 text-[10px] font-black uppercase tracking-widest text-outline mb-2">
                <a href="{{ route('admin.explorer') }}" class="hover:text-primary transition-colors">Coffre-fort</a>
                @if($file->folder)
                    <span class="material-symbols-outlined text-[10px]">chevron_right</span>
                    <a href="{{ route('admin.explorer', $file->folder->id) }}" class="hover:text-primary transition-colors">{{ $file->folder->name }}</a>
                @endif
                <span class="material-symbols-outlined text-[10px]">chevron_right</span>
                <span class="text-primary dark:text-blue-400">{{ $file->name }}</span>
            </nav>
            <h1 class="text-3xl font-black tracking-tight text-on-surface dark:text-white">{{ $file->name }}</h1>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.files.download', $file) }}" class="flex items-center gap-2 px-6 py-2.5 bg-primary text-white rounded-xl text-[10px] font-black uppercase tracking-widest hover:opacity-90 transition-all shadow-lg shadow-primary/20">
                <span class="material-symbols-outlined text-sm">download</span>
                <span>Télécharger</span>
            </a>
            <form action="{{ route('admin.files.destroy', $file) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer définitivement ce fichier ? Cette action est irréversible.')">
                @csrf
                @method('DELETE')
                <button type="submit" class="p-2.5 bg-rose-50 dark:bg-rose-900/10 text-rose-600 dark:text-rose-400 rounded-xl hover:bg-rose-100 transition-all border border-rose-100 dark:border-rose-900/20 shadow-sm">
                    <span class="material-symbols-outlined">delete</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Content Grid -->
    <div class="grid grid-cols-12 gap-8">
        <!-- Main Preview Area (Bento-style large card) -->
        <div class="col-span-12 lg:col-span-8">
            <div class="bg-white dark:bg-slate-900 rounded-2xl overflow-hidden shadow-sm border border-outline-variant/10 dark:border-slate-800 flex flex-col min-h-[85vh]">
                <div class="p-4 bg-surface-container-low dark:bg-slate-800/50 flex justify-between items-center border-b border-outline-variant/5">
                    <div class="flex items-center gap-4">
                        <div class="flex items-center bg-white dark:bg-slate-900 px-3 py-1 rounded-lg border border-outline-variant/20">
                            <span class="text-[10px] font-black text-primary dark:text-blue-400 mr-2 uppercase tracking-widest">Type</span>
                            <span class="text-[10px] font-black text-outline uppercase tracking-wider">{{ $file->extension }}</span>
                        </div>
                        <div class="h-4 w-px bg-outline-variant/30"></div>
                        <span class="text-xs font-bold text-on-surface-variant dark:text-slate-400">Visionneuse Intégrée</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <button class="p-1.5 hover:bg-white dark:hover:bg-slate-800 rounded text-outline transition-all"><span class="material-symbols-outlined text-lg">zoom_in</span></button>
                        <button class="p-1.5 hover:bg-white dark:hover:bg-slate-800 rounded text-outline transition-all"><span class="material-symbols-outlined text-lg">fullscreen</span></button>
                    </div>
                </div>
                
                {{-- Logic Preview Area --}}
                <div class="flex-grow bg-slate-50 dark:bg-slate-950 relative flex items-center justify-center overflow-hidden">
                    @php
                        $ext = strtolower($file->extension);
                        $isOffice = in_array($ext, ['doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx']);
                        $isVideo = in_array($ext, ['mp4', 'webm', 'ogg']);
                        $isAudio = in_array($ext, ['mp3', 'wav', 'ogg']);
                        $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                    @endphp

                    @if($isOffice)
                        <div class="text-center p-12">
                            <div class="w-20 h-20 bg-primary/10 rounded-3xl flex items-center justify-center text-primary mx-auto mb-6 shadow-inner">
                                <span class="material-symbols-outlined text-4xl">description</span>
                            </div>
                            <h4 class="text-lg font-black text-on-surface dark:text-white mb-2">Aperçu Office Indisponible</h4>
                            <p class="text-xs text-outline dark:text-slate-500 mb-6 max-w-xs mx-auto">Le format {{ strtoupper($ext) }} nécessite un environnement de production ou le téléchargement local.</p>
                            <a href="{{ route('admin.files.download', $file) }}" class="inline-flex items-center gap-2 px-6 py-3 bg-primary text-white text-[10px] font-black uppercase tracking-widest rounded-xl shadow-lg shadow-primary/20">
                                <span class="material-symbols-outlined text-sm">download</span> Télécharger pour voir
                            </a>
                        </div>
                    @elseif($isVideo)
                        <video controls class="w-full h-full object-contain bg-black shadow-2xl">
                            <source src="{{ route('admin.files.preview', $file) }}" type="video/{{ $ext === 'mp4' ? 'mp4' : $ext }}">
                        </video>
                    @elseif($isAudio)
                        <div class="flex flex-col items-center gap-6">
                            <div class="w-24 h-24 bg-gradient-to-br from-primary to-primary-container rounded-full flex items-center justify-center text-white shadow-xl animate-pulse">
                                <span class="material-symbols-outlined text-5xl">audiotrack</span>
                            </div>
                            <audio controls class="w-80 custom-audio shadow-lg">
                                <source src="{{ route('admin.files.preview', $file) }}" type="audio/{{ $ext === 'mp3' ? 'mpeg' : $ext }}">
                            </audio>
                        </div>
                    @elseif($isImage)
                        <img src="{{ route('admin.files.preview', $file) }}" class="max-w-full max-h-full object-contain p-4 drop-shadow-2xl">
                    @else
                        <iframe src="{{ route('admin.files.preview', $file) }}" class="w-full h-full min-h-[85vh] border-none"></iframe>
                    @endif

                    <!-- Architectural Grid Overlay -->
                    <div class="absolute inset-0 pointer-events-none opacity-[0.03]" style="background-image: linear-gradient(var(--tw-primary) 1px, transparent 1px), linear-gradient(90deg, var(--tw-primary) 1px, transparent 1px); background-size: 50px 50px;"></div>
                </div>
            </div>
        </div>

        <!-- Sidebar Metadata & Access -->
        <div class="col-span-12 lg:col-span-4 space-y-8">
            <!-- File Info Card -->
            <section class="bg-white dark:bg-slate-900 p-8 rounded-2xl border border-outline-variant/10 dark:border-slate-800 shadow-sm">
                <h3 class="text-[10px] font-black uppercase tracking-[0.2em] text-outline dark:text-slate-500 mb-8">Métadonnées de l'Actif</h3>
                <div class="space-y-4">
                    <div class="flex justify-between items-center py-4 border-b border-black/5 dark:border-white/5">
                        <span class="text-xs font-bold text-outline dark:text-slate-400">Type d'actif</span>
                        <span class="text-xs font-black text-on-surface dark:text-white uppercase">{{ $file->extension }}</span>
                    </div>
                    <div class="flex justify-between items-center py-4 border-b border-black/5 dark:border-white/5">
                        <span class="text-xs font-bold text-outline dark:text-slate-400">Poids</span>
                        <span class="text-xs font-black text-on-surface dark:text-white">{{ number_format($file->size / 1024 / 1024, 2) }} Mo</span>
                    </div>
                    <div class="flex justify-between items-center py-4 border-b border-black/5 dark:border-white/5">
                        <span class="text-xs font-bold text-outline dark:text-slate-400">Déposé le</span>
                        <span class="text-xs font-black text-on-surface dark:text-white">{{ $file->created_at->format('d M Y') }}</span>
                    </div>
                    <div class="flex justify-between items-center py-4">
                        <span class="text-xs font-bold text-outline dark:text-slate-400">Propriétaire</span>
                        <div class="flex items-center gap-2">
                            <div class="w-5 h-5 rounded-full bg-primary-fixed text-primary text-[8px] font-black flex items-center justify-center">
                                {{ strtoupper(substr($file->user->name ?? 'S', 0, 1)) }}
                            </div>
                            <span class="text-xs font-black text-on-surface dark:text-white">{{ $file->user->name ?? 'Système' }}</span>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Access Management Card -->
            <section class="bg-white dark:bg-slate-900 p-8 rounded-2xl border border-outline-variant/10 dark:border-slate-800 shadow-sm relative overflow-hidden">
                <div class="flex justify-between items-center mb-8 relative z-10">
                    <h3 class="text-[10px] font-black uppercase tracking-[0.2em] text-outline dark:text-slate-500">Contrôle d'Accès</h3>
                    <a href="{{ route('admin.files.permissions', $file) }}" class="text-primary dark:text-blue-400 text-[10px] font-black uppercase tracking-widest hover:underline">Gérer</a>
                </div>
                
                <div class="space-y-6 relative z-10">
                    <!-- Owner (Default) -->
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-2xl bg-primary-fixed dark:bg-blue-900/30 flex items-center justify-center text-primary dark:text-blue-400 font-black">
                                AD
                            </div>
                            <div>
                                <p class="text-xs font-bold text-on-surface dark:text-white">Administrateurs</p>
                                <p class="text-[10px] text-outline dark:text-slate-500 font-medium">Accès Racine</p>
                            </div>
                        </div>
                        <span class="text-[9px] font-black px-2 py-1 bg-surface-container-high dark:bg-slate-800 rounded-lg uppercase tracking-wider text-primary dark:text-blue-400">Root</span>
                    </div>

                    @forelse($file->sharedWith as $user)
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-2xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-500 dark:text-slate-400 font-black">
                                    {{ strtoupper(substr($user->name, 0, 2)) }}
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-on-surface dark:text-white">{{ $user->name }}</p>
                                    <p class="text-[10px] text-outline dark:text-slate-500 font-medium italic">Lecteur Autorisé</p>
                                </div>
                            </div>
                            <span class="material-symbols-outlined text-emerald-500 text-sm">verified_user</span>
                        </div>
                    @empty
                    @endforelse

                    <a href="{{ route('admin.files.permissions', $file) }}" class="w-full mt-4 py-4 border border-dashed border-outline-variant/30 dark:border-slate-800 text-outline dark:text-slate-500 rounded-2xl text-[9px] font-black uppercase tracking-widest hover:bg-primary/5 hover:text-primary dark:hover:text-blue-400 hover:border-primary/50 transition-all flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined text-lg">person_add</span>
                        <span>Accorder un Accès Sécurisé</span>
                    </a>
                </div>

                <!-- Abstract Decorative Background -->
                <div class="absolute -bottom-10 -right-10 w-40 h-40 bg-primary/5 rounded-full blur-3xl"></div>
            </section>
        </div>
    </div>
</div>
@endsection

