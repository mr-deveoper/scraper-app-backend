<?php

namespace App\Services\Scrapers;

use Symfony\Component\DomCrawler\Crawler;
use Illuminate\Support\Facades\Http;
use App\Helpers\ProxyHelper;

/**
 * Jumia Scraper
 * 
 * Handles scraping of product data from Jumia.com.
 * This scraper implements the Strategy pattern and provides
 * methods to extract product information from Jumia product pages.
 * 
 * Features:
 * - Product data extraction (title, price, image)
 * - Category page parsing for product URLs
 * - Proxy support for avoiding rate limiting
 * - Error handling for blocked requests
 * 
 * @see ScraperInterface
 */
class JumiaScraper implements ScraperInterface
{
    /**
     * Check if this scraper supports the given URL.
     * 
     * Determines if the URL is from Jumia.com and can be scraped.
     * 
     * @param string $url The URL to check
     * @return bool True if this scraper can handle the URL
     */
    public function supports(string $url): bool
    {
        return str_contains($url, 'jumia.');
    }

    /**
     * Scrape a single product from the given URL.
     * 
     * Extracts product title, price, and image URL from a Jumia product page.
     * 
     * @param string $url The product URL to scrape
     * @return array The scraped product data
     * @throws \Exception When product data cannot be found or scraping fails
     */
    public function scrapeProduct(string $url): array
    {
        // Fetch and parse the product page
        $crawler = $this->fetch($url);

        // Extract product information using CSS selectors
        $titleNode = $crawler->filter('h1.-fs20.-pts.-pbxs');
        $priceNode = $crawler->filter('span.-b.-ltr.-tal.-fs24');
        $imageNode = $crawler->filter('img.-fw.-fh');

        // Validate that all required elements are found
        if ($titleNode->count() === 0 || $priceNode->count() === 0 || $imageNode->count() === 0) {
            throw new \Exception('Product data not found. Jumia may have changed layout or blocked the request.');
        }

        // Return standardized product data
        return [
            'external_id' => $this->extractId($url),
            'title' => trim($titleNode->text()),
            'price' => trim($priceNode->text()),
            'image_url' => $imageNode->first()->attr('src'),
        ];
    }

    /**
     * Scrape all product URLs from a category page.
     * 
     * Extracts product links from Jumia category pages.
     * 
     * @param string $url The category URL to scrape
     * @return array Array of product URLs found on the page
     */
    public function scrapeCategory(string $url): array
    {
        // Fetch and parse the category page
        $crawler = $this->fetch($url);

        // Extract product URLs from category listings
        return array_filter($crawler->filter('a.core')->each(function (Crawler $node) {
            $href = $node->attr('href');
            
            // Handle both absolute and relative URLs
            if (str_starts_with($href, 'http')) {
                return $href;
            } else {
                return 'https://www.jumia.com.eg' . $href;
            }
        }));
    }

    /**
     * Fetch and parse a web page using HTTP client.
     * 
     * Handles HTTP requests with proper headers, proxy support, and error handling.
     * 
     * @param string $url The URL to fetch
     * @return Crawler The parsed DOM crawler
     * @throws \Exception When the HTTP request fails
     */
    private function fetch(string $url): Crawler
    {
        // Get a random proxy to avoid rate limiting
        $proxy = ProxyHelper::getRandom();

        // Configure HTTP client with browser-like headers
        $client = Http::withHeaders([
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
            'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
            'Accept-Language' => 'en-US,en;q=0.5',
            'Connection' => 'keep-alive',
        ])->timeout(15)->withOptions([
            'verify' => false,
            'proxy' => $proxy,
        ]);

        // Make the HTTP request
        $response = $client->get($url);

        // Check if the request was successful
        if (!$response->successful()) {
            throw new \Exception("Failed to fetch URL: {$url}. Status: {$response->status()}");
        }

        // Return parsed DOM crawler
        return new Crawler($response->body());
    }

    /**
     * Extract product ID from Jumia URL.
     * 
     * Jumia product URLs contain a unique identifier in the format /PRODUCT_ID.html.
     * This method extracts that ID or generates a fallback hash.
     * 
     * @param string $url The Jumia product URL
     * @return string The extracted product ID or URL hash
     */
    private function extractId(string $url): string
    {
        // Extract 8-character ID from Jumia URLs like /MLA12345678.html
        preg_match('/\/([a-z0-9]{8})\.html/', $url, $matches);
        
        // Return extracted ID or fallback to URL hash
        return $matches[1] ?? md5($url);
    }
}
