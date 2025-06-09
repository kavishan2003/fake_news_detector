<?php

namespace App\Livewire;


use Livewire\Component;
use App\Models\FakenessCheck;
use Illuminate\Support\Facades\DB;

class Analytics extends Component
{


    public $pieChartLabels = [];
    public $pieChartData = [];
    public $domainAnalytics = [];
    public $chartLabels = [];
    public $chartData = [];

    public function mount()
    {
        $this->loadAnalyticsData();
    }

    public function loadAnalyticsData()
    {
        // Fetch all articles from the database
        $articles = FakenessCheck::select('url', 'score')->get();

        $domainScores = [];

        foreach ($articles as $article) {
            $host = parse_url($article->url, PHP_URL_HOST);

            // Clean up the host (e.g., remove 'www.')
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
                'average_score' => round($averageScore, 2),
                'article_count' => count($scores),
            ];


            $this->chartLabels[] = $domain;
            $this->chartData[] = round($averageScore, 2);
        }


        usort($processedAnalytics, function ($a, $b) {
            return $b['average_score'] <=> $a['average_score'];
        });

        $this->domainAnalytics = $processedAnalytics;



        $articles = FakenessCheck::select('url', 'score')->get();

        $domainScores = [];
        $domainArticleCounts = [];

        foreach ($articles as $article) {
            $host = parse_url($article->url, PHP_URL_HOST);
            $domain = str_replace('www.', '', $host);

            if ($domain) {
                if (!isset($domainScores[$domain])) {
                    $domainScores[$domain] = [];
                }
                $domainScores[$domain][] = $article->score;


                $domainArticleCounts[$domain] = ($domainArticleCounts[$domain] ?? 0) + 1;
            }
        }

        $processedAnalytics = [];
        foreach ($domainScores as $domain => $scores) {
            $averageScore = count($scores) > 0 ? array_sum($scores) / count($scores) : 0;
            $processedAnalytics[] = [
                'domain' => $domain,
                'average_score' => round($averageScore, 2),
                'article_count' => $domainArticleCounts[$domain],
            ];


            $this->chartLabels[] = $domain;
            $this->chartData[] = round($averageScore, 2);


            $this->pieChartLabels[] = $domain;
            $this->pieChartData[] = $domainArticleCounts[$domain];
        }


        usort($processedAnalytics, function ($a, $b) {
            return $b['average_score'] <=> $a['average_score'];
        });

        $this->domainAnalytics = $processedAnalytics;
    }


    public function render()
    {
        return view('livewire.analytics');
    }
}
