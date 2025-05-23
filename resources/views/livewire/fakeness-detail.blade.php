<div class="max-w-3xl mx-auto p-6">
    <h1 class="text-3xl font-bold mb-4">{{ $article->title }}</h1>
    <img src="{{ $article->image }}" alt="Thumbnail" class="mb-4 w-full h-auto rounded-lg shadow">

    <p class="text-gray-600 mb-2"><strong>Original URL:</strong>
        <a href="{{ $article->url }}" target="_blank" class="text-blue-600 underline">
            {{ $article->url }}
        </a>
    </p>

    <p class="text-lg font-semibold mb-4">Fakeness Score: {{ $article->score }}%</p>

    <h2 class="text-xl font-bold mb-2">Explanation</h2>
    <p class="text-gray-800 mb-6">{{ $article->explanation }}</p>

    <a href="{{ $article->url }}" target="_blank"
        class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
        View Full Article
    </a>

    <!-- Facebook Like + Share -->
    <div class="mt-6">
        <div class="fb-like" data-href="{{ url()->current() }}" data-width="" data-layout="button_count"
            data-action="like" data-size="large" data-share="true"></div>
    </div>

    <!-- Facebook Comments -->
    <div class="mt-6">
        <div class="fb-comments" data-href="{{ url()->current() }}" data-width="100%" data-numposts="5"></div>
    </div>

    <!-- Facebook SDK -->
    <script async defer crossorigin="anonymous" src="https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v19.0"
        nonce="{{ \Illuminate\Support\Str::random(10) }}"></script>
</div>
