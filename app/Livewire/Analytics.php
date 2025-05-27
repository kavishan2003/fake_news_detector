<?php

namespace App\Livewire;


use Livewire\Component;
use App\Models\FakenessCheck; // Assuming you have an Article model
use Illuminate\Support\Facades\DB; // For database operations

class Analytics extends Component
{





    public $domainAnalytics = []; // Stores the processed data for domains
    public $chartLabels = [];    // Labels for the chart (domains)
    public $chartData = [];      // Data for the chart (average fakeness scores)

    public function mount()
    {
        $this->loadAnalyticsData();
    }

    public function loadAnalyticsData()
    {
        // Fetch all articles from the database
        // You might want to add pagination or limit results for very large datasets
        $articles = FakenessCheck::select('url', 'score')->get();

        $domainScores = []; // Temporarily store all scores for each domain

        foreach ($articles as $article) {
            $host = parse_url($article->url, PHP_URL_HOST);

            // Clean up the host (e.g., remove 'www.') for better grouping
            $domain = str_replace('www.', '', $host);

            if ($domain) {
                if (!isset($domainScores[$domain])) {
                    $domainScores[$domain] = [];
                }
                $domainScores[$domain][] = $article->score;
            }
        }

        // Calculate average fakeness score for each domain
        $processedAnalytics = [];
        foreach ($domainScores as $domain => $scores) {
            $averageScore = count($scores) > 0 ? array_sum($scores) / count($scores) : 0;
            $processedAnalytics[] = [
                'domain' => $domain,
                'average_score' => round($averageScore, 2), // Round to 2 decimal places
                'article_count' => count($scores),
            ];

            // Prepare data for Chart.js
            $this->chartLabels[] = $domain;
            $this->chartData[] = round($averageScore, 2);
        }

        // Sort by average score (optional, but good for display)
        usort($processedAnalytics, function($a, $b) {
            return $b['average_score'] <=> $a['average_score']; // Sort descending
        });

        $this->domainAnalytics = $processedAnalytics;
    }

  
    public function render()
    {
        return view('livewire.analytics');
    }
}
