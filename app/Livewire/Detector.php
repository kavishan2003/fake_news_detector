<?php

namespace App\Livewire;

use GuzzleHttp\Client;
use Livewire\Component;
use Illuminate\Log\Logger;
use Illuminate\Support\Str;
use Livewire\WithPagination;
use App\Models\FakenessCheck;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\DomCrawler\Crawler;

class Detector extends Component
{
    use WithPagination;
    protected $paginationTheme = 'tailwind';

    public string $url = '';
    public ?int $fakenessScore = null;
    public string $error = '';
    // public $history;
    public $perPage = 12;
    public ?string $ogTitle = null;
    public ?string $ogImage = null;
    public ?string $explanation = null;
    public $ogDescription;
    public $Title;

    protected $listeners = ['resetFakenessScore'];

    public function fetchOGMeta($url)
    {
        try {
            $client = new Client();
            $response = $client->get($url);
            $html = $response->getBody()->getContents();
            $crawler = new Crawler($html);

            $ogTitle = $crawler->filterXPath("//meta[@property='og:title']")->attr('content') ?? null;
            $ogImage = $crawler->filterXPath("//meta[@property='og:image']")->attr('content') ?? null;
            $ogDescription = $crawler->filterXPath("//meta[@property='og:description']")->attr('content') ?? null;

            return [
                'title' => $ogTitle,
                'image' => $ogImage,
                'description' => $ogDescription
            ];
        } catch (\Exception $e) {
            // Log the error or set a specific error message for meta fetching
            // Log::error("Failed to fetch OG meta for URL: {$url} - " . $e->getMessage());
            return [
                'title' => null,
                'image' => null,
                'description' => null
            ];
        }
    }

    public function fetchExplanation($url, $score, $Title, $ogDescription)
    {
        $prompt = "Explain in up to 500 words why the article at this URL is " . ($score < 50 ? 'fake' : 'real') . ": $url. Consider the title: '{$Title}' and description: '{$ogDescription}'.";

        try {
            $response = Http::withToken(env('OPENAI_API_KEY'))->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-4o-mini',
                'messages' => [
                    ['role' => 'system', 'content' => 'You are a fact-checking assistant. Provide concise and clear explanations.'],
                    ['role' => 'user', 'content' => $prompt]
                ],
                'max_tokens' => 1000,
            ]);

