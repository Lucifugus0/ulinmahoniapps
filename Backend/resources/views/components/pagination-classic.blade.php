@props(['paginator'])

@if ($paginator->hasPages())
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        {{-- Info hasil --}}
        <div class="text-sm text-gray-500 dark:text-gray-400 text-center sm:text-left">
            Menampilkan
            <span class="font-semibold text-gray-700 dark:text-gray-200">{{ $paginator->firstItem() ?? 0 }}</span>
            -
            <span class="font-semibold text-gray-700 dark:text-gray-200">{{ $paginator->lastItem() ?? 0 }}</span>
            dari
            <span class="font-semibold text-gray-700 dark:text-gray-200">{{ $paginator->total() }}</span>
            hasil
        </div>

        {{-- Navigasi --}}
        <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center gap-1">
            {{-- Tombol First --}}
            @if ($paginator->currentPage() > 2)
                <a href="{{ $paginator->url(1) }}"
                   class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-200 transition-colors"
                   aria-label="Halaman pertama"
                   title="Halaman pertama">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
                    </svg>
                </a>
            @endif

            {{-- Tombol Previous --}}
            @if ($paginator->onFirstPage())
                <span class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm text-gray-300 dark:text-gray-600 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 cursor-not-allowed">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}"
                   class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-200 transition-colors"
                   aria-label="Sebelumnya">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
            @endif

            {{-- Nomor Halaman --}}
            @php
                $current = $paginator->currentPage();
                $last = $paginator->lastPage();
                $start = max(1, $current - 1);
                $end = min($last, $current + 1);

                // Pastikan selalu tampilkan minimal 3 halaman jika tersedia
                if ($end - $start < 2 && $last >= 3) {
                    if ($start == 1) {
                        $end = min($last, 3);
                    } elseif ($end == $last) {
                        $start = max(1, $last - 2);
                    }
                }
            @endphp

            {{-- Halaman 1 + ellipsis jika perlu --}}
            @if ($start > 1)
                <a href="{{ $paginator->url(1) }}"
                   class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm font-medium text-gray-600 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    1
                </a>
                @if ($start > 2)
                    <span class="inline-flex items-center justify-center w-9 h-9 text-sm text-gray-400 dark:text-gray-500">...</span>
                @endif
            @endif

            {{-- Range halaman tengah --}}
            @for ($i = $start; $i <= $end; $i++)
                @if ($i == $current)
                    <span class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm font-bold text-white bg-violet-600 border border-violet-600 shadow-sm">
                        {{ $i }}
                    </span>
                @else
                    <a href="{{ $paginator->url($i) }}"
                       class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm font-medium text-gray-600 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        {{ $i }}
                    </a>
                @endif
            @endfor

            {{-- Ellipsis + halaman terakhir jika perlu --}}
            @if ($end < $last)
                @if ($end < $last - 1)
                    <span class="inline-flex items-center justify-center w-9 h-9 text-sm text-gray-400 dark:text-gray-500">...</span>
                @endif
                <a href="{{ $paginator->url($last) }}"
                   class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm font-medium text-gray-600 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    {{ $last }}
                </a>
            @endif

            {{-- Tombol Next --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}"
                   class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-200 transition-colors"
                   aria-label="Selanjutnya">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            @else
                <span class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm text-gray-300 dark:text-gray-600 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 cursor-not-allowed">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </span>
            @endif

            {{-- Tombol Last --}}
            @if ($paginator->currentPage() < $last - 1)
                <a href="{{ $paginator->url($last) }}"
                   class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-200 transition-colors"
                   aria-label="Halaman terakhir"
                   title="Halaman terakhir">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"/>
                    </svg>
                </a>
            @endif
        </nav>
    </div>
@endif
