<?php

namespace App\Console\Commands;

use App\Models\ScrapedArticles;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class FetchRssFeeds extends Command
{
    protected $signature = 'rss:fetch';
    protected $description = 'Fetch RSS feeds and save article URLs to database';

    protected $feeds = [
        'https://www.cnbc.com/id/100727362/device/rss/rss.html',
        'https://abcnews.go.com/abcnews/internationalheadlines',
        'https://feeds.nbcnews.com/nbcnews/public/news',
        'https://www.aljazeera.com/xml/rss/all.xml',
        'https://rss.nytimes.com/services/xml/rss/nyt/World.xml', // <-- corrected
        // 'https://feeds.washingtonpost.com/rss/world',
    ];


    public function handle()
    {
        logger('Fetching RSS feeds...');

        foreach ($this->feeds as $feedUrl) {
            try {
                $response = Http::get($feedUrl);

                if (!$response->ok()) {
                    throw new \Exception("Failed to fetch RSS: $feedUrl");
                }

                logger("Processing: $feedUrl");
                $xml = @simplexml_load_string($response->body());

                // Example: print all <item><link> elements
                foreach ($xml->channel->item as $item) {
                    // logger("Found link: " . $item->link);
                    $url = (string) $item->link;

                    $old_url = ScrapedArticles::where('url', $url)->first();

                    if ($url == isset($old_url)) {
                        continue;
                    } else {

                        ScrapedArticles::firstOrCreate(
                            ['url' => $url],
                            ['source' => parse_url($url, PHP_URL_HOST)] // Use only the host part
                        );
                    }
                }
            } catch (\Exception $e) {
                $this->error("Error processing feed: $feedUrl - " . $e->getMessage());
            }
        }

        $this->info('RSS feeds processed successfully.');

        // $response = Http::get($feedUrl);

        // if (!$response->ok()) {
        //     throw new \Exception("Failed to fetch RSS: $feedUrl");
        // }


        // logger($response->body());
        // $xml = @simplexml_load_string($response->body());


        // if (!$xml || !isset($xml->channel->item)) {
        //     throw new \Exception("Invalid or empty RSS feed content.");
        // }

        // foreach ($xml->channel->item as $item) {
        //     $rawText = '';

        //     foreach ($item->children() as $child) {
        //         $rawText .= strip_tags((string) $child) . ' ';
        //     }

        //     // Extract http(s) links
        //     preg_match_all('/https?:\/\/[^\s<>"]+/', $rawText, $matches);

        //     foreach ($matches[0] as $url) {
        //         $url = rtrim($url, '.),');

        //         if (strlen($url) > 10) {
        //             ScrapedArticles::firstOrCreate(
        //                 ['url' => $url],
        //                 ['source' => parse_url($url, PHP_URL_HOST)] // Use only the host part
        //             );
        //         }
        //     }
        // }
        // logger('RSS feeds processed successfully.');
    }
}
