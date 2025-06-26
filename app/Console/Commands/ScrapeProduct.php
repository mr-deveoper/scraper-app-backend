<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Interfaces\ProductRepositoryInterface;
use App\Services\ScraperService;

/**
 * Scrape Product Command
 * 
 * Artisan command to scrape a single product from a given URL.
 * This command uses the scraper service to extract product data
 * and stores it in the database using the product repository.
 * 
 * Usage: php artisan scrape:product {url}
 * 
 * @see ScraperService
 * @see ProductRepositoryInterface
 */
class ScrapeProduct extends Command
{
    /**
     * The name and signature of the console command.
     * 
     * @var string
     */
    protected $signature = 'scrape:product {url}';

    /**
     * The console command description.
     * 
     * @var string
     */
    protected $description = 'Scrape a single product from the given URL';

    /**
     * Execute the console command.
     * 
     * This method handles the main logic of the command:
     * 1. Validates the provided URL
     * 2. Gets the appropriate scraper for the URL
     * 3. Scrapes the product data
     * 4. Stores the data in the database
     * 
     * @param ProductRepositoryInterface $repo The product repository
     * @return int Command exit code (0 for success, 1 for failure)
     */
    public function handle(ProductRepositoryInterface $repo): int
    {
        try {
            // Get the URL from command arguments
            $url = $this->argument('url');

            // Validate the URL
            if (empty($url) || !filter_var($url, FILTER_VALIDATE_URL)) {
                $this->error("Invalid URL provided: {$url}");
                return 1;
            }

            $this->info("Starting to scrape product from: {$url}");

            // Get the scraper service from the container
            $scraperService = app('scraper.service');

            // Get the appropriate scraper for this URL
            $scraper = $scraperService->getScraper($url);

            $this->info("Using scraper: " . get_class($scraper));

            // Scrape the product data
            $this->info("Scraping product data...");
            $productData = $scraper->scrapeProduct($url);

            $this->info("Product data scraped successfully:");
            $this->line("- Title: " . $productData['title']);
            $this->line("- Price: " . $productData['price']);
            $this->line("- External ID: " . $productData['external_id']);

            // Store the product data in the database
            $this->info("Storing product in database...");
            $product = $repo->storeOrUpdate($productData);

            $this->info("Product scraped and saved successfully!");
            $this->info("Product ID: " . $product->id);

            return 0;

        } catch (\InvalidArgumentException $e) {
            $this->error("Invalid argument: " . $e->getMessage());
            return 1;

        } catch (\Exception $e) {
            $this->error("Failed to scrape product: " . $e->getMessage());
            
            // Log the error for debugging
            \Illuminate\Support\Facades\Log::error('ScrapeProduct command failed', [
                'url' => $url ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return 1;
        }
    }
}
