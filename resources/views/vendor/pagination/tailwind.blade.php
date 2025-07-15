@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex items-center justify-center">
        {{-- The `flex-wrap` class is key for responsiveness on small screens --}}
        <div class="flex items-center justify-center flex-wrap bg-white rounded-full shadow-md p-1 sm:p-2">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <span class="flex items-center justify-center w-8 h-8 mx-1 text-gray-400 cursor-default">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="flex items-center justify-center w-8 h-8 mx-1 text-gray-600 rounded-full hover:bg-gray-100 transition-colors duration-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                </a>
            @endif

            {{-- Pagination Elements --}}
            {{-- This part is hidden on extra-small screens (xs) and shown from small (sm) screens up --}}
            <div class="hidden sm:flex sm:items-center">
                @foreach ($elements as $element)
                    {{-- "Three Dots" Separator --}}
                    @if (is_string($element))
                        <span class="flex items-center justify-center w-8 h-8 mx-1 text-gray-400 cursor-default">{{ $element }}</span>
                    @endif

                    {{-- Array Of Links --}}
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <span class="flex items-center justify-center w-8 h-8 mx-1 text-white bg-blue-600 rounded-full cursor-default">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}" class="flex items-center justify-center w-8 h-8 mx-1 text-gray-700 rounded-full hover:bg-gray-100 transition-colors duration-200">{{ $page }}</a>
                            @endif
                        @endforeach
                    @endif
                @endforeach
            </div>

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="flex items-center justify-center w-8 h-8 mx-1 text-gray-600 rounded-full hover:bg-gray-100 transition-colors duration-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </a>
            @else
                <span class="flex items-center justify-center w-8 h-8 mx-1 text-gray-400 cursor-default">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </span>
            @endif
        </div>
    </nav>
@endif