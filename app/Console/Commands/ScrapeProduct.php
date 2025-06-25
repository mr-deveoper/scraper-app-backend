<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ScraperService;

class ScrapeProduct extends Command
{
    protected $signature = 'scrape:product {url}';
    protected $description = 'Scrape and store product data';
    protected ScraperService $scraper;

    public function __construct(ScraperService $scraper)
    {
        parent::__construct();
        $this->scraper = $scraper;
    }

    public function handle()
    {
        $url = $this->argument('url');
        $this->scraper->scrape($url);
        $this->info("Product scraped and stored.");
    }
}
