<?php

namespace App\Console\Commands;

use App\Livewire\Detector;
use App\Models\ScrapedArticle;
use App\Models\ScrapedArticles;
use Illuminate\Console\Command;
use App\Services\FakenessChecker;
use App\Http\Controllers\DetectorController;

class RunFakenessChecks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:run-fakeness-checks';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        $url = ScrapedArticles::where('checked', 0)->first();

        if (!$url) {
            logger('No unchecked articles found.');
            return;
        }



        $detector = new DetectorController();

        $detector->url = $url->url;

        $detector->checkFakenessCorn();

        $this->info("Checked URL: {$url->url}");

        ScrapedArticles::where('url', $url->url)->update(['checked' => 1]);

        // $detector = new Detector();
        // $detector->url = $url->url;

        // try {
        //     $detector->checkFakeness();
        //     $this->info("Checked URL: {$url->url}");
        // } catch (\Exception $e) {
        //     $this->error("Error checking fakeness: " . $e->getMessage());
        // }

        logger('done');
        // logger('Running fakeness checks...');
        // $articles = ScrapedArticles::where('checked', false)->take(10)->get(); // batch 10 at a time

        // foreach ($articles as $article) {
        //     try {
        //         // This assumes you have a service or controller method that checks fakeness by URL
        //         app(Detector::class)->checkFakeness();

        //         $article->checked = true;
        //         $article->save();
        //     } catch (\Exception $e) {
        //         $this->error("Error checking fakeness for {$article->url}: " . $e->getMessage());
        //     }
        // }

        // $this->info('Fakeness check completed for current batch.');
    }
}
