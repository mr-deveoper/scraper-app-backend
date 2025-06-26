<?php

namespace App\Jobs;

use App\Interfaces\ProductRepositoryInterface;
use App\Services\ScraperService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Scrape Category Job
 * 
 * Queue job for scraping all products from a category page.
 * This job processes category pages to extract product URLs
 * and then scrapes each individual product.
 * 
 * Features:
 * - Category page processing
 * - Individual product scraping
 * - Error handling and logging
 * - Queue-based processing for better performance
 * 
 * @see ScraperService
 * @see ProductRepositoryInterface
 */
class ScrapeCategoryJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     * 
     * @var int
     */
    public $tries = 3;

    /**
     * The number of seconds the job can run before timing out.
     * 
     * @var int
     */
    public $timeout = 300; // 5 minutes

    /**
     * Create a new job instance.
     * 
     * @param string $url The category URL to scrape
     */
    public function __construct(public string $url) {}

    /**
     * Execute the job.
     * 
     * This method handles the main logic of the job:
     * 1. Gets the appropriate scraper for the category URL
     * 2. Scrapes all product URLs from the category page
     * 3. Iterates through each product URL and scrapes individual products
     * 4. Stores each product in the database
     * 
     * @param ProductRepositoryInterface $repo The product repository
     * @return void
     */
    public function handle(ProductRepositoryInterface $repo): void
    {
        try {
            Log::info('Starting category scraping job', ['url' => $this->url]);

            // Get the scraper service from the container
            $scraperService = app('scraper.service');

            // Get the appropriate scraper for this URL
            $scraper = $scraperService->getScraper($this->url);

            Log::info('Using scraper for category', [
                'url' => $this->url,
                'scraper_class' => get_class($scraper)
            ]);

            // Scrape all product URLs from the category page
            $productUrls = $scraper->scrapeCategory($this->url);

            Log::info('Found product URLs in category', [
                'url' => $this->url,
                'product_count' => count($productUrls)
            ]);

            $successCount = 0;
            $errorCount = 0;

            // Process each product URL
            foreach ($productUrls as $productUrl) {
                try {
                    // Scrape individual product data
                    $productData = $scraper->scrapeProduct($productUrl);

                    // Store the product in the database
                    $repo->storeOrUpdate($productData);

                    $successCount++;

                    Log::info('Product scraped successfully', [
                        'product_url' => $productUrl,
                        'external_id' => $productData['external_id'] ?? 'unknown'
                    ]);

                } catch (\Exception $e) {
                    $errorCount++;

                    Log::warning("Failed to scrape product: {$productUrl}", [
                        'error' => $e->getMessage(),
                        'product_url' => $productUrl
                    ]);

                    // Continue with next product instead of failing the entire job
                    continue;
                }
            }

            Log::info('Category scraping job completed', [
                'url' => $this->url,
                'total_products' => count($productUrls),
                'successful' => $successCount,
                'failed' => $errorCount
            ]);

        } catch (\Exception $e) {
            Log::error('Category scraping job failed', [
                'url' => $this->url,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Re-throw the exception to mark the job as failed
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     * 
     * Called when the job fails after all retry attempts.
     * 
     * @param \Throwable $exception The exception that caused the failure
     * @return void
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Category scraping job failed permanently', [
            'url' => $this->url,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString()
        ]);
    }
}

