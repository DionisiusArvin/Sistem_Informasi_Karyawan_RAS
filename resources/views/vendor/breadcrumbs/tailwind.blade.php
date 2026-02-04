@unless ($breadcrumbs->isEmpty())
    <nav aria-label="Breadcrumb">
        <ol class="flex flex-wrap items-center gap-2 text-sm">
            @foreach ($breadcrumbs as $breadcrumb)
                <li class="flex items-center gap-2">
                    @if ($breadcrumb->url && !$loop->last)
                        <a href="{{ $breadcrumb->url }}"
                           class="inline-flex items-center gap-1 rounded-full border border-gray-200 bg-white/70 px-3 py-1 text-gray-600 shadow-sm hover:text-gray-900 hover:border-gray-300 hover:bg-white dark:border-gray-700 dark:bg-gray-800/70 dark:text-gray-300 dark:hover:text-white">
                            {{ $breadcrumb->title }}
                        </a>
                    @else
                        <span class="inline-flex items-center gap-1 rounded-full bg-gray-100 px-3 py-1 text-gray-800 dark:bg-gray-700 dark:text-gray-100">
                            {{ $breadcrumb->title }}
                        </span>
                    @endif

                    @unless ($loop->last)
                        <svg class="h-4 w-4 text-gray-400 dark:text-gray-500" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L10.94 10 7.23 6.29a.75.75 0 111.06-1.06l4.24 4.24a.75.75 0 010 1.06l-4.24 4.24a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
                        </svg>
                    @endif
                </li>
            @endforeach
        </ol>
    </nav>
@endunless
