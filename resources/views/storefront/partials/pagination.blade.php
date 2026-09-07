@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Navegación de Paginación" class="flex items-center justify-between">

        {{-- Móviles --}}
        <div class="flex justify-between flex-1 sm:hidden gap-3">
            @if ($paginator->onFirstPage())
                <span class="inline-flex items-center px-4 py-2 text-xs font-bold tracking-widest uppercase text-neutral-600 border border-white/10 cursor-not-allowed">Anterior</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="inline-flex items-center px-4 py-2 text-xs font-bold tracking-widest uppercase text-neutral-300 border border-white/20 hover:border-rose-500 hover:text-rose-400 transition-colors">Anterior</a>
            @endif

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="inline-flex items-center px-4 py-2 text-xs font-bold tracking-widest uppercase text-neutral-300 border border-white/20 hover:border-rose-500 hover:text-rose-400 transition-colors">Siguiente</a>
            @else
                <span class="inline-flex items-center px-4 py-2 text-xs font-bold tracking-widest uppercase text-neutral-600 border border-white/10 cursor-not-allowed">Siguiente</span>
            @endif
        </div>

        {{-- Escritorio --}}
        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
            <p class="text-xs text-neutral-500 font-mono tracking-wider">
                <span class="text-neutral-300">{{ $paginator->firstItem() }}</span>–<span class="text-neutral-300">{{ $paginator->lastItem() }}</span>
                de <span class="text-neutral-300">{{ $paginator->total() }}</span>
            </p>

            <span class="inline-flex gap-1.5">
                @if ($paginator->onFirstPage())
                    <span aria-disabled="true" class="inline-flex items-center px-3 py-2 text-neutral-700 border border-white/10 cursor-not-allowed">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>
                    </span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex items-center px-3 py-2 text-neutral-400 border border-white/20 hover:border-rose-500 hover:text-rose-400 transition-colors" aria-label="Anterior">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>
                    </a>
                @endif

                @foreach ($elements as $element)
                    @if (is_string($element))
                        <span aria-disabled="true" class="inline-flex items-center px-4 py-2 text-sm font-bold text-neutral-600 cursor-default">{{ $element }}</span>
                    @endif
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <span aria-current="page" class="inline-flex items-center px-4 py-2 text-sm font-black text-black bg-white border border-white">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}" class="inline-flex items-center px-4 py-2 text-sm font-bold text-neutral-400 border border-white/20 hover:border-rose-500 hover:text-rose-400 transition-colors" aria-label="{{ __('Go to page :page', ['page' => $page]) }}">{{ $page }}</a>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex items-center px-3 py-2 text-neutral-400 border border-white/20 hover:border-rose-500 hover:text-rose-400 transition-colors" aria-label="Siguiente">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" /></svg>
                    </a>
                @else
                    <span aria-disabled="true" class="inline-flex items-center px-3 py-2 text-neutral-700 border border-white/10 cursor-not-allowed">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" /></svg>
                    </span>
                @endif
            </span>
        </div>
    </nav>
@endif