            // Check for API errors or empty responses
            if ($response->successful()) {
                return $response->json('choices.0.message.content') ?? 'No explanation provided.';
            } else {
                // \Log::error("OpenAI API error for explanation: " . $response->body());
                return 'Failed to get an explanation from the AI service.';
            }
        } catch (\Exception $e) {
            // \Log::error("Exception when fetching explanation from OpenAI: " . $e->getMessage());
            return 'An error occurred while generating the explanation.';
        }
    }

    public function checkFakeness()
    {
        $this->reset(['fakenessScore', 'error', 'explanation', 'ogTitle', 'ogImage', 'ogDescription']);
        $this->fakenessScore = null;

        if (!filter_var($this->url, FILTER_VALIDATE_URL)) {
            $this->addError('url', 'Please enter a valid URL.');
            return;
        }

        $cacheKey = 'fakeness_score_' . md5($this->url);

        if (Cache::has($cacheKey)) {
            $cachedData = Cache::get($cacheKey);
            $this->fakenessScore = $cachedData['score'];
            $this->ogTitle = $cachedData['title'];
            $this->ogImage = $cachedData['image'];
            $this->explanation = $cachedData['explanation'];
            $this->ogDescription = $cachedData['description'];
            // Since we're loading from cache, ensure the UI updates.
            // This might still trigger a refreshWindow if needed for other elements.
            // $this->dispatchBrowserEvent('fakeness-score-shown');
            return;
        }

        try {
            // 1. Fetch OG metadata first
            $ogData = $this->fetchOGMeta($this->url);
            $title = $ogData['title'] ?? 'Unknown Article'; // fallback
            $image = $ogData['image'] ?? null;
            $description = $ogData['description'] ?? null;

            $this->ogTitle = $title;
            $this->ogImage = $image;
            $this->ogDescription = $description;

            // 2. Generate slug base and slug with timestamp
            $slugBase = Str::slug($title);
            $timestamp = now()->format('YmdHis');
            $slug = $slugBase . '-' . $timestamp;

            // 3. Ensure slug uniqueness
            while (FakenessCheck::where('slug', $slug)->exists()) {
                $timestamp = now()->addSecond()->format('YmdHis');
                $slug = $slugBase . '-' . $timestamp;
            }

            // 4. Prepare prompt and call OpenAI for fakeness score
            $prompt = <<<EOD
                Based on the following news article metadata and URL, determine how fake this news article is.
                Give me a percentage score from 0 to 100%, where 100% is completely fake and 0% is completely true and factual.
                Just give me a percentage number only, no other text at all. Respond with only a number and no text.

                URL: {$this->url}
                Title: {$this->ogTitle}
                Description: {$this->ogDescription}
                EOD;

            $response = Http::withToken(config('services.openai.key'))
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => 'gpt-4o-mini',
                    'messages' => [
                        ['role' => 'user', 'content' => $prompt],
                    ],
                    'max_tokens' => 10,
                ]);

            $answer = $response->json('choices.0.message.content') ?? '';
            preg_match('/\d{1,3}/', $answer, $matches);
            $score = isset($matches[0]) ? min((int)$matches[0], 100) : null;
            // $score = 80;

            if ($score === null) {
                $this->error = 'Unable to extract a score from the AI response. Raw response: ' . $answer;
                // \Log::warning("AI did not return a score for URL: {$this->url}. Raw response: {$answer}");
                return;
            }

            $this->fakenessScore = $score;

            // 5. Fetch explanation from OpenAI
            $prompt = <<<EOD
                Based on the following news article metadata and URL, give me a brief explanation about this news article and why it is considered fake news. If it's not considered fake, then show why it's not fake. (around 500 words)

                URL: {$this->url}
                Title: {$this->ogTitle}
                Description: {$this->ogDescription}
                EOD;

            $response = Http::withToken(config('services.openai.key'))
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => 'gpt-4o-mini',
                    'messages' => [
                        ['role' => 'user', 'content' => $prompt],
                    ],
                    'max_tokens' => 1000,
                ]);

            $answer = $response->json('choices.0.message.content') ?? '';

            $explanation = $answer;

            $this->explanation = $explanation;

            // Cache the score and other relevant data for 24 hours
            Cache::put($cacheKey, [
                'score' => $score,
                'title' => $title,
                'image' => $image,
                'description' => $description,
                'explanation' => $explanation,
            ], now()->addHours(24));

            // 6. Save all data to DB
            FakenessCheck::create([
                'url' => $this->url,
                'score' => $score,
                'title' => $title,
                'image' => $image,
                'explanation' => $explanation,
                'slug' => $slug,
            ]);

            // $this->refreshHistory();
            $this->url = '';
            $this->dispatch('fakeness-check-complete');

            // $this->dispatch('fakeness-check-complete'); // Changed event name for clarity

            // Dispatch browser event after a successful check and data saving
            // $this->dispatchBrowserEvent('fakeness-check-complete');

        } catch (\Exception $e) {
            $this->error = 'Failed to process check: ' . $e->getMessage();
            // \Log::error("Error in checkFakeness for URL: {$this->url} - " . $e->getMessage());
        }
    }

    public function mount()
    {
        // $this->refreshHistory();
    }



    public static function generateSlug($title)
    {
        $timestamp = now()->format('YmdHis');
        return Str::slug($title) . '-' . $timestamp;
    }

    // delete function
    public function deleteARC($id)
    {
        $delete = FakenessCheck::findOrFail($id);
        $delete->delete();
        session()->flash('message', 'Article deleted successfully.');
    }



    public function render()
    {
        // $history = FakenessCheck::select('url')->latest()->paginate(5);
        return view('livewire.detector', [
            // 'history' => $this->history,
            'history' => FakenessCheck::latest()->paginate($this->perPage),
            // 'recentArticles' => $this->getRecentArticles()
        ]);
    }

    public function rendered() {}

    public function resetFakenessScore()
    {
        $this->reset(['fakenessScore', 'error', 'explanation', 'ogTitle', 'ogImage', 'ogDescription']);
    }
}
