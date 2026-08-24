@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex flex-col sm:flex-row items-center justify-between gap-3 text-xs">
        
        <!-- Results Counter Text -->
        <div class="text-slate-500 text-xs font-mono-num">
            @if ($paginator->firstItem())
                <span>Menampilkan <span class="font-semibold text-slate-800">{{ $paginator->firstItem() }}</span> - <span class="font-semibold text-slate-800">{{ $paginator->lastItem() }}</span> dari <span class="font-semibold text-slate-800">{{ $paginator->total() }}</span> data</span>
            @else
                <span>Total <span class="font-semibold text-slate-800">{{ $paginator->total() }}</span> data</span>
            @endif
        </div>

        <!-- Pagination Controls -->
        <div class="inline-flex items-center gap-1 font-mono-num">
            
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <span class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-md border border-slate-200 bg-slate-50 text-slate-300 text-xs cursor-not-allowed select-none">
                    <i class="fa-solid fa-chevron-left text-[10px]"></i>
                    <span class="hidden sm:inline">Sebelumnya</span>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-md border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 hover:text-blue-600 text-xs font-medium shadow-2xs transition-colors">
                    <i class="fa-solid fa-chevron-left text-[10px]"></i>
                    <span class="hidden sm:inline">Sebelumnya</span>
                </a>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <span class="px-2 py-1 text-slate-400 font-semibold select-none text-xs">{{ $element }}</span>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="inline-flex items-center justify-center min-w-7 h-7 px-2 rounded-md bg-blue-600 text-white font-bold text-xs shadow-xs border border-blue-600 select-none">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $url }}" class="inline-flex items-center justify-center min-w-7 h-7 px-2 rounded-md bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 hover:text-blue-600 hover:border-slate-300 text-xs font-semibold shadow-2xs transition-colors">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-md border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 hover:text-blue-600 text-xs font-medium shadow-2xs transition-colors">
                    <span class="hidden sm:inline">Selanjutnya</span>
                    <i class="fa-solid fa-chevron-right text-[10px]"></i>
                </a>
            @else
                <span class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-md border border-slate-200 bg-slate-50 text-slate-300 text-xs cursor-not-allowed select-none">
                    <span class="hidden sm:inline">Selanjutnya</span>
                    <i class="fa-solid fa-chevron-right text-[10px]"></i>
                </span>
            @endif
        </div>
    </nav>
@endif
