@props(['paginator'])

@if ($paginator->hasPages())
    <div class="flex justify-center">
        <nav class="flex items-center gap-1" role="navigation" aria-label="Pagination Navigation">
            {{-- Tombol Previous --}}
            @if ($paginator->onFirstPage())
                <span class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-gray-300 dark:text-gray-600 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 cursor-not-allowed">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M9.4 13.4l1.4-1.4-4-4 4-4-1.4-1.4L4 8z" />
                    </svg>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}"
                   class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-violet-500 dark:text-violet-400 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 hover:bg-violet-50 dark:hover:bg-gray-700 transition-colors shadow-sm"
                   aria-label="Sebelumnya">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M9.4 13.4l1.4-1.4-4-4 4-4-1.4-1.4L4 8z" />
                    </svg>
                </a>
            @endif

            {{-- Nomor Halaman --}}
            @php
                $current = $paginator->currentPage();
                $last = $paginator->lastPage();
                $start = max(1, $current - 2);
                $end = min($last, $current + 2);

                // Pastikan selalu tampilkan minimal 5 halaman jika tersedia
                if ($end - $start < 4 && $last >= 5) {
                    if ($start == 1) {
                        $end = min($last, 5);
                    } elseif ($end == $last) {
                        $start = max(1, $last - 4);
                    }
                }
            @endphp

            {{-- Halaman 1 + ellipsis --}}
            @if ($start > 1)
                <a href="{{ $paginator->url(1) }}"
                   class="inline-flex items-center justify-center min-w-[2.25rem] h-9 px-3 rounded-lg text-sm font-medium text-gray-600 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors shadow-sm">
                    1
                </a>
                @if ($start > 2)
                    <span class="inline-flex items-center justify-center w-9 h-9 text-sm text-gray-400 dark:text-gray-500">...</span>
                @endif
            @endif

            {{-- Range halaman --}}
            @for ($i = $start; $i <= $end; $i++)
                @if ($i == $current)
                    <span class="inline-flex items-center justify-center min-w-[2.25rem] h-9 px-3 rounded-lg text-sm font-bold text-white bg-violet-600 border border-violet-600 shadow-sm">
                        {{ $i }}
                    </span>
                @else
                    <a href="{{ $paginator->url($i) }}"
                       class="inline-flex items-center justify-center min-w-[2.25rem] h-9 px-3 rounded-lg text-sm font-medium text-gray-600 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors shadow-sm"
                       aria-label="Halaman {{ $i }}">
                        {{ $i }}
                    </a>
                @endif
            @endfor

            {{-- Ellipsis + halaman terakhir --}}
            @if ($end < $last)
                @if ($end < $last - 1)
                    <span class="inline-flex items-center justify-center w-9 h-9 text-sm text-gray-400 dark:text-gray-500">...</span>
                @endif
                <a href="{{ $paginator->url($last) }}"
                   class="inline-flex items-center justify-center min-w-[2.25rem] h-9 px-3 rounded-lg text-sm font-medium text-gray-600 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors shadow-sm"
                   aria-label="Halaman {{ $last }}">
                    {{ $last }}
                </a>
            @endif

            {{-- Tombol Next --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}"
                   class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-violet-500 dark:text-violet-400 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 hover:bg-violet-50 dark:hover:bg-gray-700 transition-colors shadow-sm"
                   aria-label="Selanjutnya">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M6.6 13.4L5.2 12l4-4-4-4 1.4-1.4L12 8z" />
                    </svg>
                </a>
            @else
                <span class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-gray-300 dark:text-gray-600 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 cursor-not-allowed">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M6.6 13.4L5.2 12l4-4-4-4 1.4-1.4L12 8z" />
                    </svg>
                </span>
            @endif
        </nav>
    </div>
@endif
