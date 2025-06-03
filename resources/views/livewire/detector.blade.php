{{-- @section('head')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endsection --}}

<div>

    {{-- <nav class="bg-white shadow-lg py-4 fixed w-full top-0 z-50">
        <div class="container mx-auto px-4 flex justify-between items-center">
            {{-- Logo/Brand (Optional) --}}


            {{-- Navigation Links
            <div>
                <ul class="flex space-x-8">
                    <li>
                        <a href="/"
                            class="text-gray-700 hover:text-blue-600 font-semibold text-lg transition-colors duration-200">
                            Home
                        </a>
                    </li>
                    <li>
                        <a wire:navigate href="{{ route('analytics') }}"
                            class="text-gray-700 hover:text-blue-600 font-semibold text-lg transition-colors duration-200">
                            Analytics
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav> --}}
    <div
        class="container mx-auto px-4 py-10 min-h-screen flex flex-col lg:flex-row items-center lg:items-start justify-center bg-gradient-to-br from-blue-50 to-indigo-100">


        {{-- Main Content --}}
        <main class="flex-1 overflow-y-auto p-4 lg:p-0 ">
            <div class="text-center mb-12 animate-fade-in-down">
                <h1 class="text-5xl font-extrabold text-blue-700  tracking-tight">Fake News Detector</h1>
                <p class="text-gray-600 mt-5 text-2xl">Discover if a news article is fake or real. Paste the article URL
                    below to check!</p>
            </div>



            <div
                class="max-w-2xl w-full mx-auto bg-white p-8 rounded-2xl shadow-xl transform transition-all duration-300 hover:scale-105">
                <form action="/checkFakeness" method="post">
                    @if (session('alert'))
                        <div id="alert-message"
                            class="bg-gray-200 border-l-4 border-red-200 text-red-500 p-4 mb-4 rounded-md flex justify-between items-center">
                            <div>{{ session('alert') }}</div>
                            <button type="button"
                                class="ml-4 text-red-700 hover:text-red-900 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-opacity-50"
                                onclick="document.getElementById('alert-message').style.display='none'">
                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    @endif

                    {{-- @if (session('alert'))
                        <script>
                            Swal.fire("SweetAlert2 is working!");
                        </script>
                    @endif --}}



                    @csrf
                    <div class="mb-6">
                        <label for="url" class="block text-sm font-semibold text-gray-700 mb-2">Article URL</label>
                        <input type="text" id="url" name="url" wire:model.defer="url"
                            placeholder="e.g., https://www.nytimes.com/2024/05/23/world/ukraine-war.html" required
                            class="w-full border border-gray-300 rounded-xl p-4 text-gray-800 focus:outline-none focus:ring-4 focus:ring-blue-300 focus:border-blue-500 transition-all duration-200">
                        @error('url')
                            <span class="text-red-600 text-sm mt-2 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-6">
                        {{-- <div class="bg-gray-50 text-gray-500 text-center border border-gray-200 rounded-xl p-5 cf-turnstile flex items-center justify-center"
                            data-sitekey="{{ config('services.turnstile.key') }}" data-theme="{{ $theme ?? 'light' }}">
                            <p class="text-sm">Please complete the captcha</p>
                        </div> --}}
                    </div>

                    <button type="submit"
                        class="w-full bg-blue-700 text-white font-bold py-4 rounded-xl hover:bg-blue-800 transition-all duration-300 transform hover:-translate-y-1 shadow-lg hover:shadow-xl flex items-center justify-center"
                        id="submitBtn">
                        <svg id="checkIcon" class="w-5 h-5 mr-2" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span id="btnText">Check Authenticity</span>
                    </button>
                </form>


                @if ($fakenessScore !== null)
                    <div id="fakeness-bar">
                        <div class="mt-8 text-center bg-gray-50 p-6 rounded-xl shadow-inner animate-fade-in">
                            <p class="text-2xl font-bold text-gray-800 mb-3">Fakeness Score: <strong
                                    class="text-blue-600">{{ $fakenessScore }}%</strong></p>
                            @php
                                $barColor = 'bg-green-500'; // green
                                $textColor = 'text-green-700';
                                $message = 'Looks credible!';
                                if ($fakenessScore >= 70) {
                                    $barColor = 'bg-red-500'; // red
                                    $textColor = 'text-red-700';
                                    $message = 'High likelihood of being fake news!';
                                } elseif ($fakenessScore >= 30) {
                                    $barColor = 'bg-yellow-500'; // yellow
                                    $textColor = 'text-yellow-700';
                                    $message = 'Exercise caution, it might be misleading.';
                                }
                            @endphp
                            <div class="w-full h-5 mt-4 bg-gray-200 rounded-full overflow-hidden shadow-md">
                                <div class="h-full rounded-full transition-all duration-700 ease-out {{ $barColor }}"
                                    style="width: {{ $fakenessScore }}%;"></div>
                            </div>

                            <p class="mt-4 text-lg font-semibold {{ $textColor }}">{{ $message }}</p>
                        </div>


                    </div>
                @endif
            </div>


            {{-- @php
                dd($fakenessScore);
            @endphp --}}
            @if ($history->count() > 0)
                {{-- @php
                    dd($history)
                @endphp --}}
                <div class="mt-20 w-full max-w-5xl mx-auto animate-fade-in-up">
                    <h2 class="text-3xl font-extrabold text-gray-800 mb-8 text-center">Previously Analyzed Articles</h2>
                    <div class="mb-6 flex justify-center">
                        {{-- <select wire:model.live="perPage"
                            class="border border-gray-300 rounded-lg p-2 text-gray-700 shadow-sm focus:ring-blue-400 focus:border-blue-400">
                            <option value="6">Show 6 per page</option>
                            <option value="9">Show 9 per page</option>
                            <option value="12">Show 12 per page</option>
                        </select> --}}
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8 ">
                        @foreach ($history as $article)
                            {{-- @php
                               $score = $article->score;
                            @endphp --}}
                            <div
                                class="bg-white rounded-2xl shadow-lg p-6 flex flex-col transform transition-transform duration-300 hover:-translate-y-2 hover:shadow-xl">

                                <img src="{{ $article->image }}" alt="Thumbnail"
                                    class="w-full h-48 object-cover rounded-xl mb-4 shadow-sm border border-gray-100">
                                <h3 class="text-xl font-bold text-gray-800 mb-2 truncate"
                                    title="{{ $article->title ?? 'No title available' }}">
                                    {{ $article->title ?? 'No title available' }}</h3>
                                <p class="text-sm text-gray-600 mb-3 line-clamp-2">{{ $article->url }}</p>
                                <div class="flex items-center justify-between mb-4">
                                    <span class="text-md font-semibold text-gray-700">Fake Score: <span
                                            class="font-extrabold text-lg {{ $article->score >= 70 ? 'text-red-600' : ($article->score >= 30 ? 'text-yellow-600' : 'text-green-600') }}">{{ $article->score }}%</span></span>
                                </div>

                                <p class="text-sm text-gray-700 mb-4 line-clamp-3">
                                    {{ Str::limit($article->explanation, 150) }}</p>
                                <div class="mt-auto">
                                    <a href="{{ route('fakeness.detail', ['slug' => $article->slug]) }}"
                                        target="_blank"
                                        class="inline-block bg-blue-600 text-white font-semibold py-3 px-5 rounded-lg hover:bg-blue-700 transition-colors duration-300 text-sm shadow-md hover:shadow-lg">
                                        View Details
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @if (method_exists($history, 'links') && $history->total() > $history->perPage())
                        <div class="mt-8 flex justify-center">
                            <div class="bg-white p-4 rounded-lg shadow-md">
                                {{ $history->links('pagination::tailwind') }}
                            </div>
                        </div>
                    @endif
                </div>
            @else
                <div
                    class="flex flex-col items-center justify-center text-center text-gray-500 mt-20 w-full max-w-5xl mx-auto animate-fade-in-up">
                    <h3 class="text-xl font-bold mb-2">No Articles Found</h3>
                    <p class="text-md text-gray-600">You haven't analyzed any articles yet. Start by pasting a URL
                        above!</p>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-32 w-32 mb-6 text-gray-400" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M9 13h6m2 9H7a2 2 0 01-2-2V7h14v13a2 2 0 01-2 2zM10 3h4a1 1 0 011 1v1H9V4a1 1 0 011-1z" />
                    </svg>
                </div>

            @endif

        </main>
    </div>
</div>

@script
    <script>
        // Livewire.on('fakeness-check-complete', () => {
        //     console.log('Fakeness check completed. UI will update.');
        //     setTimeout(() => {
        //         window.location.reload(); // Reloads the entire page
        //     }, 6000); // 8000 milliseconds = 8 seconds
        // });
        // button

        document.getElementById('submitBtn').addEventListener('click', function() {
            document.getElementById('checkIcon').style.display = 'none';
            document.getElementById('btnText').innerHTML = `
            <svg class="animate-spin mx-auto h-5 w-5 text-white flex items-center text-center"
                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor"
                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span class="ml-2">Analyzing...</span>
        `;
        });,
    </script>
@endscript
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const alertBox = document.getElementById("fakeness-bar");
        if (alertBox) {
            setTimeout(() => {
                alertBox.classList.add("opacity-0", "transition-opacity", "duration-1000");
                setTimeout(() => {
                    alertBox.style.display = "none";
                }, 1000); // wait for fade-out transition to finish
            }, 8000);
        }
    });
</script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const alertBox = document.getElementById("alert-message");
        if (alertBox) {
            setTimeout(() => {
                alertBox.classList.add("opacity-0", "transition-opacity", "duration-700");
                setTimeout(() => {
                    alertBox.style.display = "none";
                }, 700); // wait for the fade-out transition
            }, 4000); // 5 seconds delay
        }
    });
</script>
