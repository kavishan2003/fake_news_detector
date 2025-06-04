<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Livewire\Component;
use Illuminate\Log\Logger;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Livewire\WithPagination;
use App\Models\FakenessCheck;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redirect;
use Symfony\Component\DomCrawler\Crawler;

class DetectorController extends Controller
{
    public string $url = '';
    public ?int $fakenessScore = null;
    public string $error = '';
    public $perPage = 12;
    public ?string $ogTitle = null;
    public ?string $ogImage = null;
    public ?string $explanation = null;
    public $ogDescription;
    public $Title;
    public $ogLogo;
    public $ogName;



    public function fetchOGMeta($url)
    {
        try {

            $client = new Client();
            $response = $client->get($url);
            $html = $response->getBody()->getContents();
            $crawler = new Crawler($html);

            $defaultImage = asset('images/newspaper.jpg');

            $ogTitle = $crawler->filterXPath("//meta[@property='og:title']")->count() ?
                $crawler->filterXPath("//meta[@property='og:title']")->attr('content') : null;

            $ogImage = $crawler->filterXPath("//meta[@property='og:image']")->count()
                ? $crawler->filterXPath("//meta[@property='og:image']")->attr('content')
                : $defaultImage;
            $ogDescription = $crawler->filterXPath("//meta[@property='og:description']")->attr('content') ?? null;

            if (!$ogTitle && $crawler->filter('title')->count()) {
                $ogTitle = $crawler->filter('title')->text();
            }
            // extractig the logo

            $ogLogo = null;
            $favicon = null;

            if ($crawler->filterXPath("//meta[@property='og:logo']")->count()) {
                $ogLogo = $crawler->filterXPath("//meta[@property='og:logo']")->attr('content');
            } elseif ($crawler->filterXPath("//meta[@name='logo']")->count()) {
                $ogLogo = $crawler->filterXPath("//meta[@name='logo']")->attr('content');
            }

            if ($crawler->filterXPath("//link[@rel='icon']")->count()) {
                $favicon = $crawler->filterXPath("//link[@rel='icon']")->attr('href');
            } elseif ($crawler->filterXPath("//link[@rel='shortcut icon']")->count()) {
                $favicon = $crawler->filterXPath("//link[@rel='shortcut icon']")->attr('href');
            }

            $base = new \GuzzleHttp\Psr7\Uri($url);
            if ($favicon) {
                $favicon = \GuzzleHttp\Psr7\UriResolver::resolve($base, new \GuzzleHttp\Psr7\Uri($favicon))->__toString();
            }


            //extract site name

            $siteName = null;

            if ($crawler->filterXPath("//meta[@property='og:site_name']")->count()) {
                $siteName = $crawler->filterXPath("//meta[@property='og:site_name']")->attr('content');
            }

            if (!$siteName && $crawler->filter('title')->count()) {
                $siteName = $crawler->filter('title')->text();
                $siteName = preg_replace('/\|.*$| - .*$/', '', $siteName);
                $siteName = trim($siteName);
            }

            if (!$siteName) {
                $parsedUrl = parse_url($url);
                $host = $parsedUrl['host'] ?? 'unknown';
                $siteName = ucfirst(str_replace('www.', '', $host));
            }

            


            return [
                'title' => $ogTitle,
                'image' => $ogImage,
                'description' => $ogDescription,
                'logo' => $ogLogo ?? $favicon ?? null,
                'name'=> $siteName,
            ];
        } catch (\Exception $e) {

            return [
                'title' => null,
                'image' => asset('images/newspaper.jpg'),
                'description' => null
            ];
        }
    }

    public function checkFakeness(Request $request)
    {

        $userUrl = $request->input('url');

        $this->fakenessScore = null;


        if (!filter_var($userUrl, FILTER_VALIDATE_URL)) {

            return;
        }

        $cacheKey = 'fakeness_score_' . md5($userUrl);

        if (Cache::has($cacheKey)) {

            return redirect()->back()->with('alert', 'This URL has already been checked before.');
        }


        try {
            // 1. Fetch OG metadata first
            $ogData = $this->fetchOGMeta($userUrl);
            $title = $ogData['title'] ?? 'Unknown Article'; // fallback
            $image = $ogData['image'] ?? asset('images/newspaper.jpg');
            $description = $ogData['description'] ?? null;
            $Logo = $ogData['logo'] ?? asset('images/newspaper.jpg');
            $Name = $ogData['name'] ??'No name';


            $this->ogTitle = $title;
            $this->ogImage = $image;
            $this->ogDescription = $description;
            $this->ogLogo = $Logo;
            $this->ogName = $Name;


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
            
            URL: {$userUrl}
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
            logger($response);

            $answer = $response->json('choices.0.message.content') ?? '';
            preg_match('/\d{1,3}/', $answer, $matches);
            $score = isset($matches[0]) ? min((int)$matches[0], 100) : null;


            if ($score === null) {
                $this->error = 'Unable to extract a score from the AI response. Raw response: ' . $answer;
                return 1;
            }

            $this->fakenessScore = $score;

            // 5. Fetch explanation from OpenAI
            $prompt = <<<EOD
            Based on the following news article metadata and URL, give me a brief explanation about this news article and why it is considered fake news.
            If it's not considered fake, then show why it's not fake. (around 500 words)
            
            URL: {$userUrl}
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

            // dump($score);
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
                'url' => $userUrl,
                'score' => $score,
                'title' => $title,
                'image' => $image,
                'explanation' => $explanation,
                'slug' => $slug,
                'logo' => $Logo,
                'name' => $Name,

            ]);


            return view('welcome', [
                'fakenessScore' => $score,

            ]);
        } catch (\Exception $e) {
            $this->error = 'Failed to process check: ' . $e->getMessage();
        }
    }
}
