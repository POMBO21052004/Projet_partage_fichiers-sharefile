@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="flex flex-col sm:flex-row items-center justify-between gap-4">
        <div>
            <p class="text-[10px] font-black uppercase tracking-widest text-outline dark:text-slate-500">
                Affichage de <span class="text-on-surface dark:text-white">{{ $paginator->firstItem() }}</span> à <span class="text-on-surface dark:text-white">{{ $paginator->lastItem() }}</span> sur <span class="text-on-surface dark:text-white">{{ $paginator->total() }}</span> résultats
            </p>
        </div>

        <div>
            <ul class="relative z-0 inline-flex rounded-lg shadow-sm -space-x-px">
                {{-- Previous Page Link --}}
                @if ($paginator->onFirstPage())
                    <li aria-disabled="true" aria-label="Précédent">
                        <span class="relative inline-flex items-center px-3 py-2 rounded-l-lg border border-black/5 dark:border-white/10 bg-slate-50 dark:bg-slate-900/50 text-sm font-medium text-slate-300 dark:text-slate-600 cursor-not-allowed">
                            <span class="material-symbols-outlined text-sm">chevron_left</span>
                        </span>
                    </li>
                @else
                    <li>
                        <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="relative inline-flex items-center px-3 py-2 rounded-l-lg border border-black/5 dark:border-white/10 bg-white dark:bg-slate-800 text-sm font-medium text-outline hover:bg-slate-50 dark:hover:bg-slate-700 transition-all">
                            <span class="material-symbols-outlined text-sm">chevron_left</span>
                        </a>
                    </li>
                @endif

                {{-- Pagination Elements --}}
                @foreach ($elements as $element)
                    {{-- "Three Dots" Separator --}}
                    @if (is_string($element))
                        <li aria-disabled="true">
                            <span class="relative inline-flex items-center px-4 py-2 border border-black/5 dark:border-white/10 bg-white dark:bg-slate-800 text-sm font-medium text-slate-500">{{ $element }}</span>
                        </li>
                    @endif

                    {{-- Array Of Links --}}
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <li aria-current="page">
                                    <span class="relative inline-flex items-center px-4 py-2 border border-primary bg-primary/10 text-primary text-sm font-black z-10">{{ $page }}</span>
                                </li>
                            @else
                                <li>
                                    <a href="{{ $url }}" class="relative inline-flex items-center px-4 py-2 border border-black/5 dark:border-white/10 bg-white dark:bg-slate-800 text-sm font-medium text-outline hover:bg-slate-50 dark:hover:bg-slate-700 transition-all">{{ $page }}</a>
                                </li>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                {{-- Next Page Link --}}
                @if ($paginator->hasMorePages())
                    <li>
                        <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="relative inline-flex items-center px-3 py-2 rounded-r-lg border border-black/5 dark:border-white/10 bg-white dark:bg-slate-800 text-sm font-medium text-outline hover:bg-slate-50 dark:hover:bg-slate-700 transition-all">
                            <span class="material-symbols-outlined text-sm">chevron_right</span>
                        </a>
                    </li>
                @else
                    <li aria-disabled="true" aria-label="Suivant">
                        <span class="relative inline-flex items-center px-3 py-2 rounded-r-lg border border-black/5 dark:border-white/10 bg-slate-50 dark:bg-slate-900/50 text-sm font-medium text-slate-300 dark:text-slate-600 cursor-not-allowed">
                            <span class="material-symbols-outlined text-sm">chevron_right</span>
                        </span>
                    </li>
                @endif
            </ul>
        </div>
    </nav>
@endif
